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

namespace Sylius\Component\Grid\Tests\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Filter\MoneyFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class MoneyFilterTest extends TestCase
{
    private MoneyFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new MoneyFilter();
    }

    public function testIsInitializable(): void
    {
        $this->assertInstanceOf(MoneyFilter::class, $this->filter);
    }

    public function testImplementsFilterInterface(): void
    {
        $this->assertInstanceOf(FilterInterface::class, $this->filter);
    }

    public function testDoesNothingWhenThereIsNoData(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $dataSource->expects($this->never())->method('restrict');

        $this->filter->apply($dataSource, 'total', [], ['currency_field' => 'currencyCode']);
    }

    public function testFiltersByTotalAloneInAllCurrenciesWhenNoneHasBeenGiven(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);

        $expressionBuilder
            ->expects($this->exactly(2))
            ->method('greaterThan')
            ->with('total', 1200)
            ->willReturnOnConsecutiveCalls('EXPR1', 'EXPR2');

        $restricted = [];
        $dataSource
            ->expects($this->exactly(2))
            ->method('restrict')
            ->willReturnCallback(static function ($expr) use (&$restricted): void {
                $restricted[] = $expr;
            });

        $this->filter->apply($dataSource, 'total', [
            'greaterThan' => '12.00',
            'lessThan' => '',
            'currency' => '',
        ], ['currency_field' => 'currencyCode']);

        $this->filter->apply($dataSource, 'total', [
            'greaterThan' => '12.00',
            'lessThan' => '',
        ], ['currency_field' => 'currencyCode']);

        self::assertSame(['EXPR1', 'EXPR2'], $restricted);
    }

    public function testFiltersByGivenCurrency(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);

        $expressionBuilder
            ->expects($this->exactly(2))
            ->method('equals')
            ->with('currencyCode', 'GBP')
            ->willReturn('EXPR');

        $restricted = [];
        $dataSource
            ->expects($this->exactly(2))
            ->method('restrict')
            ->willReturnCallback(static function ($expr) use (&$restricted): void {
                $restricted[] = $expr;
            });

        $this->filter->apply($dataSource, 'total', [
            'currency' => 'GBP',
        ], ['currency_field' => 'currencyCode']);

        $this->filter->apply($dataSource, 'total', [
            'greaterThan' => '',
            'lessThan' => '',
            'currency' => 'GBP',
        ], ['currency_field' => 'currencyCode']);

        self::assertSame(['EXPR', 'EXPR'], $restricted);
    }

    public function testFiltersMoneyGreaterThan(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);

        $expr1 = new \stdClass();
        $expr2 = new \stdClass();

        $expressionBuilder->method('equals')->with('currencyCode', 'GBP')->willReturn($expr1);
        $expressionBuilder->method('greaterThan')->with('total', 1200)->willReturn($expr2);

        $calls = [];
        $dataSource->expects($this->exactly(2))
            ->method('restrict')
            ->willReturnCallback(static function ($expr) use (&$calls): void {
                $calls[] = $expr;
            });

        $this->filter->apply($dataSource, 'total', [
            'greaterThan' => '12.00',
            'lessThan' => '',
            'currency' => 'GBP',
        ], ['currency_field' => 'currencyCode']);

        self::assertSame([$expr1, $expr2], $calls);
    }

    public function testFiltersMoneyLessThan(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);

        $currencyExpr = new \stdClass();
        $lessExpr = new \stdClass();

        $expressionBuilder->method('equals')->with('currencyCode', 'GBP')->willReturn($currencyExpr);
        $expressionBuilder->method('lessThan')->with('total', 12000)->willReturn($lessExpr);

        $calls = [];
        $dataSource->expects($this->exactly(2))
            ->method('restrict')
            ->willReturnCallback(static function ($expr) use (&$calls): void {
                $calls[] = $expr;
            });

        $this->filter->apply($dataSource, 'total', [
            'greaterThan' => '',
            'lessThan' => '120.00',
            'currency' => 'GBP',
        ], ['currency_field' => 'currencyCode']);

        self::assertSame([$currencyExpr, $lessExpr], $calls);
    }

    public function testFiltersMoneyInSpecifiedRange(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);

        $currencyExpr = new \stdClass();
        $greaterExpr = new \stdClass();
        $lessExpr = new \stdClass();

        $expressionBuilder->method('equals')->with('currencyCode', 'GBP')->willReturn($currencyExpr);
        $expressionBuilder->method('greaterThan')->with('total', 1200)->willReturn($greaterExpr);
        $expressionBuilder->method('lessThan')->with('total', 12000)->willReturn($lessExpr);

        $calls = [];
        $dataSource->expects($this->exactly(3))
            ->method('restrict')
            ->willReturnCallback(static function ($expr) use (&$calls): void {
                $calls[] = $expr;
            });

        $this->filter->apply($dataSource, 'total', [
            'greaterThan' => '12.00',
            'lessThan' => '120.00',
            'currency' => 'GBP',
        ], ['currency_field' => 'currencyCode']);

        self::assertSame([$currencyExpr, $greaterExpr, $lessExpr], $calls);
    }

    public function testAmountScaleCanBeConfigured(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);

        $currencyExpr = new \stdClass();
        $greaterExpr = new \stdClass();
        $lessExpr = new \stdClass();

        $expressionBuilder->method('equals')->with('currencyCode', 'GBP')->willReturn($currencyExpr);
        $expressionBuilder->method('greaterThan')->with('total', 1_200_000)->willReturn($greaterExpr);
        $expressionBuilder->method('lessThan')->with('total', 12_000_000)->willReturn($lessExpr);

        $calls = [];
        $dataSource->expects($this->exactly(3))
            ->method('restrict')
            ->willReturnCallback(static function ($expr) use (&$calls): void {
                $calls[] = $expr;
            });

        $this->filter->apply($dataSource, 'total', [
            'greaterThan' => '12',
            'lessThan' => '120',
            'currency' => 'GBP',
        ], ['currency_field' => 'currencyCode', 'scale' => 5]);

        self::assertSame([$currencyExpr, $greaterExpr, $lessExpr], $calls);
    }
}
