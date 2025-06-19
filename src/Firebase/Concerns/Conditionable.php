<?php

namespace Kreait\Firebase\Concerns;

trait Conditionable
{
    /**
     * Conditionally apply a callback if the given condition is true.
     *
     * @param bool $condition The condition to evaluate.
     * @param callable $callback The callback to execute if condition is true.
     * @param callable|null $default The fallback callback if condition is false.
     * @return $this|mixed
     */
    public function when(
        bool $condition,
        callable $callback,
        ?callable $default = null,
    ): mixed {
        if ($condition) {
            return $callback($this);
        }

        if ($default !== null) {
            return $default($this);
        }

        return $this;
    }

    /**
     * Conditionally apply a callback if the given condition is false.
     *
     * @param bool $condition The condition to evaluate.
     * @param callable $callback The callback to execute if condition is false.
     * @param callable|null $default The fallback callback if condition is true.
     * @return $this|mixed
     */
    public function unless(
        bool $condition,
        callable $callback,
        ?callable $default = null,
    ): mixed {
        return $this->when(! $condition, $callback, $default);
    }
}
