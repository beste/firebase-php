<?php

declare(strict_types=1);

namespace Kreait\Firebase;

use Beste\Cache\InMemoryCache;
use Beste\Clock\SystemClock;
use Beste\Clock\WrappingClock;
use Firebase\JWT\CachedKeySet;
use Google\Auth\ApplicationDefaultCredentials;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\FetchAuthTokenCache;
use Google\Auth\FetchAuthTokenInterface;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Google\Auth\Middleware\AuthTokenMiddleware;
use Google\Auth\ProjectIdProviderInterface;
use Google\Auth\SignBlobInterface;
use Google\Cloud\Storage\StorageClient;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Utils as GuzzleUtils;
use Kreait\Firebase\AppCheck\AppCheckTokenGenerator;
use Kreait\Firebase\AppCheck\AppCheckTokenVerifier;
use Kreait\Firebase\Auth\ApiClient;
use Kreait\Firebase\Auth\CustomTokenViaGoogleCredentials;
use Kreait\Firebase\Auth\SignIn\GuzzleHandler;
use Kreait\Firebase\Database\UrlBuilder;
use Kreait\Firebase\Exception\AppCheckApiExceptionConverter;
use Kreait\Firebase\Exception\AuthApiExceptionConverter;
use Kreait\Firebase\Exception\DatabaseApiExceptionConverter;
use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Exception\MessagingApiExceptionConverter;
use Kreait\Firebase\Exception\RemoteConfigApiExceptionConverter;
use Kreait\Firebase\Exception\RuntimeException;
use Kreait\Firebase\Http\ErrorResponseParser;
use Kreait\Firebase\Http\HttpClientOptions;
use Kreait\Firebase\Http\Middleware;
use Kreait\Firebase\JWT\IdTokenVerifier;
use Kreait\Firebase\JWT\SessionCookieVerifier;
use Kreait\Firebase\Messaging\AppInstanceApiClient;
use Kreait\Firebase\Messaging\RequestFactory;
use Kreait\Firebase\Valinor\Mapper;
use Kreait\Firebase\Valinor\Normalizer;
use Kreait\Firebase\Valinor\Source;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Clock\ClockInterface;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;
use Throwable;

use function array_filter;
use function is_string;
use function sprintf;
use function trim;

final class Factory
{
    public const API_CLIENT_SCOPES = [
        'https://www.googleapis.com/auth/iam',
        'https://www.googleapis.com/auth/cloud-platform',
        'https://www.googleapis.com/auth/firebase',
        'https://www.googleapis.com/auth/firebase.database',
        'https://www.googleapis.com/auth/firebase.messaging',
        'https://www.googleapis.com/auth/firebase.remoteconfig',
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/securetoken',
    ];

    /**
     * @var non-empty-string|null
     */
    private ?string $databaseUrl = null;

    /**
     * @var non-empty-string|null
     */
    private ?string $defaultStorageBucket = null;

    private ?ServiceAccount $serviceAccount = null;

    private ?FetchAuthTokenInterface $googleAuthTokenCredentials = null;

    /**
     * @var non-empty-string|null
     */
    private ?string $projectId = null;

    private CacheItemPoolInterface $defaultCache;

    private ?CacheItemPoolInterface $verifierCache = null;

    private ?CacheItemPoolInterface $authTokenCache = null;

    private ?CacheItemPoolInterface $keySetCache = null;

    private ClockInterface $clock;

    /**
     * @var callable|null
     */
    private $httpLogMiddleware;

    /**
     * @var callable|null
     */
    private $httpDebugLogMiddleware;

    /**
     * @var callable|null
     */
    private $databaseAuthVariableOverrideMiddleware;

    /**
     * @var non-empty-string|null
     */
    private ?string $tenantId = null;

    private HttpFactory $httpFactory;

    private HttpClientOptions $httpClientOptions;

    private ErrorResponseParser $errorResponseParser;

    /**
     * @var array<non-empty-string, mixed>
     */
    private array $firestoreClientConfig = [];

    private mixed $mapperCache = null;

    private mixed $normalizerCache = null;

    private ?Mapper $mapper = null;

    private ?Normalizer $normalizer = null;

    public function __construct()
    {
        $this->clock = SystemClock::create();

        $this->defaultCache = new InMemoryCache($this->clock);
        $this->httpFactory = new HttpFactory();
        $this->httpClientOptions = HttpClientOptions::default();
        $this->errorResponseParser = new ErrorResponseParser();
    }

