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
use Sylius\Component\Grid\Data\MemberOfAwareExpressionBuilderInterface;
use Sylius\Component\Grid\Filter\StringFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class StringFilterTest extends TestCase
{
    private StringFilter $stringFilter;

    protected function setUp(): void
    {
        $this->stringFilter = new StringFilter();
    }

    public function testImplementsFilterInterface(): void
    {
        $this->assertInstanceOf(FilterInterface::class, $this->stringFilter);
    }

    public function testFiltersWithLikeByDefault(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('like')->with('firstName', '%John%')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'firstName', 'John', []);
    }

    public function testFiltersEqualStrings(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('equals')->with('firstName', 'John')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_EQUAL, 'value' => 'John'], []);
    }

    public function testFiltersNotEqualStrings(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('notEquals')->with('firstName', 'John')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_NOT_EQUAL, 'value' => 'John'], []);
    }

    public function testFiltersDataContainingEmptyStrings(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('isNull')->with('firstName')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_EMPTY], []);
    }

    public function testFiltersDataContainingNotEmptyStrings(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('isNotNull')->with('firstName')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_NOT_EMPTY], []);
    }

    public function testFiltersDataContainingAString(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('like')->with('firstName', '%John%')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_CONTAINS, 'value' => 'John'], []);
    }

    public function testFiltersDataNotContainingAString(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('notLike')->with('firstName', '%John%')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_NOT_CONTAINS, 'value' => 'John'], []);
    }

    public function testFiltersDataStartingWithAString(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('like')->with('firstName', 'John%')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_STARTS_WITH, 'value' => 'John'], []);
    }

    public function testFiltersDataEndingWithAString(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('like')->with('firstName', '%John')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_ENDS_WITH, 'value' => 'John'], []);
    }

    public function testFiltersDataContainingOneOfStrings(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('in')->with('firstName', ['John', 'Paul', 'Rick'])->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_IN, 'value' => 'John, Paul,Rick'], []);
    }

    public function testFiltersDataContainingValueBeingMemberOfField(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var MemberOfAwareExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(MemberOfAwareExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('memberOf')->with('Rick', 'firstName')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_MEMBER_OF, 'value' => 'Rick'], []);
    }

    public function testFiltersDataContainingNoneOfStrings(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('notIn')->with('firstName', ['John', 'Paul', 'Rick'])->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_NOT_IN, 'value' => 'John, Paul,Rick'], []);
    }

    public function testFiltersInMultipleFields(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->exactly(2))->method('like')->willReturnMap([['firstName', '%John%', 'EXPR1'], ['lastName', '%John%', 'EXPR2']]);
        $expressionBuilderMock->expects($this->once())->method('orX')->with('EXPR1', 'EXPR2')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'name', 'John', ['fields' => ['firstName', 'lastName']]);
    }

    public function testFiltersTranslationFields(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('like')->with('translation.name', '%John%')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'name', 'John', ['fields' => ['translation.name']]);
    }

    public function testThrowsAnExceptionIfTypeIsUnknown(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $this->expectException(\InvalidArgumentException::class);

        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => 'UNKNOWN_TYPE', 'value' => 'John'], []);
    }

    public function testIgnoresFilterIfItsValueIsEmptyAndTheFilterDependsOnIt(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->exactly(8))->method('getExpressionBuilder')->willReturn($expressionBuilderMock);

        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_CONTAINS, 'value' => ''], []);
        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_ENDS_WITH, 'value' => ''], []);
        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_EQUAL, 'value' => ''], []);
        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_NOT_EQUAL, 'value' => ''], []);
        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_IN, 'value' => ''], []);
        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_NOT_CONTAINS, 'value' => ''], []);
        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_NOT_IN, 'value' => ''], []);
        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_STARTS_WITH, 'value' => ''], []);
    }

    public function testDoesNotIgnoreFilterIfItsValueIsZero(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);

        $expressionBuilderMock->expects($this->once())->method('like')->with('firstName', '%0%')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'firstName', ['type' => StringFilter::TYPE_CONTAINS, 'value' => '0'], []);
    }

    public function testUsesScalarDataAsValue(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('like')->with('firstName', '%John%')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'firstName', 'John', []);
    }

    public function testUsesTypeFromOptionsIfSet(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var ExpressionBuilderInterface|MockObject $expressionBuilderMock */
        $expressionBuilderMock = $this->createMock(ExpressionBuilderInterface::class);

        $dataSourceMock->expects($this->once())->method('getExpressionBuilder')->willReturn($expressionBuilderMock);
        $expressionBuilderMock->expects($this->once())->method('equals')->with('firstName', 'John')->willReturn('EXPR');
        $dataSourceMock->expects($this->once())->method('restrict')->with('EXPR');

        $this->stringFilter->apply($dataSourceMock, 'firstName', 'John', ['type' => StringFilter::TYPE_EQUAL]);
    }
}
