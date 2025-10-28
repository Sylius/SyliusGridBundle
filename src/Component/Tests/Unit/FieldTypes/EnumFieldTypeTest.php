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

namespace Sylius\Component\Grid\Tests\Unit\FieldTypes;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Exception\LogicException;
use Sylius\Component\Grid\FieldTypes\EnumFieldType;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Sylius\Component\Grid\Tests\Dummy\TestEnum;
use Sylius\Component\Grid\Tests\Dummy\TestNonBackedEnum;
use Sylius\Component\Grid\Tests\Dummy\TestTranslatableEnum;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\InvalidArgumentException;

final class EnumFieldTypeTest extends TestCase
{
    private DataExtractorInterface|MockObject $dataExtractorMock;

    private EnumFieldType $enumFieldType;

    private Field $enumField;

    protected function setUp(): void
    {
        $this->dataExtractorMock = $this->createMock(DataExtractorInterface::class);
        $this->enumFieldType = new EnumFieldType($this->dataExtractorMock, $this->createMock(TranslatorInterface::class));
        $this->enumField = Field::fromNameAndType('status', 'enum');
    }

    public function testAGridFieldType(): void
    {
        $this->assertInstanceOf(FieldTypeInterface::class, $this->enumFieldType);
    }

    public function testUsesDataExtractorToObtainDataAndRendersIt(): void
    {
        $this->dataExtractorMock->expects($this->once())->method('get')->with($this->enumField, ['foo' => 'bar'])->willReturn(TestEnum::A);

        $this->assertSame('a', $this->enumFieldType->render($this->enumField, ['foo' => 'bar'], []));
    }

    public function testUsesDataExtractorToObtainDataParseWithGivenConfigurationAndTranslatorAndRendersIt(): void
    {
        $this->dataExtractorMock->expects($this->once())->method('get')->with($this->enumField, ['foo' => 'bar'])->willReturn(TestTranslatableEnum::A);

        $this->assertSame('a.translation', $this->enumFieldType->render($this->enumField, ['foo' => 'bar'], []));
    }

    public function testUsesDataExtractorToObtainDataParseWithGivenConfigurationAndWithoutTranslationPackage(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('You have configured a translatable enum, but Symfony translator is not available. Try running "composer require symfony/translator".');

        $this->dataExtractorMock->expects($this->once())->method('get')->with($this->enumField, ['foo' => 'bar'])->willReturn(TestTranslatableEnum::A);

        $enumFieldType = new EnumFieldType($this->dataExtractorMock);
        $this->assertSame('a.translation', $enumFieldType->render($this->enumField, ['foo' => 'bar'], []));
    }

    public function testUsesDataExtractorToObtainNonBackedEnumDataParseWithGivenConfigurationAndTranslatorAndRendersIt(): void
    {
        $this->dataExtractorMock->expects($this->once())->method('get')->with($this->enumField, ['foo' => 'bar'])->willReturn(TestNonBackedEnum::A);

        $this->assertSame('A', $this->enumFieldType->render($this->enumField, ['foo' => 'bar'], []));
    }

    public function testUsesDataExtractorToObtainNullDataParseWithGivenConfigurationAndTranslatorAndRendersIt(): void
    {
        $this->dataExtractorMock->expects($this->once())->method('get')->with($this->enumField, ['foo' => 'bar'])->willReturn(null);

        $this->assertSame('', $this->enumFieldType->render($this->enumField, ['foo' => 'bar'], []));
    }

    public function testUsesDataExtractorWithInvalidData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an instance of UnitEnum. Got: string');

        $this->dataExtractorMock->expects($this->once())->method('get')->with($this->enumField, ['foo' => 'bar'])->willReturn('testvalue');

        $this->assertSame('', $this->enumFieldType->render($this->enumField, ['foo' => 'bar'], []));
    }
}
