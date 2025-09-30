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

namespace Sylius\Component\Grid\Tests\Unit\Definition;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Field;

final class FieldTest extends TestCase
{
    private Field $field;

    protected function setUp(): void
    {
        $this->field = Field::fromNameAndType('enabled', 'boolean');
    }

    public function testHasName(): void
    {
        $this->assertSame('enabled', $this->field->getName());
    }

    public function testHasType(): void
    {
        $this->assertSame('boolean', $this->field->getType());
    }

    public function testHasPathWhichDefaultsToName(): void
    {
        $this->assertSame('enabled', $this->field->getPath());

        $this->field->setPath('method.enabled');
        $this->assertSame('method.enabled', $this->field->getPath());
    }

    public function testHasLabelWhichDefaultsToName(): void
    {
        $this->assertSame('enabled', $this->field->getLabel());

        $this->field->setLabel('Is enabled?');
        $this->assertSame('Is enabled?', $this->field->getLabel());
    }

    public function testToggleable(): void
    {
        $this->assertTrue($this->field->isEnabled());

        $this->field->setEnabled(false);
        $this->assertFalse($this->field->isEnabled());
        $this->field->setEnabled(true);
        $this->assertTrue($this->field->isEnabled());
    }

    public function testKnowsByWhichPropertyItCanBeSorted(): void
    {
        $this->assertNull($this->field->getSortable());

        $this->field->setSortable('method.enabled');
        $this->assertSame('method.enabled', $this->field->getSortable());
    }

    public function testItsSortedByNameWhenSortableIsNotSet(): void
    {
        $this->assertNull($this->field->getSortable());

        $this->field->setSortable('enabled');
        $this->assertSame('enabled', $this->field->getSortable());
    }

    public function testHasNoOptionsByDefault(): void
    {
        $this->assertSame([], $this->field->getOptions());
    }

    public function testCanHaveOptions(): void
    {
        $this->field->setOptions(['template' => '@SyliusUi/Grid/Field/_status.html.twig']);
        $this->assertSame(['template' => '@SyliusUi/Grid/Field/_status.html.twig'], $this->field->getOptions());
    }

    public function testHasLastPositionByDefault(): void
    {
        $this->assertSame(100, $this->field->getPosition());
    }

    public function testItsPositionIsMutable(): void
    {
        $this->field->setPosition(1);
        $this->assertSame(1, $this->field->getPosition());
    }
}
