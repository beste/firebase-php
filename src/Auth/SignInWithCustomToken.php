<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth;

use SensitiveParameter;

/**
 * @internal
 */
final class SignInWithCustomToken implements IsTenantAware, SignIn
{
    private ?string $tenantId = null;

    private function __construct(#[SensitiveParameter] private readonly string $customToken)
    {
    }

    public static function fromValue(#[SensitiveParameter] string $customToken): self
    {
        return new self($customToken);
    }

    public function withTenantId(string $tenantId): self
    {
        $action = clone $this;
        $action->tenantId = $tenantId;

        return $action;
    }

    public function customToken(): string
    {
        return $this->customToken;
    }

    public function tenantId(): ?string
    {
        return $this->tenantId;
    }
}
