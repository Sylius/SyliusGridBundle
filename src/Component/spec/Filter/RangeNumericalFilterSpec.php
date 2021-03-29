<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Grid\Filter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class RangeNumericalFilterSpec extends ObjectBehavior
{
    function it_implements_a_filter_interface(): void
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_filters_number_from(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->greaterThanOrEqual('number', '3')->willReturn('EXPR');
        $dataSource->restrict('EXPR', DataSourceInterface::CONDITION_HAVING_AND)->shouldBeCalled();

        $this->apply(
            $dataSource,
            'number',
            [
                'from' => '3',
            ],
            []
        );
    }

    function it_filters_number_to(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->lessThanOrEqual('number', '8')->willReturn('EXPR');
        $dataSource->restrict('EXPR', DataSourceInterface::CONDITION_HAVING_AND)->shouldBeCalled();

        $this->apply(
            $dataSource,
            'number',
            [
                'to' => '8',
            ],
            []
        );
    }

    function it_filters_number_from_to(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->greaterThanOrEqual('number', '2')->willReturn('EXPR1');
        $dataSource->restrict('EXPR1', DataSourceInterface::CONDITION_HAVING_AND)->shouldBeCalled();

        $expressionBuilder->lessThanOrEqual('number', '4')->willReturn('EXPR2');
        $dataSource->restrict('EXPR2', DataSourceInterface::CONDITION_HAVING_AND)->shouldBeCalled();

        $this->apply(
            $dataSource,
            'number',
            [
                'from' => '2',
                'to' => '4',
            ],
            []
        );
    }
}
