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

namespace Sylius\Bundle\GridBundle\Tests\Unit\FieldTypes;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\FieldTypes\TwigFieldType;
use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Twig\Environment;

final class TwigFieldTypeTest extends TestCase
{
    private DataExtractorInterface|MockObject $dataExtractor;

    private Environment|MockObject $twig;

    private TwigFieldType $fieldType;

    protected function setUp(): void
    {
        $this->dataExtractor = $this->createMock(DataExtractorInterface::class);
        $this->twig = $this->createMock(Environment::class);
        $this->fieldType = new TwigFieldType($this->dataExtractor, $this->twig);
    }

    public function testIsAGridFieldType(): void
    {
        $this->assertInstanceOf(FieldTypeInterface::class, $this->fieldType);
    }

    public function testUsesDataExtractorToObtainDataAndRendersItViaTwig(): void
    {
        $field = $this->createMock(Field::class);
        $field->method('getPath')->willReturn('foo');

        $this->dataExtractor->expects(self::once())->method('get')->with($field, ['foo' => 'bar'])->willReturn('Value');
        $this->twig->expects(self::once())->method('render')->with('foo.html.twig', ['data' => 'Value', 'options' => ['template' => 'foo.html.twig']])->willReturn('<html>Value</html>');

        $result = $this->fieldType->render($field, ['foo' => 'bar'], [
            'template' => 'foo.html.twig',
        ]);

        $this->assertEquals('<html>Value</html>', $result);
    }

    public function testUsesDataDirectlyIfDotIsConfiguredAsPath(): void
    {
        $field = $this->createMock(Field::class);
        $field->method('getPath')->willReturn('.');

        $this->twig->expects(self::once())->method('render')->with('foo.html.twig', ['data' => 'bar', 'options' => ['template' => 'foo.html.twig']])->willReturn('<html>Bar</html>');

        $result = $this->fieldType->render($field, 'bar', ['template' => 'foo.html.twig']);

        $this->assertEquals('<html>Bar</html>', $result);
    }
}
