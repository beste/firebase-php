<?php

declare(strict_types=1);

namespace Kreait\Firebase;

/**
 * @internal
 */
final class ServiceAccount
{
    public function __construct(
        /** @var non-empty-string */
        public string $type,
        /** @var non-empty-string */
        #[\SensitiveParameter]
        public string $projectId,
        /** @var non-empty-string */
        #[\SensitiveParameter]
        public string $clientEmail,
        /** @var non-empty-string */
        #[\SensitiveParameter]
        public string $privateKey,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        public ?string $clientId = null,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        public ?string $privateKeyId = null,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        public ?string $authUri = null,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        public ?string $tokenUri = null,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        public ?string $authProviderX509CertUrl = null,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        public ?string $clientX509CertUrl = null,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        public ?string $quotaProjectId = null,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        public ?string $universeDomain = null,
    ) {
    }
}
