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
use Sylius\Component\Grid\Validation\FieldValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class FieldValidatorSpec extends ObjectBehavior
{
    function it_implements_field_validator_interface(): void
    {
        $this->shouldImplement(FieldValidatorInterface::class);
    }

    function it_throws_exception_if_wrong_field_name_provided(
        Grid $grid,
        Field $field,
        Field $anotherField,
    ): void {
        $grid->getEnabledFields()->willReturn(['name' => $field, 'code' => $anotherField]);
        $grid->getSorting()->willReturn(['sorting' => ['non_sortable_field' => 'desc']]);

        $this
            ->shouldThrow(new BadRequestHttpException('non_sortable_field is not valid field, did you mean one of these: name, code?'))
            ->during('validateFieldName', ['non_sortable_field', ['name' => $field, 'code' => $anotherField]])
        ;
    }

    function it_passes_if_valid_sorting_parameter_provided(
        Grid $grid,
        Field $field,
        Field $anotherField,
    ): void {
        $grid->getEnabledFields()->willReturn(['name' => $field, 'code' => $anotherField]);
        $grid->getSorting()->willReturn(['sorting' => ['sortable_field' => 'desc']]);

        $this
            ->shouldNotThrow(new BadRequestHttpException())
            ->during('validateFieldName', ['sortable_field', ['sortable_field' => $field, 'code' => $anotherField]])
        ;
    }
}
