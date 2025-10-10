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

namespace Sylius\Component\Grid\Tests\Unit\Sorting;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Sorting\Sorter;
use Sylius\Component\Grid\Sorting\SorterInterface;
use Sylius\Component\Grid\Validation\FieldValidatorInterface;
use Sylius\Component\Grid\Validation\SortingParametersValidatorInterface;

final class SorterTest extends TestCase
{
    private Sorter $sorter;

    private SortingParametersValidatorInterface $sortingValidator;

    private FieldValidatorInterface $fieldValidator;

    protected function setUp(): void
    {
        $this->sortingValidator = $this->createMock(SortingParametersValidatorInterface::class);
        $this->fieldValidator = $this->createMock(FieldValidatorInterface::class);
        $this->sorter = new Sorter($this->sortingValidator, $this->fieldValidator);
    }

    public function testImplementsSorterInterface(): void
    {
        $this->assertInstanceOf(SorterInterface::class, $this->sorter);
    }

    public function testSortsDataSourceBasedOnGridDefinition(): void
    {
        $grid = $this->createMock(Grid::class);
        $field = $this->createMock(Field::class);
        $anotherField = $this->createMock(Field::class);
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $parameters = new Parameters();

        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);

        $grid->method('getSorting')->willReturn(['name' => 'desc']);
        $grid->method('getFields')->willReturn(['name' => $field, 'code' => $anotherField]);

        $this->sortingValidator
            ->expects($this->once())
            ->method('validateSortingParameters')
            ->with(['name' => 'desc'], ['name' => $field, 'code' => $anotherField]);

        $this->fieldValidator
            ->expects($this->once())
            ->method('validateFieldName')
            ->with('name', ['name' => $field, 'code' => $anotherField]);

        $grid->method('hasField')->with('name')->willReturn(true);
        $grid->method('getField')->with('name')->willReturn($field);

        $field->method('isSortable')->willReturn(true);
        $field->method('getSortable')->willReturn('translation.name');

        $expressionBuilder
            ->expects($this->once())
            ->method('addOrderBy')
            ->with('translation.name', 'desc');

        $this->sorter->sort($dataSource, $grid, $parameters);
    }

    public function testSortsDataSourceBasedOnSortingParameter(): void
    {
        $grid = $this->createMock(Grid::class);
        $field = $this->createMock(Field::class);
        $anotherField = $this->createMock(Field::class);
        $dataSource = $this->createMock(DataSourceInterface::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $parameters = new Parameters(['sorting' => ['name' => 'asc']]);

        $dataSource->method('getExpressionBuilder')->willReturn($expressionBuilder);

        $grid->method('getSorting')->willReturn(['code' => 'asc']);
        $grid->method('getFields')->willReturn(['name' => $field, 'code' => $anotherField]);

        $this->sortingValidator
            ->expects($this->once())
            ->method('validateSortingParameters')
            ->with(['name' => 'asc'], ['name' => $field, 'code' => $anotherField]);

        $this->fieldValidator
            ->expects($this->once())
            ->method('validateFieldName')
            ->with('name', ['name' => $field, 'code' => $anotherField]);

        $grid->method('hasField')->with('name')->willReturn(true);
        $grid->method('getField')->with('name')->willReturn($field);

        $field->method('isSortable')->willReturn(true);
        $field->method('getSortable')->willReturn('translation.name');

        $expressionBuilder
            ->expects($this->once())
            ->method('addOrderBy')
            ->with('translation.name', 'asc');

        $this->sorter->sort($dataSource, $grid, $parameters);
    }
}
