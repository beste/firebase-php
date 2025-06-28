<?php

declare(strict_types=1);

namespace Kreait\Firebase\Valinor\Converter;

use Traversable;

/**
 * @internal
 *
 * @see https://valinor.cuyz.io/latest/how-to/convert-input/#converting-keys-format-from-snake_case-to-camelcase
 */
final class SnakeCaseToCamelCaseConverter
{
    public function __invoke(mixed $values, callable $next): object
    {
        if ($values instanceof Traversable) {
            $values = iterator_to_array($values);
        }

        if (!is_array($values)) {
            return $next($values);
        }

        $camelCaseConverted = array_combine(
            array_map(
                fn($key): string => lcfirst(str_replace('_', '', ucwords((string) $key, '_'))),
                array_keys($values),
            ),
            $values,
        );

        return $next($camelCaseConverted);
    }
}
