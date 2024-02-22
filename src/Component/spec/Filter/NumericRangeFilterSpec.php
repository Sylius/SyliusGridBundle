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

namespace spec\Sylius\Component\Grid\Filter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class NumericRangeFilterSpec extends ObjectBehavior
{
    function it_implements_a_filter_interface(): void
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_does_nothing_when_there_is_no_data(DataSourceInterface $dataSource): void
    {
        $dataSource->restrict(Argument::any())->shouldNotBeCalled();

        $this->apply(
            $dataSource,
            'number',
            [],
            [],
        );
    }

    function it_filters_number_from(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
    ): void {
        $expressionBuilder->greaterThanOrEqual('number', 3)->willReturn('EXPR');
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $dataSource->getExpressionBuilder()->shouldBeCalledOnce();
        $dataSource->restrict('EXPR')->shouldBeCalledOnce();

        $this->apply(
            $dataSource,
            'number',
            [
                'greaterThan' => '3',
            ],
            [],
        );
    }

    function it_filters_number_from_without_inclusive_from(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
    ): void {
        $expressionBuilder->greaterThan('number', 7)->willReturn('EXPR');
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $dataSource->getExpressionBuilder()->shouldBeCalledOnce();
        $dataSource->restrict('EXPR')->shouldBeCalledOnce();

        $this->apply(
            $dataSource,
            'number',
            [
                'greaterThan' => '7',
            ],
            [
                'inclusive_from' => false,
            ],
        );
    }

    function it_filters_number_to(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);
        $expressionBuilder->lessThanOrEqual('number', 8)->willReturn('EXPR');

        $dataSource->getExpressionBuilder()->shouldBeCalledOnce();
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply(
            $dataSource,
            'number',
            [
                'lessThan' => '8',
            ],
            [],
        );
    }

    function it_filters_number_to_without_inclusive_to(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);
        $expressionBuilder->lessThan('number', 9)->willReturn('EXPR');

        $dataSource->getExpressionBuilder()->shouldBeCalledOnce();
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply(
            $dataSource,
            'number',
            [
                'lessThan' => '9',
            ],
            [
                'inclusive_to' => false,
            ],
        );
    }

    function it_filters_money_in_specified_range(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);
        $expressionBuilder->greaterThanOrEqual('number', 12)->willReturn('EXPR2');
        $expressionBuilder->lessThanOrEqual('number', 120)->willReturn('EXPR3');

        $dataSource->getExpressionBuilder()->shouldBeCalledOnce();
        $dataSource->restrict('EXPR2')->shouldBeCalledOnce();
        $dataSource->restrict('EXPR3')->shouldBeCalledOnce();

        $this->apply(
            $dataSource,
            'number',
            [
                'greaterThan' => '12.00',
                'lessThan' => '120.00',
            ],
            [],
        );
    }

    function its_amount_scale_and_mode_can_be_configured(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);
        $expressionBuilder->greaterThanOrEqual('number', 121)->willReturn('EXPR');
        $expressionBuilder->lessThanOrEqual('number', 259)->willReturn('EXPR1');

        $dataSource->getExpressionBuilder()->shouldBeCalledOnce();
        $dataSource->restrict('EXPR')->shouldBeCalledOnce();
        $dataSource->restrict('EXPR1')->shouldBeCalledOnce();

        $this->apply(
            $dataSource,
            'number',
            [
                'greaterThan' => '120.78',
                'lessThan' => '258.51',
            ],
            [
                'scale' => 0,
                'rounding_mode' => \NumberFormatter::ROUND_CEILING,
            ],
        );
    }

    function it_filters_with_all_available_configurations(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);
        $expressionBuilder->greaterThan('number', 121)->willReturn('EXPR');
        $expressionBuilder->lessThanOrEqual('number', 259)->willReturn('EXPR1');

        $dataSource->getExpressionBuilder()->shouldBeCalledOnce();
        $dataSource->restrict('EXPR')->shouldBeCalledOnce();
        $dataSource->restrict('EXPR1')->shouldBeCalledOnce();

        $this->apply(
            $dataSource,
            'number',
            [
                'greaterThan' => '120.78',
                'lessThan' => '258.51',
            ],
            [
                'scale' => 0,
                'rounding_mode' => \NumberFormatter::ROUND_CEILING,
                'inclusive_to' => true,
                'inclusive_from' => false,
            ],
        );
    }

    function its_amount_scale_can_be_configured(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);
        $expressionBuilder->greaterThan('number', 234520)->willReturn('EXPR');
        $expressionBuilder->lessThanOrEqual('number', 122120)->willReturn('EXPR1');

        $dataSource->getExpressionBuilder()->shouldBeCalledOnce();
        $dataSource->restrict('EXPR')->shouldBeCalledOnce();
        $dataSource->restrict('EXPR1')->shouldBeCalledOnce();

        $this->apply(
            $dataSource,
            'number',
            [
                'greaterThan' => '234.52',
                'lessThan' => '122.12',
            ],
            [
                'scale' => 3,
                'rounding_mode' => \NumberFormatter::ROUND_CEILING,
                'inclusive_to' => true,
                'inclusive_from' => false,
            ],
        );
    }
}
