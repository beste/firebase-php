<?php

declare(strict_types=1);

namespace Kreait\Firebase;

use Kreait\Firebase\AppCheck\ApiClient;
use Kreait\Firebase\AppCheck\AppCheckToken;
use Kreait\Firebase\AppCheck\AppCheckTokenGenerator;
use Kreait\Firebase\AppCheck\AppCheckTokenOptions;
use Kreait\Firebase\AppCheck\AppCheckTokenVerifier;
use Kreait\Firebase\AppCheck\DebugToken;
use Kreait\Firebase\AppCheck\VerifyAppCheckTokenResponse;
use Kreait\Firebase\Contract\AppCheckWithDebugTokens;
use Kreait\Firebase\Contract\AppCheckWithReplayProtection;
use SensitiveParameter;

use function bin2hex;
use function chr;
use function is_array;
use function ord;
use function random_bytes;
use function str_split;
use function vsprintf;

/**
 * @internal
 */
final readonly class AppCheck implements Contract\AppCheck, AppCheckWithReplayProtection, AppCheckWithDebugTokens
{
    public function __construct(
        private ApiClient $client,
        private AppCheckTokenGenerator $tokenGenerator,
        private AppCheckTokenVerifier $tokenVerifier,
    ) {
    }

    public function createToken(string $appId, AppCheckTokenOptions|array|null $options = null): AppCheckToken
    {
        if (is_array($options)) {
            $options = AppCheckTokenOptions::fromArray($options);
        }

        $customToken = $this->tokenGenerator->createCustomToken($appId, $options);
        $result = $this->client->exchangeCustomToken($appId, $customToken);

        return AppCheckToken::fromArray($result);
    }

    public function verifyToken(#[SensitiveParameter] string $appCheckToken): VerifyAppCheckTokenResponse
    {
        return $this->tokenVerifier->verifyToken($appCheckToken);
    }

    public function verifyTokenWithReplayProtection(#[SensitiveParameter] string $appCheckToken): VerifyAppCheckTokenResponse
    {
        return $this->tokenVerifier->verifyToken($appCheckToken, true);
    }

    public function createDebugToken(string $appId, string $displayName, #[SensitiveParameter] ?string $token = null): DebugToken
    {
        $token ??= $this->generateUuid4();

        return DebugToken::fromArray($this->client->createDebugToken($appId, $displayName, $token), $token);
    }

    public function getDebugToken(string $appId, string $debugTokenId): DebugToken
    {
        return DebugToken::fromArray($this->client->getDebugToken($appId, $debugTokenId));
    }

    public function listDebugTokens(string $appId): array
    {
        $tokens = [];
        $pageToken = null;

        do {
            $result = $this->client->listDebugTokens($appId, $pageToken);

            foreach ($result['debugTokens'] ?? [] as $data) {
                $tokens[] = DebugToken::fromArray($data);
            }

            $pageToken = $result['nextPageToken'] ?? null;
        } while ($pageToken !== null && $pageToken !== '');

        return $tokens;
    }

    public function updateDebugToken(string $appId, string $debugTokenId, string $displayName): DebugToken
    {
        return DebugToken::fromArray($this->client->updateDebugToken($appId, $debugTokenId, $displayName));
    }

    public function deleteDebugToken(string $appId, string $debugTokenId): void
    {
        $this->client->deleteDebugToken($appId, $debugTokenId);
    }

    /**
     * @return non-empty-string
     */
    private function generateUuid4(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0F) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3F) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