    /**
     * @param string|array<mixed> $value
     *
     * @throws InvalidArgumentException
     */
    public function withServiceAccount(string|array $value): self
    {
        $serviceAccount = $this->mapServiceAccount($value);

        $factory = clone $this;
        $factory->serviceAccount = $serviceAccount;

        return $factory;
    }

    /**
     * @param non-empty-string $projectId
     */
    public function withProjectId(string $projectId): self
    {
        $factory = clone $this;
        $factory->projectId = $projectId;

        return $factory;
    }

    /**
     * @param non-empty-string $tenantId
     */
    public function withTenantId(string $tenantId): self
    {
        $factory = clone $this;
        $factory->tenantId = $tenantId;

        return $factory;
    }

    /**
     * @param UriInterface|non-empty-string $uri
     */
    public function withDatabaseUri(UriInterface|string $uri): self
    {
        $url = trim($uri instanceof UriInterface ? $uri->__toString() : $uri);

        if ($url === '') {
            throw new InvalidArgumentException('The database URI cannot be empty');
        }

        $factory = clone $this;
        $factory->databaseUrl = $url;

        return $factory;
    }

    /**
     * The object to use as the `auth` variable in your Realtime Database Rules
     * when the Admin SDK reads from or writes to the Realtime Database.
     *
     * This allows you to downscope the Admin SDK from its default full read and
     * write privileges. You can pass `null` to act as an unauthenticated client.
     *
     * @see https://firebase.google.com/docs/database/admin/start#authenticate-with-limited-privileges
     *
     * @param array<non-empty-string, mixed>|null $override
     */
    public function withDatabaseAuthVariableOverride(?array $override): self
    {
        $factory = clone $this;
        $factory->databaseAuthVariableOverrideMiddleware = Middleware::addDatabaseAuthVariableOverride($override);

        return $factory;
    }

    /**
     * @deprecated 7.19.0 Use `createFirestore($database)` instead
     * @see createFirestore()
     *
     * @param non-empty-string $database
     */
    public function withFirestoreDatabase(string $database): self
    {
        return $this->withFirestoreClientConfig(['database' => $database]);
    }

    /**
     * @param array<non-empty-string, mixed> $config
     */
    public function withFirestoreClientConfig(array $config): self
    {
        $factory = clone $this;
        $factory->firestoreClientConfig = array_merge($this->firestoreClientConfig, $config);

        return $factory;
    }

    /**
     * @param non-empty-string $name
     */
    public function withDefaultStorageBucket(string $name): self
    {
        $factory = clone $this;
        $factory->defaultStorageBucket = $name;

        return $factory;
    }

    /**
     * A cache instance to use when more specific caches are not set.
     */
    public function withDefaultCache(CacheItemPoolInterface $cache): self
    {
        $factory = clone $this;
        $factory->defaultCache = $cache;

        return $factory;
    }

    public function withVerifierCache(CacheItemPoolInterface $cache): self
    {
        $factory = clone $this;
        $factory->verifierCache = $cache;

        return $factory;
    }

    public function withAuthTokenCache(CacheItemPoolInterface $cache): self
    {
        $factory = clone $this;
        $factory->authTokenCache = $cache;

        return $factory;
    }

    public function withKeySetCache(CacheItemPoolInterface $cache): self
    {
        $factory = clone $this;
        $factory->keySetCache = $cache;

        return $factory;
    }

    public function withMapperCache(mixed $cache): self
    {
        $factory = clone $this;
        $factory->mapperCache = $cache;
        $factory->mapper = null;

        return $factory;
    }

    public function withNormalizerCache(mixed $cache): self
    {
        $factory = clone $this;
        $factory->normalizerCache = $cache;
        $factory->normalizer = null;

        return $factory;
    }

    public function withHttpClientOptions(HttpClientOptions $options): self
    {
        $factory = clone $this;
        $factory->httpClientOptions = $options;

        return $factory;
    }

    /**
     * @param non-empty-string|null $logLevel
     * @param non-empty-string|null $errorLogLevel
     */
    public function withHttpLogger(LoggerInterface $logger, ?MessageFormatter $formatter = null, ?string $logLevel = null, ?string $errorLogLevel = null): self
    {
        $formatter ??= new MessageFormatter();
        $logLevel ??= LogLevel::INFO;
        $errorLogLevel ??= LogLevel::NOTICE;

        $factory = clone $this;
        $factory->httpLogMiddleware = Middleware::log($logger, $formatter, $logLevel, $errorLogLevel);

        return $factory;
    }

