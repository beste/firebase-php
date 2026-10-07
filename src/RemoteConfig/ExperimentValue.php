<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig;

use JsonSerializable;

/**
 * @phpstan-type RemoteConfigExperimentValueShape array{
 *     experimentId: string,
 *     variantValue: list<array{variantId: string, value?: string, noChange?: bool}>,
 *     exposurePercent?: int<0, 100>
 * }
 *
 * @see https://firebase.google.com/docs/reference/remote-config/rest/v1/RemoteConfig#experimentvalue
 */
final readonly class ExperimentValue implements JsonSerializable
{
    /**
     * @param RemoteConfigExperimentValueShape $data
     */
    private function __construct(private array $data)
    {
    }

    /**
     * @param RemoteConfigExperimentValueShape $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * @return RemoteConfigExperimentValueShape
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @return RemoteConfigExperimentValueShape
     */
    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
