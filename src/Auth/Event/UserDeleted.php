<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\Event;

final readonly class UserDeleted
{
    /**
     * @param non-empty-string $uid
     */
    public function __construct(public string $uid)
    {
    }
}