    /**
     * @param non-empty-string|null $logLevel
     * @param non-empty-string|null $errorLogLevel
     */
    public function withHttpDebugLogger(LoggerInterface $logger, ?MessageFormatter $formatter = null, ?string $logLevel = null, ?string $errorLogLevel = null): self
    {
        $formatter ??= new MessageFormatter(MessageFormatter::DEBUG);
        $logLevel ??= LogLevel::INFO;
        $errorLogLevel ??= LogLevel::NOTICE;

        $factory = clone $this;
        $factory->httpDebugLogMiddleware = Middleware::log($logger, $formatter, $logLevel, $errorLogLevel);

        return $factory;
    }

    public function withClock(object $clock): self
    {
        if (!$clock instanceof ClockInterface) {
            $clock = WrappingClock::wrapping($clock);
        }

        $factory = clone $this;
        $factory->clock = $clock;

        return $factory;
    }

    public function createAppCheck(): Contract\AppCheck
    {
        $projectId = $this->getProjectId();
        $serviceAccount = $this->getServiceAccount();

        if ($serviceAccount === null) {
            throw new RuntimeException('Unable to use AppCheck without credentials');
        }

        $http = $this->createApiClient([
            'base_uri' => 'https://firebaseappcheck.googleapis.com/v1/projects/'.$projectId.'/',
        ]);

        $keySet = new CachedKeySet(
            'https://firebaseappcheck.googleapis.com/v1/jwks',
            new Client($this->httpClientOptions->guzzleConfig()),
            $this->httpFactory,
            $this->keySetCache ?? $this->defaultCache,
            21600,
            true,
        );

        return new AppCheck(
            new AppCheck\ApiClient($http, new AppCheckApiExceptionConverter($this->errorResponseParser)),
            new AppCheckTokenGenerator(
                $serviceAccount->clientEmail,
                $serviceAccount->privateKey,
                $this->clock,
            ),
            new AppCheckTokenVerifier($projectId, $keySet),
        );
    }

    public function createAuth(): Contract\Auth
    {
        $projectId = $this->getProjectId();

        $httpClient = $this->createApiClient();

        $signInHandler = new GuzzleHandler($projectId, $httpClient);
        $authApiClient = new ApiClient(
            $projectId,
            $this->tenantId,
            $httpClient,
            $signInHandler,
            $this->clock,
            new AuthApiExceptionConverter($this->errorResponseParser),
        );
        $customTokenGenerator = $this->createCustomTokenGenerator();
        $idTokenVerifier = $this->createIdTokenVerifier();
        $sessionCookieVerifier = $this->createSessionCookieVerifier();

        return new Auth($authApiClient, $customTokenGenerator, $idTokenVerifier, $sessionCookieVerifier, $this->clock);
    }

    public function createDatabase(): Contract\Database
    {
        $middlewares = array_filter([
            Middleware::ensureJsonSuffix(),
            $this->databaseAuthVariableOverrideMiddleware,
        ]);

        $http = $this->createApiClient(null, $middlewares);
        $databaseUrl = $this->getDatabaseUrl();
        $resourceUrlBuilder = UrlBuilder::create($databaseUrl);

        return new Database(
            GuzzleUtils::uriFor($databaseUrl),
            new Database\ApiClient(
                $http,
                $resourceUrlBuilder,
                new DatabaseApiExceptionConverter($this->errorResponseParser),
            ),
        );
    }

    public function createRemoteConfig(): Contract\RemoteConfig
    {
        $http = $this->createApiClient([
            'base_uri' => "https://firebaseremoteconfig.googleapis.com/v1/projects/{$this->getProjectId()}/remoteConfig",
        ]);

        return new RemoteConfig(
            new RemoteConfig\ApiClient(
                $this->getProjectId(),
                $http,
                new RemoteConfigApiExceptionConverter($this->errorResponseParser),
            ),
        );
    }

    public function createMessaging(): Contract\Messaging
    {
        $projectId = $this->getProjectId();

        $errorHandler = new MessagingApiExceptionConverter($this->clock);
        $requestFactory = new RequestFactory(
            requestFactory: $this->httpFactory,
            streamFactory: $this->httpFactory,
        );

        $messagingApiClient = new Messaging\ApiClient(
            $this->createApiClient(),
            $projectId,
            $requestFactory,
        );

        $appInstanceApiClient = new AppInstanceApiClient(
            $this->createApiClient([
                'base_uri' => 'https://iid.googleapis.com',
                'headers' => [
                    'access_token_auth' => 'true',
                ],
            ]),
            $errorHandler,
        );

        return new Messaging($messagingApiClient, $appInstanceApiClient, $errorHandler);
    }

