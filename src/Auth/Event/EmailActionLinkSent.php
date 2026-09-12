<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\Event;

final readonly class EmailActionLinkSent
{
    /**
     * @param non-empty-string $type
     * @param non-empty-string $email
     * @param non-empty-string|null $locale
     */
    public function __construct(
        public string $type,
        public string $email,
        public ?string $locale,
    ) {
    }
}
