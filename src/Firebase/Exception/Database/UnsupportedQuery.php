<?php

declare(strict_types=1);

namespace Kreait\Firebase\Exception\Database;

use Kreait\Firebase\Database\Query;
use Kreait\Firebase\Exception\DatabaseException;
use Kreait\Firebase\Exception\RuntimeException;
use Throwable;

final class UnsupportedQuery extends RuntimeException implements DatabaseException
{
    public function __construct(
        private readonly Query $query,
        string $message,
        ?Throwable $previous = null,
    ) {
        parent::__construct(message: $message, previous: $previous);
    }

    public function getQuery(): Query
    {
        return $this->query;
    }
}
