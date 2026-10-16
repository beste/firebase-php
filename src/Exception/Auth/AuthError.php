<?php

declare(strict_types=1);

namespace Kreait\Firebase\Exception\Auth;

use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\RuntimeException;

final class AuthError extends RuntimeException implements AuthException
{
    public static function missingProjectId(string $message): self
    {
        $fullMessage = <<<MSG
            {$message}

            The current Firebase project is configured without a project ID. The project
            ID can be determined automatically with service account credentials, by
            providing a `GOOGLE_CLOUD_PROJECT=project_id` environment variable, or
            manually by calling `withProjectId()` on the SDK's factory.

            MSG;

        return new self($fullMessage);
    }
}
