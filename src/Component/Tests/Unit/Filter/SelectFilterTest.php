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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Filter\SelectFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class SelectFilterTest extends TestCase
{
    private SelectFilter $selectFilter;

    protected function setUp(): void
    {
        $this->selectFilter = new SelectFilter();
    }

    public function testImplementsAFilterInterface(): void
    {
        $this->assertInstanceOf(FilterInterface::class, $this->selectFilter);
    }

    public function testFiltersById(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);
        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);
        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('equals')->with('select', '7')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');
        $this->selectFilter->apply($dataSourceMock, 'select', '7', []);
    }

    public function testFiltersWithMultipleIds(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);
        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);
        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('in')->with('select', ['4', '2'])->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');
        $this->selectFilter->apply($dataSourceMock, 'select', ['4', '2'], []);
    }

    public function testDoesNotFiltersWhenDataIdIsNotDefined(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $expressionBuilderMock->expects($this->never())->method('equals');
        $dataSourceMock->expects($this->never())->method('restrict');

        $this->selectFilter->apply($dataSourceMock, 'select', '', []);
    }
}
