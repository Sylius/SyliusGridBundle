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

namespace spec\Sylius\Component\Grid\Validation;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Validation\SortingParametersValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class SortingParametersValidatorSpec extends ObjectBehavior
{
    function it_implements_grid_data_source_sorting_validator_interface(): void
    {
        $this->shouldImplement(SortingParametersValidatorInterface::class);
    }

    function it_throws_exception_if_wrong_sorting_parameter_provided(
        Grid $grid,
        Field $field,
        Field $anotherField,
    ): void {
        $grid->getEnabledFields()->willReturn(['name' => $field, 'code' => $anotherField]);
        $grid->getSorting()->willReturn(['name' => 'non_sortable_parameter']);

        $this
            ->shouldThrow(new BadRequestHttpException('non_sortable_parameter is not valid, use asc or desc instead.'))
            ->during('validateSortingParameters', [['name' => 'non_sortable_parameter'], ['name' => $field, 'code' => $anotherField]])
        ;
    }

    function it_passes_if_valid_sorting_parameter_provided(
        Grid $grid,
        Field $field,
        Field $anotherField,
    ): void {
        $grid->getEnabledFields()->willReturn(['name' => $field, 'code' => $anotherField]);
        $grid->getSorting()->willReturn(['name' => 'asc']);

        $this
            ->shouldNotThrow(new BadRequestHttpException())
            ->during('validateSortingParameters', [['name' => 'asc'], ['name' => $field, 'code' => $anotherField]])
        ;
    }
}