    /**
     * @deprecated 7.14.0 Firebase Dynamic Links is deprecated and should not be used in new projects. The service will
     *                    shut down on August 25, 2025. The component will remain in the SDK until then, but as the
     *                    Firebase service is deprecated, this component is also deprecated
     * @codeCoverageIgnore
     *
     * @see https://firebase.google.com/support/dynamic-links-faq Dynamic Links Deprecation FAQ
     *
     * @param Stringable|non-empty-string|null $defaultDynamicLinksDomain
     */
    public function createDynamicLinksService($defaultDynamicLinksDomain = null): Contract\DynamicLinks
    {
        $apiClient = new DynamicLink\ApiClient(
            $this->createApiClient(),
            $this->httpFactory,
            $this->httpFactory,
        );

        $defaultDynamicLinksDomain = trim((string) $defaultDynamicLinksDomain);

        if ($defaultDynamicLinksDomain !== '') {
            return DynamicLinks::withApiClientAndDefaultDomain($apiClient, $defaultDynamicLinksDomain);
        }

        return DynamicLinks::withApiClient($apiClient);
    }

    /**
     * @param non-empty-string|null $databaseName
     */
    public function createFirestore(?string $databaseName = null): Contract\Firestore
    {
        $config = $this->googleCloudClientConfig() + $this->firestoreClientConfig;

        if ($databaseName !== null) {
            $config['database'] = $databaseName;
        }

        return Firestore::fromConfig($config);
    }

    public function createStorage(): Contract\Storage
    {
        try {
            $storageClient = new StorageClient($this->googleCloudClientConfig());
        } catch (Throwable $e) {
            throw new RuntimeException('Unable to create a StorageClient: '.$e->getMessage(), $e->getCode(), $e);
        }

        return new Storage($storageClient, $this->getStorageBucketName());
    }

    /**
     * @deprecated 7.20.0
     * @codeCoverageIgnore
     *
     * @return array<mixed>
     */
    public function getDebugInfo(): array
    {
        try {
            $projectId = $this->getProjectId();
        } catch (Throwable $e) {
            $projectId = $e->getMessage();
        }

        try {
            $credentials = $this->getGoogleAuthTokenCredentials();

            if ($credentials !== null) {
                $credentials = $credentials::class;
            }
        } catch (Throwable $e) {
            $credentials = $e->getMessage();
        }

        try {
            $databaseUrl = $this->getDatabaseUrl();
        } catch (Throwable $e) {
            $databaseUrl = $e->getMessage();
        }

        return [
            'credentialsType' => $credentials,
            'databaseUrl' => $databaseUrl,
            'defaultStorageBucket' => $this->defaultStorageBucket,
            'projectId' => $projectId,
            'serviceAccount' => $this->getServiceAccount(),
            'tenantId' => $this->tenantId,
            'tokenCacheType' => $this->authTokenCache !== null ? $this->authTokenCache::class : $this->defaultCache::class,
            'verifierCacheType' => $this->verifierCache !== null ? $this->verifierCache::class : $this->defaultCache::class,
        ];
    }

    /**
     * @param array<non-empty-string, mixed>|null $config
     * @param array<callable(callable): callable>|null $middlewares
     */
    public function createApiClient(?array $config = null, ?array $middlewares = null): Client
    {
        $config ??= [];
        $middlewares ??= [];

        $config = [...$this->httpClientOptions->guzzleConfig(), ...$config];

        $handler = HandlerStack::create($config['handler'] ?? null);

        if ($this->httpLogMiddleware !== null) {
            $handler->push($this->httpLogMiddleware, 'http_logs');
        }

        if ($this->httpDebugLogMiddleware !== null) {
            $handler->push($this->httpDebugLogMiddleware, 'http_debug_logs');
        }

        foreach ($this->httpClientOptions->guzzleMiddlewares() as $middleware) {
            $handler->push($middleware['middleware'], $middleware['name']);
        }

        foreach ($middlewares as $middleware) {
            $handler->push($middleware);
        }

        $credentials = $this->getGoogleAuthTokenCredentials();

        if (!$credentials instanceof FetchAuthTokenInterface) {
            throw new RuntimeException('Unable to create an API client without credentials');
        }

        $projectId = $this->getProjectId();
        $cachePrefix = 'kreait_firebase_'.$projectId;

        $credentials = new FetchAuthTokenCache($credentials, ['prefix' => $cachePrefix], $this->authTokenCache ?? $this->defaultCache);
        $authTokenHandler = HttpHandlerFactory::build(new Client($config));

        $handler->push(new AuthTokenMiddleware($credentials, $authTokenHandler));

        $config['handler'] = $handler;
        $config['auth'] = 'google_auth';

        return new Client($config);
    }

