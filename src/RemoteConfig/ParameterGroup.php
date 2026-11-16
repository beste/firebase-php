<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig;

use JsonSerializable;

/**
 * @phpstan-import-type RemoteConfigParameterShape from Parameter
 *
 * @phpstan-type RemoteConfigParameterGroupShape array{
 *     description?: string|null,
 *     parameters?: array<non-empty-string, RemoteConfigParameterShape>}
 */
final class ParameterGroup implements JsonSerializable
{
    private string $description = '';

    /**
     * @var array<non-empty-string, Parameter>
     */
    private array $parameters = [];

    /**
     * @param non-empty-string $name
     */
    private function __construct(private readonly string $name)
    {
    }

    /**
     * @param non-empty-string $name
     */
    public static function named(string $name): self
    {
        return new self($name);
    }

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    /**
     * @return array<non-empty-string, Parameter>
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    public function withDescription(string $description): self
    {
        $group = clone $this;
        $group->description = $description;

        return $group;
    }

    public function withParameter(Parameter $parameter): self
    {
        $group = clone $this;
        $group->parameters[$parameter->name()] = $parameter;

        return $group;
    }

    /**
     * @return RemoteConfigParameterGroupShape
     */
    public function toArray(): array
    {
        $parameters = [];

        foreach ($this->parameters as $parameter) {
            $parameters[$parameter->name()] = $parameter->toArray();
        }

        $array = [
            'description' => $this->description,
        ];

        // An empty PHP array would be encoded as a JSON list, but the API expects a map
        if ($parameters !== []) {
            $array['parameters'] = $parameters;
        }

        return $array;
    }

    /**
     * @return RemoteConfigParameterGroupShape
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
