<?php

declare(strict_types=1);

namespace Kreait\Firebase\Contract;

use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\FirebaseInstallationId;
use Kreait\Firebase\Messaging\FirebaseInstallationIds;
use Kreait\Firebase\Messaging\Message;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Kreait\Firebase\Messaging\RegistrationToken;
use Kreait\Firebase\Messaging\RegistrationTokens;

/**
 * Transitional contract for messages with explicit target types.
 *
 * This interface exists to support Firebase Installation IDs without changing
 * the existing Messaging::sendMulticast() signature in a backwards-incompatible
 * way, and to provide the FID counterparts of the methods on Messaging that are
 * named after registration tokens. Messaging::sendMulticast() defers to
 * sendMulticastToRegistrationTokens(). In a future major release, sendMulticast()
 * should be deprecated in favor of the two multicast methods defined here.
 */
interface MessagingWithMulticast
{
    /**
     * @param Message|array<mixed> $message
     * @param RegistrationTokens|RegistrationToken|list<RegistrationToken|string>|non-empty-string $registrationTokens
     *
     * @throws InvalidArgumentException if the message is invalid or the list of registration tokens is empty
     * @throws MessagingException if the API request failed
     * @throws FirebaseException if something very unexpected happened (never :))
     */
    public function sendMulticastToRegistrationTokens(Message|array $message, RegistrationTokens|RegistrationToken|array|string $registrationTokens, bool $validateOnly = false): MulticastSendReport;

    /**
     * @param Message|array<mixed> $message
     * @param FirebaseInstallationIds|FirebaseInstallationId|list<FirebaseInstallationId|string>|non-empty-string $firebaseInstallationIds
     *
     * @throws InvalidArgumentException if the message is invalid or the list of Firebase Installation IDs is empty
     * @throws MessagingException if the API request failed
     * @throws FirebaseException if something very unexpected happened (never :))
     */
    public function sendMulticastToFids(Message|array $message, FirebaseInstallationIds|FirebaseInstallationId|array|string $firebaseInstallationIds, bool $validateOnly = false): MulticastSendReport;

    /**
     * The Firebase Installation ID counterpart of Messaging::validateRegistrationTokens().
     *
     * @param FirebaseInstallationIds|FirebaseInstallationId|list<FirebaseInstallationId|non-empty-string>|non-empty-string $firebaseInstallationIdOrIds
     *
     * @throws MessagingException
     * @throws FirebaseException
     *
     * @return array{
     *     valid: list<non-empty-string>,
     *     unknown: list<non-empty-string>,
     *     invalid: list<non-empty-string>
     * }
     */
    public function validateFids(FirebaseInstallationIds|FirebaseInstallationId|array|string $firebaseInstallationIdOrIds): array;
}
