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
use Sylius\Component\Grid\Filter\NumericRangeFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class NumericRangeFilterTest extends TestCase
{
    private NumericRangeFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new NumericRangeFilter();
    }

    public function testImplementsFilterInterface(): void
    {
        $this->assertInstanceOf(FilterInterface::class, $this->filter);
    }

    public function testDoesNothingWhenThereIsNoData(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $dataSource->expects($this->never())->method('restrict');

        $this->filter->apply($dataSource, 'number', [], []);
    }

    public function testFiltersNumberFrom(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $builder = $this->createMock(ExpressionBuilderInterface::class);

        $dataSource->method('getExpressionBuilder')->willReturn($builder);
        $expr = new \stdClass();

        $builder
            ->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with('number', 3)
            ->willReturn($expr);

        $dataSource
            ->expects($this->once())
            ->method('restrict')
            ->with($expr);

        $this->filter->apply($dataSource, 'number', ['greaterThan' => '3'], []);
    }

    public function testFiltersNumberFromWithoutInclusiveFrom(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $builder = $this->createMock(ExpressionBuilderInterface::class);

        $dataSource->method('getExpressionBuilder')->willReturn($builder);
        $expr = new \stdClass();

        $builder
            ->expects($this->once())
            ->method('greaterThan')
            ->with('number', 7)
            ->willReturn($expr);

        $dataSource
            ->expects($this->once())
            ->method('restrict')
            ->with($expr);

        $this->filter->apply(
            $dataSource,
            'number',
            ['greaterThan' => '7'],
            ['inclusive_from' => false],
        );
    }

    public function testFiltersNumberTo(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $builder = $this->createMock(ExpressionBuilderInterface::class);

        $dataSource->method('getExpressionBuilder')->willReturn($builder);
        $expr = new \stdClass();

        $builder
            ->expects($this->once())
            ->method('lessThanOrEqual')
            ->with('number', 8)
            ->willReturn($expr);

        $dataSource
            ->expects($this->once())
            ->method('restrict')
            ->with($expr);

        $this->filter->apply($dataSource, 'number', ['lessThan' => '8'], []);
    }

    public function testFiltersNumberToWithoutInclusiveTo(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $builder = $this->createMock(ExpressionBuilderInterface::class);

        $dataSource->method('getExpressionBuilder')->willReturn($builder);
        $expr = new \stdClass();

        $builder
            ->expects($this->once())
            ->method('lessThan')
            ->with('number', 9)
            ->willReturn($expr);

        $dataSource
            ->expects($this->once())
            ->method('restrict')
            ->with($expr);

        $this->filter->apply(
            $dataSource,
            'number',
            ['lessThan' => '9'],
            ['inclusive_to' => false],
        );
    }

    public function testFiltersNumberInSpecifiedRange(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $builder = $this->createMock(ExpressionBuilderInterface::class);

        $dataSource->method('getExpressionBuilder')->willReturn($builder);

        $greaterExpr = new \stdClass();
        $lessExpr = new \stdClass();

        $builder
            ->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with('number', 12)
            ->willReturn($greaterExpr);

        $builder
            ->expects($this->once())
            ->method('lessThanOrEqual')
            ->with('number', 120)
            ->willReturn($lessExpr);

        $dataSource
            ->expects($this->exactly(2))
            ->method('restrict')
            ->with($this->logicalOr(
                $this->identicalTo($greaterExpr),
                $this->identicalTo($lessExpr),
            ));

        $this->filter->apply(
            $dataSource,
            'number',
            ['greaterThan' => '12.00', 'lessThan' => '120.00'],
            [],
        );
    }

    public function testAmountScaleAndModeCanBeConfigured(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $builder = $this->createMock(ExpressionBuilderInterface::class);
        $dataSource->method('getExpressionBuilder')->willReturn($builder);

        $greaterExpr = new \stdClass();
        $lessExpr = new \stdClass();

        $builder
            ->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with('number', 121)
            ->willReturn($greaterExpr);

        $builder
            ->expects($this->once())
            ->method('lessThanOrEqual')
            ->with('number', 259)
            ->willReturn($lessExpr);

        $dataSource
            ->expects($this->exactly(2))
            ->method('restrict')
            ->with($this->logicalOr(
                $this->identicalTo($greaterExpr),
                $this->identicalTo($lessExpr),
            ));

        $this->filter->apply(
            $dataSource,
            'number',
            ['greaterThan' => '120.78', 'lessThan' => '258.51'],
            ['scale' => 0, 'rounding_mode' => \NumberFormatter::ROUND_CEILING],
        );
    }

    public function testFiltersWithAllAvailableConfigurations(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $builder = $this->createMock(ExpressionBuilderInterface::class);
        $dataSource->method('getExpressionBuilder')->willReturn($builder);

        $greaterExpr = new \stdClass();
        $lessExpr = new \stdClass();

        $builder
            ->expects($this->once())
            ->method('greaterThan')
            ->with('number', 121)
            ->willReturn($greaterExpr);

        $builder
            ->expects($this->once())
            ->method('lessThanOrEqual')
            ->with('number', 259)
            ->willReturn($lessExpr);

        $dataSource
            ->expects($this->exactly(2))
            ->method('restrict')
            ->with($this->logicalOr(
                $this->identicalTo($greaterExpr),
                $this->identicalTo($lessExpr),
            ));

        $this->filter->apply(
            $dataSource,
            'number',
            ['greaterThan' => '120.78', 'lessThan' => '258.51'],
            [
                'scale' => 0,
                'rounding_mode' => \NumberFormatter::ROUND_CEILING,
                'inclusive_to' => true,
                'inclusive_from' => false,
            ],
        );
    }

    public function testAmountScaleCanBeConfigured(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $builder = $this->createMock(ExpressionBuilderInterface::class);
        $dataSource->method('getExpressionBuilder')->willReturn($builder);

        $greaterExpr = new \stdClass();
        $lessExpr = new \stdClass();

        $builder
            ->expects($this->once())
            ->method('greaterThan')
            ->with('number', 234520)
            ->willReturn($greaterExpr);

        $builder
            ->expects($this->once())
            ->method('lessThanOrEqual')
            ->with('number', 122120)
            ->willReturn($lessExpr);

        $dataSource
            ->expects($this->exactly(2))
            ->method('restrict')
            ->with($this->logicalOr(
                $this->identicalTo($greaterExpr),
                $this->identicalTo($lessExpr),
            ));

        $this->filter->apply(
            $dataSource,
            'number',
            ['greaterThan' => '234.52', 'lessThan' => '122.12'],
            [
                'scale' => 3,
                'rounding_mode' => \NumberFormatter::ROUND_CEILING,
                'inclusive_to' => true,
                'inclusive_from' => false,
            ],
        );
    }
}
