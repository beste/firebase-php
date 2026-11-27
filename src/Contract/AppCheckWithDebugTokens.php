<?php

declare(strict_types=1);

namespace Kreait\Firebase\Contract;

use Kreait\Firebase\AppCheck\DebugToken;
use Kreait\Firebase\Exception;
use SensitiveParameter;

/**
 * Transitional contract for App Check debug token support.
 *
 * This interface exists to add debug token support without adding methods to the
 * existing AppCheck contract in a backwards-incompatible way.
 * In a future major release, these methods should be moved into AppCheck.
 *
 * @see https://firebase.google.com/docs/app-check/web/debug-provider
 * @see https://firebase.google.com/docs/reference/appcheck/rest/v1/projects.apps.debugTokens
 */
interface AppCheckWithDebugTokens
{
    /**
     * Registers a new debug token for the given app.
     *
     * If no token is given, a random UUID4 is generated. The returned DebugToken carries the secret in its
     * `token` property; it cannot be retrieved from the App Check API afterwards.
     *
     * Each app can have a maximum of 20 debug tokens. The display name can be at most 50 characters long.
     *
     * @param non-empty-string $appId
     * @param non-empty-string $displayName
     * @param non-empty-string|null $token a UUID4 secret
     *
     * @throws Exception\AppCheckException
     * @throws Exception\FirebaseException
     */
    public function createDebugToken(string $appId, string $displayName, #[SensitiveParameter] ?string $token = null): DebugToken;

    /**
     * @param non-empty-string $appId
     * @param non-empty-string $debugTokenId
     *
     * @throws Exception\AppCheckException
     * @throws Exception\FirebaseException
     */
    public function getDebugToken(string $appId, string $debugTokenId): DebugToken;

    /**
     * @param non-empty-string $appId
     *
     * @throws Exception\AppCheckException
     * @throws Exception\FirebaseException
     *
     * @return list<DebugToken>
     */
    public function listDebugTokens(string $appId): array;

    /**
     * Updates the display name of a debug token. The display name can be at most 50 characters long.
     *
     * @param non-empty-string $appId
     * @param non-empty-string $debugTokenId
     * @param non-empty-string $displayName
     *
     * @throws Exception\AppCheckException
     * @throws Exception\FirebaseException
     */
    public function updateDebugToken(string $appId, string $debugTokenId, string $displayName): DebugToken;

    /**
     * Deletes a debug token. A deleted debug token can no longer be exchanged for an App Check token.
     *
     * @param non-empty-string $appId
     * @param non-empty-string $debugTokenId
     *
     * @throws Exception\AppCheckException
     * @throws Exception\FirebaseException
     */
    public function deleteDebugToken(string $appId, string $debugTokenId): void;
}
