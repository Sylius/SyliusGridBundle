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
use Sylius\Component\Grid\Validation\FieldValidator;
use Sylius\Component\Grid\Validation\FieldValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class FieldValidatorTest extends TestCase
{
    private FieldValidatorInterface $fieldValidator;

    protected function setUp(): void
    {
        $this->fieldValidator = new FieldValidator();
    }

    public function testImplementsFieldValidatorInterface(): void
    {
        $this->assertInstanceOf(FieldValidatorInterface::class, $this->fieldValidator);
    }

    public function testThrowsExceptionIfWrongFieldNameProvided(): void
    {
        $field = $this->createMock(Field::class);
        $anotherField = $this->createMock(Field::class);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('non_sortable_field is not valid field, did you mean one of these: name, code?');

        $this->fieldValidator->validateFieldName('non_sortable_field', ['name' => $field, 'code' => $anotherField]);
    }

    public function testPassesIfValidSortingParameterProvided(): void
    {
        $this->expectNotToPerformAssertions();

        $field = $this->createMock(Field::class);
        $anotherField = $this->createMock(Field::class);

        // Should not throw
        $this->fieldValidator->validateFieldName('name', ['name' => $field, 'code' => $anotherField]);
    }
}
