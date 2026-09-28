<?php

declare(strict_types=1);

namespace Kreait\Firebase\Messaging;

use Countable;
use IteratorAggregate;
use Kreait\Firebase\Exception\Messaging\InvalidArgument;
use Traversable;

use function array_map;
use function array_values;
use function count;
use function is_string;

/**
 * @implements IteratorAggregate<FirebaseInstallationId>
 */
final readonly class FirebaseInstallationIds implements Countable, IteratorAggregate
{
    /**
     * @var list<FirebaseInstallationId>
     */
    private array $fids;

    /**
     * @internal
     */
    public function __construct(FirebaseInstallationId ...$fids)
    {
        $this->fids = array_values($fids);
    }

    /**
     * @param FirebaseInstallationIds|FirebaseInstallationId|array<FirebaseInstallationId|string>|non-empty-string $values
     *
     * @throws InvalidArgument
     */
    public static function fromValue(FirebaseInstallationId|FirebaseInstallationIds|array|string $values): self
    {
        $fids = [];

        if ($values instanceof self) {
            $fids = $values->values();
        } elseif ($values instanceof FirebaseInstallationId) {
            $fids = [$values];
        } elseif (is_string($values)) {
            $fids = [FirebaseInstallationId::fromValue($values)];
        } else {
            foreach ($values as $value) {
                if ($value instanceof FirebaseInstallationId) {
                    $fids[] = $value;
                } elseif ($value !== '') {
                    $fids[] = FirebaseInstallationId::fromValue($value);
                }
            }
        }

        if (count($fids) === 0) {
            throw new InvalidArgument('No Firebase Installation IDs provided');
        }

        return new self(...$fids);
    }

    /**
     * @return Traversable<FirebaseInstallationId>
     */
    public function getIterator(): Traversable
    {
        yield from $this->fids;
    }

    public function isEmpty(): bool
    {
        return $this->fids === [];
    }

    /**
     * @return list<FirebaseInstallationId>
     */
    public function values(): array
    {
        return $this->fids;
    }

    /**
     * @return list<non-empty-string>
     */
    public function asStrings(): array
    {
        return array_map(fn(FirebaseInstallationId $fid): string => $fid->value(), $this->fids);
    }

    public function count(): int
    {
        return count($this->fids);
    }

    /**
     * @param FirebaseInstallationId|non-empty-string $fid
     */
    public function has(FirebaseInstallationId|string $fid): bool
    {
        $fid = $fid instanceof FirebaseInstallationId ? $fid : FirebaseInstallationId::fromValue($fid);

        foreach ($this->fids as $existing) {
            if ($existing->value() === $fid->value()) {
                return true;
            }
        }

        return false;
    }
}
