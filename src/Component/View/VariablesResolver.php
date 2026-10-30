<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Grid\View;

/**
 * Resolves the "$name" values, as the request parameters are resolved by sylius/resource-bundle,
 * but from the given variables: an unknown variable is resolved to null.
 *
 * @experimental
 *
 * @internal
 */
final class VariablesResolver
{
    /**
     * @template TKey of array-key
     *
     * @param array<TKey, mixed> $values
     * @param array<string, mixed> $variables
     *
     * @return array<TKey, mixed>
     */
    public function resolve(array $values, array $variables): array
    {
        return array_map(
            fn (mixed $value): mixed => is_array($value) ? $this->resolve($value, $variables) : $this->resolveValue($value, $variables),
            $values,
        );
    }

    /** @param array<string, mixed> $variables */
    public function resolveValue(mixed $value, array $variables): mixed
    {
        if (!is_string($value) || !str_starts_with($value, '$')) {
            return $value;
        }

        return $variables[substr($value, 1)] ?? null;
    }
}
