<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\Event;

final readonly class CustomUserClaimsChanged
{
    /**
     * @param non-empty-string $uid
     * @param array<non-empty-string, mixed> $claims
     */
    public function __construct(
        public string $uid,
        public array $claims,
    ) {
    }
}
