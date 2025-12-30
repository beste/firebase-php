<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth;

use Beste\Clock\SystemClock;
use Beste\Clock\WrappingClock;
use DateInterval;
use Kreait\Firebase\Exception\InvalidArgumentException;
use Lcobucci\JWT\Token;
use Psr\Clock\ClockInterface;
use SensitiveParameter;

use Throwable;
use function is_int;

/**
 * @internal
 */
final readonly class CreateSessionCookie
{
    private const string FIVE_MINUTES = 'PT5M';

    private const string TWO_WEEKS = 'P14D';

    private function __construct(
        #[SensitiveParameter]
        private string $idToken,
        private ?string $tenantId,
        private DateInterval $ttl,
        private ClockInterface $clock,
    ) {
    }

/**
 * @param positive-int|DateInterval $ttl
 */
public static function forIdToken(#[SensitiveParameter] Token|string $idToken, ?string $tenantId, DateInterval|int $ttl, ?object $clock = null): self
    {
        $clock ??= SystemClock::create();

        if (!($clock instanceof ClockInterface)) {
            $clock = WrappingClock::wrapping($clock);
        }

        if ($idToken instanceof Token) {
            $idToken = $idToken->toString();
        }

        $ttl = self::assertValidDuration($ttl, $clock);

        return new self($idToken, $tenantId, $ttl, $clock);
    }

    public function idToken(): string
    {
        return $this->idToken;
    }

    public function tenantId(): ?string
    {
        return $this->tenantId;
    }

    public function ttl(): DateInterval
    {
        return $this->ttl;
    }

    public function ttlInSeconds(): int
    {
        $now = $this->clock->now();

        return $now->add($this->ttl)->getTimestamp() - $now->getTimestamp();
    }

    /**
     * @param DateInterval|positive-int $ttl
     *
     * @throws InvalidArgumentException
     */
    private static function assertValidDuration(DateInterval|int $ttl, ClockInterface $clock): DateInterval
    {
        if (is_int($ttl)) {
            try {
                $ttl = new DateInterval(sprintf('PT%sS', $ttl));
            } catch (Throwable $e) {
                throw new InvalidArgumentException('Invalid TTL value given: '.$e->getMessage());
            }
        }

        $now = $clock->now();

        $expiresAt = $now->add($ttl);

        $min = $now->add(new DateInterval(self::FIVE_MINUTES));
        $max = $now->add(new DateInterval(self::TWO_WEEKS));

        if ($expiresAt >= $min && $expiresAt <= $max) {
            return $ttl;
        }

        throw new InvalidArgumentException('The TTL of a session must be between 5 minutes and 14 days');
    }
}
