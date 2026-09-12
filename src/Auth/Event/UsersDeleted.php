<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\Event;

use Kreait\Firebase\Auth\DeleteUsersResult;

final readonly class UsersDeleted
{
    /**
     * @param list<non-empty-string> $uids
     */
    public function __construct(
        public array $uids,
        public DeleteUsersResult $result,
    ) {
    }
}
