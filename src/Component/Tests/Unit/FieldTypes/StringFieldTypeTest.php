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
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Sylius\Component\Grid\FieldTypes\StringFieldType;

final class StringFieldTypeTest extends TestCase
{
    private DataExtractorInterface|MockObject $dataExtractorMock;

    private StringFieldType $stringFieldType;

    protected function setUp(): void
    {
        $this->dataExtractorMock = $this->createMock(DataExtractorInterface::class);
        $this->stringFieldType = new StringFieldType($this->dataExtractorMock);
    }

    public function testIsAGridFieldType(): void
    {
        $this->assertInstanceOf(FieldTypeInterface::class, $this->stringFieldType);
    }

    public function testUsesDataExtractorToObtainDataAndRendersIt(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->dataExtractorMock->expects($this->once())->method('get')->with($fieldMock, ['foo' => 'bar'])->willReturn('Value');

        $this->assertSame('Value', $this->stringFieldType->render($fieldMock, ['foo' => 'bar'], []));
    }

    public function testEscapesStringValues(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->dataExtractorMock->expects($this->once())->method('get')->with($fieldMock, ['foo' => 'bar'])->willReturn('<i class="book icon"></i>');

        $this->assertSame('&lt;i class=&quot;book icon&quot;&gt;&lt;/i&gt;', $this->stringFieldType->render($fieldMock, ['foo' => 'bar'], []));
    }

    public function testCastsObjectsToString(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $data = new class() {
            public function __toString(): string
            {
                return 'Value';
            }
        };

        $this->dataExtractorMock->expects($this->once())->method('get')->with($fieldMock, ['foo' => 'bar'])->willReturn($data);

        $this->assertSame('Value', $this->stringFieldType->render($fieldMock, ['foo' => 'bar'], []));
    }

    public function testEscapesObjectsCastedToString(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $data = new class() {
            public function __toString(): string
            {
                return '<i class="book icon"></i>';
            }
        };

        $this->dataExtractorMock->expects($this->once())
            ->method('get')
            ->with($fieldMock, ['foo' => 'bar'])
            ->willReturn($data)
        ;

        $this->assertSame('&lt;i class=&quot;book icon&quot;&gt;&lt;/i&gt;', $this->stringFieldType->render($fieldMock, ['foo' => 'bar'], []));
    }

    public function testCastsIntsToString(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->dataExtractorMock->expects($this->once())
            ->method('get')
            ->with($fieldMock, ['foo' => 'bar'])
            ->willReturn(420)
        ;

        $this->assertSame('420', $this->stringFieldType->render($fieldMock, ['foo' => 'bar'], []));
    }

    public function testCastsFloatsToString(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->dataExtractorMock->expects($this->once())
            ->method('get')
            ->with($fieldMock, ['foo' => 'bar'])
            ->willReturn(420.1337)
        ;

        $this->assertSame('420.1337', $this->stringFieldType->render($fieldMock, ['foo' => 'bar'], []));
    }

    public function testCastsNullToString(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->dataExtractorMock->expects($this->once())
            ->method('get')
            ->with($fieldMock, ['foo' => 'bar'])
            ->willReturn(null)
        ;

        $this->assertSame('', $this->stringFieldType->render($fieldMock, ['foo' => 'bar'], []));
    }
}
