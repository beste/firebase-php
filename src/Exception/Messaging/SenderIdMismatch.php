<?php

declare(strict_types=1);

namespace Kreait\Firebase\Exception\Messaging;

use Kreait\Firebase\Exception\HasErrors;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Exception\RuntimeException;

/**
 * The registration token is registered to a different Firebase project than the
 * project the message is sent from. This is a permanent, per-token failure: the
 * token should be treated like an unregistered token and removed from the list
 * of message targets.
 */
final class SenderIdMismatch extends RuntimeException implements MessagingException
{
    use HasErrors;

    /**
     * @internal
     *
     * @param array<mixed> $errors
     */
    public function withErrors(array $errors): self
    {
        $new = new self(message: $this->getMessage(), previous: $this->getPrevious());
        $new->errors = $errors;

        return $new;
    }
}
