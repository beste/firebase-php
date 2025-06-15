<?php

declare(strict_types=1);

namespace Kreait\Firebase\Exception\Auth;

use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\RuntimeException;
use Lcobucci\JWT\Token;

final class RevokedSessionCookie extends RuntimeException implements AuthException
{
    public function __construct(private readonly Token $token)
    {
        parent::__construct('The Firebase session cookie has been revoked.');
    }

    public function getToken(): Token
    {
        return $this->token;
    }
}
