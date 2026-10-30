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

namespace Sylius\Component\Grid\Tests\Unit\View;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\ExpressionLanguage\ExpressionEvaluatorInterface;
use Sylius\Component\Grid\View\VariablesResolver;

final class VariablesResolverTest extends TestCase
{
    public function testResolvesTheVariablesRecursively(): void
    {
        $resolver = new VariablesResolver();

        $this->assertSame(
            ['repository' => ['method' => 'createByProductId', 'arguments' => ['en_US', 42, null, 10, true]]],
            $resolver->resolve(
                ['repository' => ['method' => 'createByProductId', 'arguments' => ['en_US', '$productId', '$missing', 10, true]]],
                ['productId' => 42],
            ),
        );
    }

    public function testResolvesASingleValue(): void
    {
        $resolver = new VariablesResolver();

        $this->assertSame(42, $resolver->resolveValue('$productId', ['productId' => 42]));
        $this->assertSame('productId', $resolver->resolveValue('productId', ['productId' => 42]));
        $this->assertSame(['$productId'], $resolver->resolveValue(['$productId'], ['productId' => 42]));
    }

    public function testEvaluatesTheExpressionsWithTheVariables(): void
    {
        $expressionEvaluator = $this->createMock(ExpressionEvaluatorInterface::class);
        $expressionEvaluator
            ->expects($this->once())
            ->method('evaluateExpression')
            ->with("variables['productId'] + resource.id", ['resource' => 'the resource', 'variables' => ['productId' => 42]])
            ->willReturn(49)
        ;

        $resolver = new VariablesResolver($expressionEvaluator);

        $this->assertSame(
            ['sum' => 49, 'productId' => 42],
            $resolver->resolve(
                ['sum' => "@=variables['productId'] + resource.id", 'productId' => '$productId'],
                ['productId' => 42],
                ['resource' => 'the resource'],
            ),
        );
    }

    public function testCannotEvaluateAnExpressionWithoutExpressionEvaluator(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The "@=variables[\'productId\']" expression cannot be evaluated without the expression language.');

        (new VariablesResolver())->resolveValue("@=variables['productId']", ['productId' => 42]);
    }
}
