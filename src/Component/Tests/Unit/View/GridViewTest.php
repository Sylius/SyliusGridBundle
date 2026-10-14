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

namespace Sylius\Component\Grid\Tests\Unit\View;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Grid\View\GridViewInterface;

final class GridViewTest extends TestCase
{
    private GridView $gridView;

    private Grid $gridDefinition;

    protected function setUp(): void
    {
        $this->gridDefinition = $this->createMock(Grid::class);
        $this->gridView = new GridView(['foo', 'bar'], $this->gridDefinition, new Parameters());
    }

    public function testItImplementsGridViewInterface(): void
    {
        $this->assertInstanceOf(GridViewInterface::class, $this->gridView);
    }

    public function testHasData(): void
    {
        $this->assertSame(['foo', 'bar'], $this->gridView->getData());
    }

    public function testHasDefinition(): void
    {
        $this->assertSame($this->gridDefinition, $this->gridView->getDefinition());
    }

    public function testHasParameters(): void
    {
        $this->assertEquals(new Parameters(), $this->gridView->getParameters());
    }

    public function testUsesDefaultSortingFromDefinitionIfNotProvidedInParameters(): void
    {
        $codeField = $this->createMock(Field::class);
        $nameField = $this->createMock(Field::class);

        $this->gridDefinition->method('hasField')->willReturnMap([
            ['foo', true],
            ['code', true],
            ['name', true],
        ]);

        $this->gridDefinition->method('getField')->willReturnMap([
            ['code', $codeField],
            ['name', $nameField],
        ]);

        $codeField->method('isSortable')->willReturn(true);
        $nameField->method('isSortable')->willReturn(true);
        $nameField->method('getSortable')->willReturn('name');

        $this->gridDefinition->method('getSorting')->willReturn(['name' => 'asc']);

        $this->assertFalse($this->gridView->isSortedBy('code'));
        $this->assertTrue($this->gridView->isSortedBy('name'));
    }

    public function testKnowsWhichFieldItHasBeenSortedBy(): void
    {
        $nameField = $this->createMock(Field::class);
        $codeField = $this->createMock(Field::class);

        $gridView = new GridView(['foo', 'bar'], $this->gridDefinition, new Parameters([
            'sorting' => ['name' => ['direction' => 'asc']],
        ]));

        $this->gridDefinition->method('hasField')->willReturnMap([
            ['foo', true],
            ['name', true],
            ['code', true],
        ]);

        $this->gridDefinition->method('getField')->willReturnMap([
            ['name', $nameField],
            ['code', $codeField],
        ]);

        $nameField->method('isSortable')->willReturn(true);
        $nameField->method('getSortable')->willReturn('name');

        $codeField->method('isSortable')->willReturn(true);
        $codeField->method('getSortable')->willReturn('code');

        $this->gridDefinition->method('getSorting')->willReturn(['code' => ['order' => 'desc']]);

        $this->assertTrue($gridView->isSortedBy('name'));
        $this->assertFalse($gridView->isSortedBy('code'));
    }

    public function testThrowsExceptionWhenTryingToSortByNonExistentField(): void
    {
        $this->gridDefinition->method('hasField')->with('code')->willReturn(false);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Field "code" does not exist.');

        $this->gridView->getSortingOrder('code');
    }

    public function testThrowsExceptionWhenTryingToSortByNonSortableField(): void
    {
        $nameField = $this->createMock(Field::class);

        $this->gridDefinition->method('hasField')->willReturnMap([
            ['code', true],
            ['name', true],
        ]);

        $this->gridDefinition->method('getField')->willReturnMap([
            ['name', $nameField],
        ]);

        $nameField->method('isSortable')->willReturn(false);

        $this->gridDefinition->method('getSorting')->willReturn(['code' => ['order' => 'asc']]);

        $this->expectException(\InvalidArgumentException::class);
        $this->gridView->isSortedBy('name');

        $this->expectException(\InvalidArgumentException::class);
        $this->gridView->getSortingOrder('name');
    }

    public function testFieldVisibilityDefaultsToTheDefinition(): void
    {
        $this->mockFields($this->createField('code'), $this->createField('name', visible: false));

        $this->assertTrue($this->gridView->isFieldVisible('code'));
        $this->assertFalse($this->gridView->isFieldVisible('name'));
    }

    public function testFieldVisibilityCanBeOverriddenByParameters(): void
    {
        $this->mockFields($this->createField('code'), $this->createField('name', visible: false));

        $gridView = new GridView([], $this->gridDefinition, new Parameters([
            'visibility' => ['code' => 'hidden', 'name' => 'visible'],
        ]));

        $this->assertFalse($gridView->isFieldVisible('code'));
        $this->assertTrue($gridView->isFieldVisible('name'));
    }

    public function testIgnoresInvalidFieldVisibilityParameters(): void
    {
        $this->mockFields($this->createField('code'), $this->createField('name', visible: false));

        $gridView = new GridView([], $this->gridDefinition, new Parameters([
            'visibility' => ['code' => 'foo', 'name' => ['visible']],
        ]));

        $this->assertTrue($gridView->isFieldVisible('code'));
        $this->assertFalse($gridView->isFieldVisible('name'));
    }

    public function testIgnoresAVisibilityParameterThatIsNotAnArray(): void
    {
        $this->mockFields($this->createField('code'));

        $gridView = new GridView([], $this->gridDefinition, new Parameters(['visibility' => 'hidden']));

        $this->assertTrue($gridView->isFieldVisible('code'));
    }

    public function testDisabledFieldIsNeverVisible(): void
    {
        $this->mockFields($this->createField('code', enabled: false));

        $gridView = new GridView([], $this->gridDefinition, new Parameters([
            'visibility' => ['code' => 'visible'],
        ]));

        $this->assertFalse($gridView->isFieldVisible('code'));
    }

    public function testThrowsExceptionWhenCheckingVisibilityOfNonExistentField(): void
    {
        $this->gridDefinition->method('hasField')->with('code')->willReturn(false);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Field "code" does not exist.');

        $this->gridView->isFieldVisible('code');
    }

    private function createField(string $name, bool $enabled = true, bool $visible = true): Field
    {
        $field = Field::fromNameAndType($name, 'string');
        $field->setEnabled($enabled);
        $field->setVisible($visible);

        return $field;
    }

    private function mockFields(Field ...$fields): void
    {
        $fieldsByName = [];
        foreach ($fields as $field) {
            $fieldsByName[$field->getName()] = $field;
        }

        $this->gridDefinition->method('hasField')->willReturnCallback(fn (string $name): bool => isset($fieldsByName[$name]));
        $this->gridDefinition->method('getField')->willReturnCallback(fn (string $name): Field => $fieldsByName[$name]);
    }
}
