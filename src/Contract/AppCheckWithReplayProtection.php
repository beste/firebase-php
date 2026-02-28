<?php

declare(strict_types=1);

namespace Kreait\Firebase\Contract;

use Kreait\Firebase\AppCheck\VerifyAppCheckTokenResponse;
use Kreait\Firebase\Exception;
use Kreait\Firebase\Exception\AppCheck\FailedToVerifyAppCheckReplayProtection;
use Kreait\Firebase\Exception\AppCheck\FailedToVerifyAppCheckToken;
use Kreait\Firebase\Exception\AppCheck\InvalidAppCheckToken;
use SensitiveParameter;

/**
 * Transitional contract for replay protection support.
 *
 * This interface exists to provide replay protection without changing the
 * existing AppCheck::verifyToken() signature in a backwards-incompatible way.
 * In a future major release, replay protection should be moved into
 * AppCheck::verifyToken() as an additional argument/options parameter.
 */
interface AppCheckWithReplayProtection
{
    /**
     * Verifies an App Check token and consumes it for replay protection.
     *
     * @param non-empty-string $appCheckToken
     *
     * @throws InvalidAppCheckToken
     * @throws FailedToVerifyAppCheckToken
     * @throws FailedToVerifyAppCheckReplayProtection
     * @throws Exception\FirebaseException
     */
    public function verifyTokenWithReplayProtection(#[SensitiveParameter] string $appCheckToken): VerifyAppCheckTokenResponse;
}
