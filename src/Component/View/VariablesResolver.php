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

use Sylius\Component\Grid\ExpressionLanguage\ExpressionEvaluatorInterface;

/**
 * @experimental
 */
final class VariablesResolver implements VariablesResolverInterface
{
    public function __construct(private readonly ?ExpressionEvaluatorInterface $expressionEvaluator = null)
    {
    }

    public function resolve(array $values, array $variables, array $expressionVariables = []): array
    {
        return array_map(
            fn (mixed $value): mixed => is_array($value)
                ? $this->resolve($value, $variables, $expressionVariables)
                : $this->resolveValue($value, $variables, $expressionVariables),
            $values,
        );
    }

    public function resolveValue(mixed $value, array $variables, array $expressionVariables = []): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        if (str_starts_with($value, '@=')) {
            return $this->evaluateExpression(substr($value, 2), $variables, $expressionVariables);
        }

        if (str_starts_with($value, '$')) {
            return $variables[substr($value, 1)] ?? null;
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $variables
     * @param array<string, mixed> $expressionVariables
     */
    private function evaluateExpression(string $expression, array $variables, array $expressionVariables): mixed
    {
        if (null === $this->expressionEvaluator) {
            throw new \LogicException(sprintf('The "@=%s" expression cannot be evaluated without the expression language. Try running "composer require symfony/expression-language".', $expression));
        }

        return $this->expressionEvaluator->evaluateExpression($expression, array_merge($expressionVariables, ['variables' => $variables]));
    }
}
