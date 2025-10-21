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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Builder\Field;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Builder\Field\Field;
use Sylius\Bundle\GridBundle\Builder\Field\FieldInterface;

final class FieldTest extends TestCase
{
    private Field $field;

    protected function setUp(): void
    {
        $this->field = Field::create('name', 'string');
    }

    public function testImplementsAnInterface(): void
    {
        $this->assertInstanceOf(FieldInterface::class, $this->field);
    }

    public function testHasNoPathByDefault(): void
    {
        $this->assertNull($this->field->getPath());
    }

    public function testSetsPath(): void
    {
        $this->field->setPath('custom_path');

        $this->assertEquals('custom_path', $this->field->getPath());
    }

    public function testHasNoLabelByDefault(): void
    {
        $this->assertNull($this->field->getLabel());
    }

    public function testSetsLabel(): void
    {
        $this->field->setLabel('Name');

        $this->assertEquals('Name', $this->field->getLabel());
    }

    public function testEnablesFields(): void
    {
        $this->field->setEnabled(true);

        $this->assertTrue($this->field->isEnabled());
    }

    public function testDisablesFields(): void
    {
        $this->field->setEnabled(false);

        $this->assertFalse($this->field->isEnabled());
    }

    public function testMakesFieldsSortable(): void
    {
        $this->field->setSortable(true);

        $this->assertTrue($this->field->isSortable());
        $this->assertEquals(true, $this->field->toArray()['sortable']);
    }

    public function testMakesFieldsSortableWithPath(): void
    {
        $this->field->setSortable(true, 'path');

        $this->assertTrue($this->field->isSortable());
        $this->assertEquals('path', $this->field->toArray()['sortable']);
    }

    public function testMakesFieldsNotSortable(): void
    {
        $this->field->setSortable(false);

        $this->assertFalse($this->field->isSortable());
        $this->assertArrayNotHasKey('sortable', $this->field->toArray());
    }

    public function testSetsPosition(): void
    {
        $this->field->setPosition(42);

        $this->assertEquals(42, $this->field->getPosition());
    }

    public function testSetsOptions(): void
    {
        $this->field->setOptions(['template' => '/path/to/template']);

        $this->assertEquals(['template' => '/path/to/template'], $this->field->getOptions());
    }

    public function testSetsOneOption(): void
    {
        $this->field->setOptions(['template' => '/path/to/template']);
        $this->field->setOption('vars', ['labels' => '/path/to/label']);

        $this->assertEquals([
            'template' => '/path/to/template',
            'vars' => ['labels' => '/path/to/label'],
        ], $this->field->getOptions());
    }

    public function testCreatesWithOptions(): void
    {
        $this->field->setOptions(['template' => '/path/to/template']);
        $this->field->withOptions(['vars' => ['labels' => '/path/to/label']]);

        $this->assertEquals([
            'template' => '/path/to/template',
            'vars' => ['labels' => '/path/to/label'],
        ], $this->field->getOptions());
    }

    public function testAddsOptions(): void
    {
        $this->field->setOptions(['template' => '/path/to/template']);
        $this->field->addOptions(['vars' => ['labels' => '/path/to/label']]);

        $this->assertEquals([
            'template' => '/path/to/template',
            'vars' => ['labels' => '/path/to/label'],
        ], $this->field->getOptions());
    }

    public function testRemovesOptions(): void
    {
        $this->field->setOptions([
            'template' => '/path/to/template',
            'vars' => [
                'labels' => '/path/to/label',
            ],
        ]);

        $this->field->removeOption('vars');

        $this->assertEquals([
            'template' => '/path/to/template',
        ], $this->field->getOptions());
    }
}
