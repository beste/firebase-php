<?php

declare(strict_types=1);

namespace Kreait\Firebase;

use SensitiveParameter;

/**
 * @internal
 */
final class ServiceAccount
{
    public function __construct(
        /** @var non-empty-string */
        public string $type,
        /** @var non-empty-string */
        #[SensitiveParameter] public string $projectId,
        /** @var non-empty-string */
        #[SensitiveParameter] public string $clientEmail,
        /** @var non-empty-string */
        #[SensitiveParameter] public string $clientId,
        /** @var non-empty-string */
        #[SensitiveParameter] public string $privateKey,
        /** @var non-empty-string */
        #[SensitiveParameter] public string $privateKeyId,
        /** @var non-empty-string */
        #[SensitiveParameter] public string $authUri,
        /** @var non-empty-string */
        #[SensitiveParameter] public string $tokenUri,
        /** @var non-empty-string */
        #[SensitiveParameter] public string $authProviderX509CertUrl,
        /** @var non-empty-string */
        #[SensitiveParameter] public string $clientX509CertUrl,
        /** @var non-empty-string|null */
        #[SensitiveParameter] public ?string $quotaProjectId = null,
        /** @var non-empty-string|null */
        #[SensitiveParameter] public ?string $universeDomain = null,
    ) {
    }
}
