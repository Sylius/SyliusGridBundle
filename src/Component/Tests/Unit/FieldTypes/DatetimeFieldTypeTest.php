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
use Sylius\Component\Grid\FieldTypes\DatetimeFieldType;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DatetimeFieldTypeTest extends TestCase
{
    private DataExtractorInterface|MockObject $dataExtractorMock;

    private DatetimeFieldType $datetimeFieldType;

    protected function setUp(): void
    {
        $this->dataExtractorMock = $this->createMock(DataExtractorInterface::class);
        $this->datetimeFieldType = new DatetimeFieldType($this->dataExtractorMock);
    }

    public function testIsAGridFieldType(): void
    {
        $this->assertInstanceOf(FieldTypeInterface::class, $this->datetimeFieldType);
    }

    public function testUsesDataExtractorToObtainDataParseItWithGivenConfigurationAndRendersIt(): void
    {
        $dateTime = new \DateTimeImmutable('2021-10-10T00:00:00+00:00', new \DateTimeZone('utc'));

        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->dataExtractorMock->expects($this->once())->method('get')->with($fieldMock, ['foo' => 'bar'])->willReturn($dateTime);

        $this->assertSame('2021-10-10T00:00:00+00:00', $this->datetimeFieldType->render($fieldMock, ['foo' => 'bar'], [
            'format' => 'c',
            'timezone' => null,
        ]));
    }

    public function testSetsTimezoneIfSpecified(): void
    {
        $dateTime = new \DateTimeImmutable('2021-10-10T00:00:00+00:00', new \DateTimeZone('utc'));

        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->dataExtractorMock->expects($this->once())->method('get')->with($fieldMock, ['foo' => 'bar'])->willReturn($dateTime);

        $this->assertSame('2021-10-10T02:00:00+02:00', $this->datetimeFieldType->render($fieldMock, ['foo' => 'bar'], [
            'format' => 'c',
            'timezone' => 'Europe/Warsaw',
        ]));
    }

    public function testReturnsNullIfPropertyAccessorReturnsNull(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->dataExtractorMock->expects($this->once())->method('get')->with($fieldMock, ['foo' => 'bar'])->willReturn(null);

        $this->assertSame('', $this->datetimeFieldType->render($fieldMock, ['foo' => 'bar'], [
            'format' => '',
            'timezone' => null,
        ]));
    }

    public function testUsesTimezoneParameterAsDefaultTimezoneOption(): void
    {
        /** @var OptionsResolver|MockObject $resolverMock */
        $resolverMock = $this->createMock(OptionsResolver::class);

        $this->datetimeFieldType = new DatetimeFieldType($this->dataExtractorMock, 'Europe/Warsaw');

        $resolverMock->expects($this->exactly(2))
            ->method('setDefault')
            ->willReturnMap([
                ['format', 'Y-m-d H:i:s', $resolverMock],
                ['timezone', 'Europe/Warsaw', $resolverMock],
            ])
        ;
        $resolverMock->expects($this->exactly(3))
            ->method('setAllowedTypes')
            ->willReturnMap([
                ['format', 'string', $resolverMock],
                ['timezone', ['null', 'string'], $resolverMock],
                ['vars', 'array', $resolverMock],
            ])
        ;

        $this->datetimeFieldType->configureOptions($resolverMock);
    }

    public function testThrowsExceptionIfReturnedValueIsNotDatetime(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->dataExtractorMock->expects($this->once())
            ->method('get')
            ->with($fieldMock, ['foo' => 'bar'])
            ->willReturn('badObject')
        ;
        $this->expectException(\InvalidArgumentException::class);

        $this->datetimeFieldType->render($fieldMock, ['foo' => 'bar'], [
            'format' => '',
            'timezone' => null,
        ]);
    }
}
