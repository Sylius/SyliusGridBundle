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

namespace Sylius\Component\Grid\Tests\Unit\Validation;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Validation\SortingParametersValidator;
use Sylius\Component\Grid\Validation\SortingParametersValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class SortingParametersValidatorTest extends TestCase
{
    private SortingParametersValidatorInterface $sortingValidator;

    protected function setUp(): void
    {
        $this->sortingValidator = new SortingParametersValidator();
    }

    public function testItImplementsSortingParametersValidatorInterface(): void
    {
        $this->assertInstanceOf(SortingParametersValidatorInterface::class, $this->sortingValidator);
    }

    public function testThrowsExceptionIfWrongSortingParameterProvided(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('non_sortable_parameter is not valid, use asc or desc instead.');

        $field = $this->createMock(Field::class);
        $anotherField = $this->createMock(Field::class);

        $this->sortingValidator->validateSortingParameters(
            ['name' => 'non_sortable_parameter'],
            ['name' => $field, 'code' => $anotherField],
        );
    }

    public function testPassesIfValidSortingParameterProvided(): void
    {
        $this->expectNotToPerformAssertions();

        $field = $this->createMock(Field::class);
        $anotherField = $this->createMock(Field::class);

        // Should not throw any exception
        $this->sortingValidator->validateSortingParameters(
            ['name' => 'asc'],
            ['name' => $field, 'code' => $anotherField],
        );
    }
}
