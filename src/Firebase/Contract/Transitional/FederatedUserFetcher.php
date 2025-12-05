<?php

declare(strict_types=1);

namespace Kreait\Firebase\Contract\Transitional;

use Kreait\Firebase\Auth\UserRecord;
use Kreait\Firebase\Exception;
use Kreait\Firebase\Exception\Auth\UserNotFound;

/**
 * @TODO: This interface is intended to be integrated into the Auth interface on the next major release.
 */
interface FederatedUserFetcher
{
    /**
     * @param non-empty-string $providerId
     * @param non-empty-string $providerUid
     *
     * @throws UserNotFound
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function getUserByProviderUid(string $providerId, string $providerUid): UserRecord;
}
