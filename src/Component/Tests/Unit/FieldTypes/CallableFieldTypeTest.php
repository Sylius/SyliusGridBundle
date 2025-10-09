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
use Sylius\Component\Grid\Exception\UnexpectedValueException;
use Sylius\Component\Grid\FieldTypes\CallableFieldType;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Symfony\Contracts\Service\ServiceLocatorTrait;
use Symfony\Contracts\Service\ServiceProviderInterface;

final class CallableFieldTypeTest extends TestCase
{
    private DataExtractorInterface|MockObject $dataExtractorMock;

    private CallableFieldType $callableFieldType;

    protected function setUp(): void
    {
        $this->dataExtractorMock = $this->createMock(DataExtractorInterface::class);
        $this->callableFieldType = new CallableFieldType($this->dataExtractorMock, new class(['my_service' => fn () => new class() {
            public function __invoke(string $value): string
            {
                return strtoupper($value);
            }

            public function concatenate(array $value): string
            {
                return implode(', ', $value);
            }
        },
        ]) implements ServiceProviderInterface {
            use ServiceLocatorTrait;
        });
    }

    public function testIsAGridFieldType(): void
    {
        $this->assertInstanceOf(FieldTypeInterface::class, $this->callableFieldType);
    }

    public function testUsesDataExtractorToObtainDataAndPassesItToACallableWithHtmlspecialchars(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->dataExtractorMock->expects($this->once())->method('get')->with($fieldMock, ['foo' => 'bar'])->willReturn('bar');

        $this->assertSame('&lt;strong&gt;bar&lt;/strong&gt;', $this->callableFieldType->render($fieldMock, ['foo' => 'bar'], [
            'callable' => fn (string $value): string => "<strong>$value</strong>",
            'htmlspecialchars' => true,
        ]));
    }

    public function testUsesDataExtractorToObtainDataAndPassesItToACallableWithoutHtmlspecialchars(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->dataExtractorMock->expects($this->once())->method('get')->with($fieldMock, ['foo' => 'bar'])->willReturn('bar');

        $this->assertSame('<strong>bar</strong>', $this->callableFieldType->render($fieldMock, ['foo' => 'bar'], [
            'callable' => fn (string $value): string => "<strong>$value</strong>",
            'htmlspecialchars' => false,
        ]));
    }

    public function testUsesDataExtractorToObtainDataAndPassesItToAFunctionCallable(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->dataExtractorMock->expects($this->once())->method('get')->with($fieldMock, ['foo' => 'bar'])->willReturn('bar');

        $this->assertSame('BAR', $this->callableFieldType->render($fieldMock, ['foo' => 'bar'], [
            'callable' => 'strtoupper',
            'htmlspecialchars' => true,
        ]));
    }

    public function testUsesDataExtractorToObtainDataAndPassesItToAStaticCallable(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->dataExtractorMock->expects($this->once())->method('get')->with($fieldMock, ['foo' => 'BAR'])->willReturn('BAR');

        $this->assertSame('bar', $this->callableFieldType->render($fieldMock, ['foo' => 'BAR'], [
            'callable' => [self::class, 'callable'],
            'htmlspecialchars' => true,
        ]));
    }

    public function testUsesDataExtractorToObtainDataAndPassesItToAService(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->dataExtractorMock->expects($this->once())->method('get')->with($fieldMock, ['foo' => 'bar'])->willReturn('bar');

        $this->assertSame('BAR', $this->callableFieldType->render($fieldMock, ['foo' => 'bar'], [
            'service' => 'my_service',
            'htmlspecialchars' => true,
        ]));
    }

    public function testUsesDataExtractorToObtainDataAndPassesItToAServiceAndMethod(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->dataExtractorMock->expects($this->once())->method('get')->with($fieldMock, ['foo' => ['foo', 'bar', 'foobar']])->willReturn(['foo', 'bar', 'foobar']);

        $this->assertSame('foo, bar, foobar', $this->callableFieldType->render($fieldMock, ['foo' => ['foo', 'bar', 'foobar']], [
            'service' => 'my_service',
            'method' => 'concatenate',
            'htmlspecialchars' => true,
        ]));
    }

    public function testThrowsAnExceptionWhenACallableReturnValueCannotBeCastedToString(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $fieldMock->expects($this->once())->method('getName')->willReturn('id');

        $this->dataExtractorMock->expects($this->once())->method('get')->with($fieldMock, ['foo' => 'bar'])->willReturn('BAR');
        $this->expectException(UnexpectedValueException::class);

        $this->callableFieldType->render($fieldMock, ['foo' => 'bar'], [
            'callable' => fn () => new \stdclass(),
            'htmlspecialchars' => true,
        ]);
    }

    public function testThrowsAnExceptionWhenNeitherCallableNorServiceOptionsAreDefined(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->expectException(\RuntimeException::class);

        $this->callableFieldType->render($fieldMock, ['foo' => 'bar'], []);
    }

    public function testThrowsAnExceptionWhenBothCallableAndServiceOptionsAreDefined(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $this->expectException(\RuntimeException::class);

        $this->callableFieldType->render($fieldMock, ['foo' => 'bar'], [
            'callable' => fn () => new \stdclass(),
            'service' => 'my_service',
        ]);
    }

    public static function callable(mixed $value): string
    {
        return strtolower($value);
    }
}
