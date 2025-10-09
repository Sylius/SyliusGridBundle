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
use Sylius\Component\Grid\Filter\EntityFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class EntityFilterTest extends TestCase
{
    private EntityFilter $entityFilter;

    protected function setUp(): void
    {
        $this->entityFilter = new EntityFilter();
    }

    public function testImplementsAFilterInterface(): void
    {
        $this->assertInstanceOf(FilterInterface::class, $this->entityFilter);
    }

    public function testFiltersById(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())
            ->method('getExpressionBuilder')
            ->willReturn($expressionBuilderMock)
        ;
        $expressionBuilderMock->expects($this->once())
            ->method('equals')
            ->with('entity', '7')
            ->willReturn('EXPR1')
        ;
        $expressionBuilderMock->expects($this->once())
            ->method('orX')
            ->with('EXPR1')
            ->willReturn('EXPR')
        ;
        $dataSourceMock->expects($this->once())
            ->method('restrict')
            ->with('EXPR')
        ;

        $this->entityFilter->apply($dataSourceMock, 'entity', '7', []);
    }

    public function testFiltersWithMultipleIds(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())
            ->method('getExpressionBuilder')
            ->willReturn($expressionBuilderMock)
        ;

        $expressionBuilderMock->expects($this->exactly(2))
            ->method('equals')
            ->willReturnMap([['entity', '4', 'EXPR1'], ['entity', '2', 'EXPR2']])
        ;
        $expressionBuilderMock->expects($this->once())
            ->method('orX')
            ->with('EXPR1', 'EXPR2')
            ->willReturn('EXPR')
        ;
        $dataSourceMock->expects($this->once())
            ->method('restrict')
            ->with('EXPR')
        ;

        $this->entityFilter->apply($dataSourceMock, 'entity', ['4', '2'], []);
    }

    public function testDoesNotFiltersWhenDataIdIsNotDefined(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $expressionBuilderMock->expects($this->never())
            ->method('equals')
        ;
        $dataSourceMock->expects($this->never())
            ->method('restrict')
        ;

        $this->entityFilter->apply($dataSourceMock, 'entity', '', []);
    }
}
