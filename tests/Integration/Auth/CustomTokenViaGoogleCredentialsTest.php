<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration\Auth;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Kreait\Firebase\Auth\CustomTokenViaGoogleCredentials;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Tests\IntegrationTestCase;
use Kreait\Firebase\Valinor\Mapper;
use Kreait\Firebase\Valinor\Normalizer;
use Kreait\Firebase\Valinor\Source;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\UnencryptedToken;
use PHPUnit\Framework\Attributes\Test;

/**
 * @internal
 */
final class CustomTokenViaGoogleCredentialsTest extends IntegrationTestCase
{
    private string $uid = 'some-uid';

    private CustomTokenViaGoogleCredentials $generator;

    private Auth $auth;

    protected function setUp(): void
    {
        $serviceAccount = (new Mapper())
            ->allowSuperfluousKeys()
            ->snakeToCamelCase()
            ->map(ServiceAccount::class, Source::parse(self::$credentials));

        $normalizedServiceAccount = (new Normalizer())->camelToSnakeCase()->toArray($serviceAccount);

        $credentials = new ServiceAccountCredentials(Factory::API_CLIENT_SCOPES, $normalizedServiceAccount);

        $this->generator = new CustomTokenViaGoogleCredentials($credentials);
        $this->auth = self::$factory->createAuth();
    }

    #[Test]
    public function createCustomToken(): void
    {
        $token = $this->generator->createCustomToken($this->uid, ['a-claim' => 'a-value']);

        $check = $this->auth->signInWithCustomToken($token);

        $this->assertSame($this->uid, $check->firebaseUserId());
    }

    #[Test]
    public function aGeneratedCustomTokenCanBeParsed(): void
    {
        $token = $this->generator->createCustomToken($this->uid, ['a-claim' => 'a-value']);

        $tokenString = trim($token->toString(), '=');
        $parsed = (new Parser(new JoseEncoder()))->parse($tokenString);

        $this->assertInstanceOf(UnencryptedToken::class, $parsed);
        $this->assertSame($this->uid, $parsed->claims()->get('uid'));
    }
}
