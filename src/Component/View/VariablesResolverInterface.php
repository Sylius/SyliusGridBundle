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
 * Resolves the values of a grid configuration (driver configuration, action options...) without any request:
 *
 *  - "$name" is replaced by the "name" variable (null when unknown), as sylius/resource-bundle does with the request values;
 *  - "@=expression" is evaluated with the expression language, the variables being available as "variables"
 *    (e.g. "@=variables['productId']"), besides the ones registered with AsExpressionVariables.
 *
 * @experimental
 */
interface VariablesResolverInterface
{
    /**
     * @template TKey of array-key
     *
     * @param array<TKey, mixed> $values
     * @param array<string, mixed> $variables
     * @param array<string, mixed> $expressionVariables Extra variables of the expressions (e.g. the "resource" of an action)
     *
     * @return array<TKey, mixed>
     */
    public function resolve(array $values, array $variables, array $expressionVariables = []): array;

    /**
     * @param array<string, mixed> $variables
     * @param array<string, mixed> $expressionVariables
     */
    public function resolveValue(mixed $value, array $variables, array $expressionVariables = []): mixed;
}
