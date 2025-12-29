<?php

declare(strict_types=1);

namespace Kreait\Firebase\Valinor\Transformer;

/**
 * @internal
 *
 * @see https://valinor.cuyz.io/latest/serialization/common-transformers-examples/#transforming-property-name-to-snake_case
 */
final class CamelToSnakeCaseTransformer
{
    public function __invoke(object $object, callable $next): mixed
    {
        $result = $next();

        if (! is_array($result)) {
            return $result;
        }

        $snakeCased = [];

        foreach ($result as $key => $value) {
            $newKey = preg_replace('/[A-Z]/', '_$0', lcfirst((string) $key));
            assert(is_string($newKey));

            $newKey = strtolower($newKey);

            $snakeCased[$newKey] = $value;
        }

        return $snakeCased;
    }
}
