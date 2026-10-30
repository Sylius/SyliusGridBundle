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

namespace Sylius\Component\Grid\Tests\Unit\ExpressionLanguage;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\ExpressionLanguage\ExpressionEvaluator;
use Sylius\Component\Grid\ExpressionLanguage\ExpressionLanguageFactory;
use Sylius\Component\Grid\ExpressionLanguage\VariablesCollectionAggregate;
use Sylius\Component\Grid\ExpressionLanguage\VariablesCollectionInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

final class ExpressionEvaluatorTest extends TestCase
{
    public function testEvaluatesExpressionsWithTheProvidedFunctionsAndVariables(): void
    {
        $provider = $this->createMock(ExpressionFunctionProviderInterface::class);
        $provider->method('getFunctions')->willReturn([ExpressionFunction::fromPhp('strtoupper', 'shout')]);

        $evaluator = new ExpressionEvaluator(
            (new ExpressionLanguageFactory([$provider]))(),
            new VariablesCollectionAggregate([
                $this->createVariablesCollection(['greeting' => 'hello', 'name' => 'overridden']),
                $this->createVariablesCollection(['punctuation' => '!']),
            ]),
        );

        $this->assertSame('HELLO GRID!', $evaluator->evaluateExpression("shout(greeting ~ ' ' ~ name) ~ punctuation", ['name' => 'grid']));
    }

    public function testAggregatesNoVariablesByDefault(): void
    {
        $this->assertSame([], (new VariablesCollectionAggregate())->getCollection());
    }

    /** @param array<string, mixed> $variables */
    private function createVariablesCollection(array $variables): VariablesCollectionInterface
    {
        $variablesCollection = $this->createMock(VariablesCollectionInterface::class);
        $variablesCollection->method('getCollection')->willReturn($variables);

        return $variablesCollection;
    }
}