    /**
     * @return array<non-empty-string, mixed>
     */
    private function googleCloudClientConfig(): array
    {
        $config = [
            'projectId' => $this->getProjectId(),
            'authCache' => $this->authTokenCache ?? $this->defaultCache,
        ];

        $credentials = $this->getGoogleAuthTokenCredentials();
        if ($credentials !== null) {
            $config['credentialsFetcher'] = $credentials;
        }

        return $config;
    }

    /**
     * @return non-empty-string
     */
    private function getProjectId(): string
    {
        if ($this->projectId !== null) {
            return $this->projectId;
        }

        $credentials = $this->getGoogleAuthTokenCredentials();
        $projectId = $credentials instanceof ProjectIdProviderInterface
            ? $credentials->getProjectId()
            : Util::getenv('GOOGLE_CLOUD_PROJECT');

        $projectId ??= $this->getServiceAccount()?->projectId;

        if (is_string($projectId) && $projectId !== '') {
            return $this->projectId = $projectId;
        }

        throw new RuntimeException('Unable to determine the Firebase Project ID');
    }

    /**
     * @return non-empty-string
     */
    private function getDatabaseUrl(): string
    {
        return $this->databaseUrl ??= sprintf('https://%s.firebaseio.com', $this->getProjectId());
    }

    /**
     * @return non-empty-string
     */
    private function getStorageBucketName(): string
    {
        return $this->defaultStorageBucket ??= sprintf('%s.appspot.com', $this->getProjectId());
    }

    private function createCustomTokenGenerator(): ?CustomTokenViaGoogleCredentials
    {
        $credentials = $this->getGoogleAuthTokenCredentials();

        if ($credentials instanceof SignBlobInterface) {
            return new CustomTokenViaGoogleCredentials($credentials, $this->tenantId);
        }

        return null;
    }

    private function createIdTokenVerifier(): IdTokenVerifier
    {
        $verifier = IdTokenVerifier::createWithProjectIdAndCache($this->getProjectId(), $this->verifierCache ?? $this->defaultCache);

        if ($this->tenantId === null) {
            return $verifier;
        }

        return $verifier->withExpectedTenantId($this->tenantId);
    }

    private function getMapper(): Mapper
    {
        return $this->mapper ??= new Mapper($this->mapperCache);
    }

    private function getNormalizer(): Normalizer
    {
        return $this->normalizer ??= new Normalizer($this->normalizerCache);
    }

    private function createSessionCookieVerifier(): SessionCookieVerifier
    {
        return SessionCookieVerifier::createWithProjectIdAndCache($this->getProjectId(), $this->verifierCache ?? $this->defaultCache);
    }

    private function getServiceAccount(): ?ServiceAccount
    {
        if ($this->serviceAccount !== null) {
            return $this->serviceAccount;
        }

        $googleApplicationCredentials = Util::getenv('GOOGLE_APPLICATION_CREDENTIALS');

        if ($googleApplicationCredentials === null) {
            return $this->serviceAccount;
        }

        return $this->serviceAccount = $this->mapServiceAccount($googleApplicationCredentials);
    }

    private function getGoogleAuthTokenCredentials(): ?FetchAuthTokenInterface
    {
        if ($this->googleAuthTokenCredentials instanceof FetchAuthTokenInterface) {
            return $this->googleAuthTokenCredentials;
        }

        $serviceAccount = $this->getServiceAccount();

        if ($serviceAccount !== null) {
            return $this->googleAuthTokenCredentials = new ServiceAccountCredentials(
                self::API_CLIENT_SCOPES,
                $this->normalizeServiceAccount($serviceAccount),
            );
        }

        try {
            return $this->googleAuthTokenCredentials = ApplicationDefaultCredentials::getCredentials(self::API_CLIENT_SCOPES);
        } catch (Throwable) {
            return null;
        }
    }

    private function mapServiceAccount(mixed $value): ServiceAccount
    {
        return $this->getMapper()
            ->allowSuperfluousKeys()
            ->snakeToCamelCase()
            ->map(ServiceAccount::class, Source::parse($value));
    }

    /**
     * @return array<non-empty-string, mixed>
     */
    private function normalizeServiceAccount(ServiceAccount $serviceAccount): array
    {
        return $this->getNormalizer()
            ->camelToSnakeCase()
            ->toArray($serviceAccount);
    }
}
