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
use Sylius\Component\Grid\Filter\DateFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class DateFilterTest extends TestCase
{
    private DateFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new DateFilter();
    }

    public function testImplementsFilterInterface(): void
    {
        $this->assertInstanceOf(FilterInterface::class, $this->filter);
    }

    public function testFiltersDateFrom(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);

        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);
        $expressionBuilder->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with('checkoutCompletedAt', '2016-12-05 08:00')
            ->willReturn('EXPR');

        $dataSource->expects($this->once())->method('restrict')->with('EXPR');

        $this->filter->apply(
            $dataSource,
            'checkoutCompletedAt',
            [
                'from' => [
                    'date' => '2016-12-05',
                    'time' => '08:00',
                ],
            ],
            [],
        );
    }

    public function testFiltersDateFromNotInclusive(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);

        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);
        $expressionBuilder->expects($this->once())
            ->method('greaterThan')
            ->with('checkoutCompletedAt', '2016-12-05 08:00')
            ->willReturn('EXPR');

        $dataSource->expects($this->once())->method('restrict')->with('EXPR');

        $this->filter->apply(
            $dataSource,
            'checkoutCompletedAt',
            [
                'from' => [
                    'date' => '2016-12-05',
                    'time' => '08:00',
                ],
                'to' => [
                    'date' => '',
                    'time' => '',
                ],
            ],
            ['inclusive_from' => false],
        );
    }

    public function testFiltersDateFromWithDefaultTime(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);

        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);
        $expressionBuilder->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with('checkoutCompletedAt', '2016-12-05 00:00')
            ->willReturn('EXPR');

        $dataSource->expects($this->once())->method('restrict')->with('EXPR');

        $this->filter->apply(
            $dataSource,
            'checkoutCompletedAt',
            [
                'from' => [
                    'date' => '2016-12-05',
                    'time' => '',
                ],
                'to' => [
                    'date' => '',
                    'time' => '',
                ],
            ],
            [],
        );
    }

    public function testFiltersDateTo(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);

        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);
        $expressionBuilder->expects($this->once())
            ->method('lessThan')
            ->with('checkoutCompletedAt', '2016-12-06 08:00')
            ->willReturn('EXPR');

        $dataSource->expects($this->once())->method('restrict')->with('EXPR');

        $this->filter->apply(
            $dataSource,
            'checkoutCompletedAt',
            [
                'to' => [
                    'date' => '2016-12-06',
                    'time' => '08:00',
                ],
            ],
            [],
        );
    }

    public function testFiltersDateToInclusive(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);

        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);
        $expressionBuilder->expects($this->once())
            ->method('lessThanOrEqual')
            ->with('checkoutCompletedAt', '2016-12-06 08:00')
            ->willReturn('EXPR');

        $dataSource->expects($this->once())->method('restrict')->with('EXPR');

        $this->filter->apply(
            $dataSource,
            'checkoutCompletedAt',
            [
                'from' => [
                    'date' => '',
                    'time' => '',
                ],
                'to' => [
                    'date' => '2016-12-06',
                    'time' => '08:00',
                ],
            ],
            ['inclusive_to' => true],
        );
    }

    public function testFiltersDateToWithDefaultTime(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);

        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);
        $expressionBuilder->expects($this->once())
            ->method('lessThan')
            ->with('checkoutCompletedAt', '2016-12-06 23:59')
            ->willReturn('EXPR');

        $dataSource->expects($this->once())->method('restrict')->with('EXPR');

        $this->filter->apply(
            $dataSource,
            'checkoutCompletedAt',
            [
                'to' => [
                    'date' => '2016-12-06',
                    'time' => '',
                ],
            ],
            [],
        );
    }

    public function testFiltersDateFromTo(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);

        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);

        $expressionBuilder->expects($this->exactly(2))
            ->method($this->logicalOr('greaterThanOrEqual', 'lessThan'))
            ->willReturnCallback(function (string $field, string $value) {
                return $value === '2016-12-05 08:00' ? 'EXPR1' : 'EXPR2';
            });

        $invokedCount = $this->exactly(2);
        $dataSource->expects($invokedCount)
            ->method('restrict')
            ->willReturnCallback(function (string $expression) use ($invokedCount) {
                $numberOfInvocationMethod = method_exists($invokedCount, 'numberOfInvocations') ? 'numberOfInvocations' : 'getInvocationCount';

                if ($invokedCount->$numberOfInvocationMethod() === 1) {
                    $this->assertEquals('EXPR1', $expression);
                }

                if ($invokedCount->$numberOfInvocationMethod() === 2) {
                    $this->assertEquals('EXPR2', $expression);
                }
            });

        $this->filter->apply(
            $dataSource,
            'checkoutCompletedAt',
            [
                'from' => [
                    'date' => '2016-12-05',
                    'time' => '08:00',
                ],
                'to' => [
                    'date' => '2016-12-06',
                    'time' => '08:00',
                ],
            ],
            [],
        );
    }
}
