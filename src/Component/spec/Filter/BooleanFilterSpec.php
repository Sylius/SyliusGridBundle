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
use Sylius\Component\Grid\Filter\BooleanFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class BooleanFilterTest extends TestCase
{
    private BooleanFilter $booleanFilter;

    protected function setUp(): void
    {
        $this->booleanFilter = new BooleanFilter();
    }

    public function testImplementsFilterInterface(): void
    {
        $this->assertInstanceOf(FilterInterface::class, $this->booleanFilter);
    }

    public function testFiltersTrueBooleanValues(): void
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
            ->with('enabled', true)
            ->willReturn('EXPR')
        ;
        $dataSourceMock->expects($this->once())
            ->method('restrict')
            ->with('EXPR')
        ;

        $this->booleanFilter->apply($dataSourceMock, 'enabled', BooleanFilter::TRUE, []);
    }

    public function testFiltersFalseBooleanValues(): void
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
            ->with('enabled', false)
            ->willReturn('EXPR')
        ;
        $dataSourceMock->expects($this->once())
            ->method('restrict')
            ->with('EXPR')
        ;

        $this->booleanFilter->apply($dataSourceMock, 'enabled', BooleanFilter::FALSE, []);
    }
}
