<?php

declare(strict_types=1);

namespace Kreait\Firebase\AppCheck;

use Beste\Json;
use GuzzleHttp\ClientInterface;
use Kreait\Firebase\Exception\AppCheck\AppCheckError;
use Kreait\Firebase\Exception\AppCheckApiExceptionConverter;
use Kreait\Firebase\Exception\AppCheckException;
use Psr\Http\Message\ResponseInterface;
use SensitiveParameter;
use Throwable;
use function is_bool;

/**
 * @internal
 *
 * @phpstan-import-type AppCheckTokenShape from AppCheckToken
 */
final readonly class ApiClient
{
    public function __construct(
        private ClientInterface $client,
        private AppCheckApiExceptionConverter $errorHandler,
    ) {
    }

    /**
     * @throws AppCheckException
     *
     * @return AppCheckTokenShape
     */
    public function exchangeCustomToken(string $appId, string $customToken): array
    {
        $response = $this->post('apps/'.$appId.':exchangeCustomToken', [
            'headers' => [
                'Content-Type' => 'application/json; UTF-8',
            ],
            'body' => Json::encode([
                'customToken' => $customToken,
            ]),
        ]);

        /** @var AppCheckTokenShape $decoded */
        $decoded = Json::decode((string) $response->getBody(), true);

        return $decoded;
    }

    /**
     * @param non-empty-string $projectId
     *
     * @throws AppCheckException
     */
    public function verifyReplayProtection(#[SensitiveParameter] string $token, string $projectId): bool
    {
        $response = $this->post(
            'https://firebaseappcheck.googleapis.com/v1beta/projects/'.$projectId.':verifyAppCheckToken',
            [
                'headers' => [
                    'Content-Type' => 'application/json; UTF-8',
                ],
                'body' => Json::encode([
                    'app_check_token' => $token,
                ]),
            ],
        );

        /** @var array{alreadyConsumed?: mixed} $decoded */
        $decoded = Json::decode((string) $response->getBody(), true);

        $alreadyConsumed = $decoded['alreadyConsumed'] ?? false;

        if (!is_bool($alreadyConsumed)) {
            throw new AppCheckError('The App Check API returned an invalid "alreadyConsumed" value.');
        }

        return $alreadyConsumed;
    }

    /**
     * @param non-empty-string $path
     * @param array<string, mixed>|null $options
     * @throws AppCheckException
     */
    private function post(string $path, ?array $options = null): ResponseInterface
    {
        $options ??= [];

        try {
            return $this->client->request('POST', $path, $options);
        } catch (Throwable $e) {
            throw $this->errorHandler->convertException($e);
        }
    }
}
