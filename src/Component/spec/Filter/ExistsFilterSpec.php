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
use Sylius\Component\Grid\Filter\ExistsFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class ExistsFilterTest extends TestCase
{
    private ExistsFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new ExistsFilter();
    }

    public function testImplementsFilterInterface(): void
    {
        $this->assertInstanceOf(FilterInterface::class, $this->filter);
    }

    public function testDoesNothingIfThereIsNoData(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $dataSource->expects($this->never())->method('restrict');

        $this->filter->apply($dataSource, 'anyField', null, []);
    }

    public function testFiltersOffAllDataWithProvidedFieldEqualToNull(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);

        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);
        $expressionBuilder->expects($this->once())
            ->method('isNotNull')
            ->with('fieldName')
            ->willReturn($expressionBuilder)
        ;

        $dataSource->expects($this->once())->method('restrict')->with($expressionBuilder);

        $this->filter->apply($dataSource, 'anyField', ExistsFilter::TRUE, ['field' => 'fieldName']);
    }

    public function testFiltersOffAllDataWithProvidedFieldNotEqualToNull(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);

        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);
        $expressionBuilder->expects($this->once())
            ->method('isNull')
            ->with('fieldName')
            ->willReturn($expressionBuilder)
        ;

        $dataSource->expects($this->once())->method('restrict')->with($expressionBuilder);

        $this->filter->apply($dataSource, 'anyField', ExistsFilter::FALSE, ['field' => 'fieldName']);
    }

    public function testFiltersOffDataByFilterNameIfFieldIsNotProvided(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);

        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);
        $expressionBuilder->expects($this->once())
            ->method('isNull')
            ->with('filterName')
            ->willReturn($expressionBuilder);

        $dataSource->expects($this->once())->method('restrict')->with($expressionBuilder);

        $this->filter->apply($dataSource, 'filterName', ExistsFilter::FALSE, []);
    }
}
