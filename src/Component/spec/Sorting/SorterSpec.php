<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Grid\Sorting;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Sorting\SorterInterface;
use Sylius\Component\Grid\Validation\FieldValidatorInterface;
use Sylius\Component\Grid\Validation\SortingParametersValidatorInterface;

final class SorterSpec extends ObjectBehavior
{
    function let(SortingParametersValidatorInterface $sortingValidator, FieldValidatorInterface $fieldValidator): void
    {
        $this->beConstructedWith($sortingValidator, $fieldValidator);
    }

    function it_implements_grid_data_source_sorter_interface(): void
    {
        $this->shouldImplement(SorterInterface::class);
    }

    function it_sorts_the_data_source_via_expression_builder_based_on_the_grid_definition(
        Grid $grid,
        Field $field,
        Field $anotherField,
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
        SortingParametersValidatorInterface $sortingValidator,
        FieldValidatorInterface $fieldValidator
    ): void {
        $parameters = new Parameters();

        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $grid->getSorting()->willReturn(['name' => 'desc']);
        $grid->getFields()->willReturn(['name' => $field, 'code' => $anotherField]);

        $sortingValidator->validateSortingParameters(['name' => 'desc'], ['name' => $field, 'code' => $anotherField])->shouldBeCalled();
        $fieldValidator->validateFieldName('name', ['name' => $field, 'code' => $anotherField])->shouldBeCalled();

        $grid->hasField('name')->willReturn(true);
        $grid->getField('name')->willReturn($field);
        $field->isSortable()->willReturn(true);
        $field->getSortable()->willReturn('translation.name');

        $expressionBuilder->addOrderBy('translation.name', 'desc')->shouldBeCalled();

        $this->sort($dataSource, $grid, $parameters);
    }

    function it_sorts_the_data_source_via_expression_builder_based_on_sorting_parameter(
        Grid $grid,
        Field $field,
        Field $anotherField,
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
        SortingParametersValidatorInterface $sortingValidator,
        FieldValidatorInterface $fieldValidator
    ): void {
        $parameters = new Parameters(['sorting' => ['name' => 'asc']]);

        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $grid->getSorting()->willReturn(['code' => 'asc']);
        $grid->getFields()->willReturn(['name' => $field, 'code' => $anotherField]);

        $sortingValidator->validateSortingParameters(['name' => 'asc'], ['name' => $field, 'code' => $anotherField])->shouldBeCalled();
        $fieldValidator->validateFieldName('name', ['name' => $field, 'code' => $anotherField])->shouldBeCalled();

        $grid->hasField('name')->willReturn(true);
        $grid->getField('name')->willReturn($field);
        $field->isSortable()->willReturn(true);
        $field->getSortable()->willReturn('translation.name');

        $expressionBuilder->addOrderBy('translation.name', 'asc')->shouldBeCalled();

        $this->sort($dataSource, $grid, $parameters);
    }
}
