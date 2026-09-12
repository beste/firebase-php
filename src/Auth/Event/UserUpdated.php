<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\Event;

use Kreait\Firebase\Auth\UserRecord;

final readonly class UserUpdated
{
    public function __construct(public UserRecord $user)
    {
    }
}
