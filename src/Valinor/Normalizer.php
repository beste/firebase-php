<?php

declare(strict_types=1);

namespace Kreait\Firebase\Valinor;

use CuyZ\Valinor\Normalizer\ArrayNormalizer;
use CuyZ\Valinor\Normalizer\Format;
use CuyZ\Valinor\NormalizerBuilder;
use Kreait\Firebase\Valinor\Transformer\CamelToSnakeCaseTransformer;

/**
 * @internal
 */
final class Normalizer
{
    public NormalizerBuilder $normalizerBuilder;

    public ?ArrayNormalizer $arrayNormalizer = null;

    public function __construct(private readonly mixed $cache = null, ?NormalizerBuilder $builder = null)
    {
        $builder ??= new NormalizerBuilder();

        if ($cache !== null) {
            $builder = $builder->withCache($this->cache);
        }

        $this->normalizerBuilder = $builder;
    }

    public function withTransformer(callable $transformer): self
    {
        $builder = $this->normalizerBuilder->registerTransformer($transformer); // @phpstan-ignore-line argument.type

        return new self($this->cache, $builder);
    }

    public function camelToSnakeCase(): self
    {
        return $this->withTransformer(new CamelToSnakeCaseTransformer());
    }

    /**
     * @return array<mixed>
     */
    public function toArray(mixed $value): array
    {
        $this->arrayNormalizer ??= $this->normalizerBuilder->normalizer(Format::array());

        $result = $this->arrayNormalizer->normalize($value);
        assert(is_array($result));

        return $result;
    }
}
