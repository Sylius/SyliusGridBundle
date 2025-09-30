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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\ActionGroup;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;

final class GridTest extends TestCase
{
    private Grid $grid;

    protected function setUp(): void
    {
        $this->grid = Grid::fromCodeAndDriverConfiguration('sylius_admin_tax_category', 'doctrine/orm', [
            'resource' => 'sylius.tax_category',
            'method' => 'createByCodeQueryBuilder',
            'arguments' => ['$code'],
        ]);
    }

    public function testHasCode(): void
    {
        $this->assertSame('sylius_admin_tax_category', $this->grid->getCode());
    }

    public function testHasDriver(): void
    {
        $this->assertSame('doctrine/orm', $this->grid->getDriver());
    }

    public function testHasDriverConfiguration(): void
    {
        $this->assertSame([
            'resource' => 'sylius.tax_category',
            'method' => 'createByCodeQueryBuilder',
            'arguments' => ['$code'],
        ], $this->grid->getDriverConfiguration());
    }

    public function testItsDriverConfigurationIsMutable(): void
    {
        $this->grid->setDriverConfiguration(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $this->grid->getDriverConfiguration());
    }

    public function testHasNoProviderByDefault(): void
    {
        $this->assertNull($this->grid->getProvider());
    }

    public function testItsProviderIsMutable(): void
    {
        $this->grid->setProvider('App\Provider');
        $this->assertSame('App\Provider', $this->grid->getProvider());
    }

    public function testItsProviderCouldBeACallable(): void
    {
        $this->grid->setProvider([GridProviderCallable::class, 'getData']);
        $this->assertSame([GridProviderCallable::class, 'getData'], $this->grid->getProvider());
    }

    public function testHasEmptySortingConfigurationByDefault(): void
    {
        $this->assertSame([], $this->grid->getSorting());
    }

    public function testCanHaveSortingConfiguration(): void
    {
        $this->grid->setSorting(['name' => 'asc']);
        $this->assertSame(['name' => 'asc'], $this->grid->getSorting());
    }

    public function testHasNoPaginationLimitsByDefault(): void
    {
        $this->assertSame([], $this->grid->getLimits());
    }

    public function testItsPaginationLimitsCanBeConfigured(): void
    {
        $this->grid->setLimits([20, 50, 100]);
        $this->assertSame([20, 50, 100], $this->grid->getLimits());
    }

    public function testDoesNotHaveAnyFieldsByDefault(): void
    {
        $this->assertSame([], $this->grid->getFields());
    }

    public function testCanHaveFieldDefinitions(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);
        $fieldMock->expects($this->once())->method('getName')->willReturn('description');

        $this->grid->addField($fieldMock);

        $this->assertSame($fieldMock, $this->grid->getField('description'));
    }

    public function testCannotHaveTwoFieldsWithTheSameName(): void
    {
        /** @var Field|MockObject $firstFieldMock */
        $firstFieldMock = $this->createMock(Field::class);

        /** @var Field|MockObject $secondFieldMock */
        $secondFieldMock = $this->createMock(Field::class);

        $firstFieldMock->expects($this->once())->method('getName')->willReturn('created_at');
        $secondFieldMock->expects($this->once())->method('getName')->willReturn('created_at');

        $this->grid->addField($firstFieldMock);

        $this->expectException(\InvalidArgumentException::class);

        $this->grid->addField($secondFieldMock);
    }

    public function testKnowsIfFieldWithGivenNameAlreadyExists(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $fieldMock->expects($this->once())->method('getName')->willReturn('enabled');

        $this->grid->addField($fieldMock);

        $this->assertTrue($this->grid->hasField('enabled'));
        $this->assertFalse($this->grid->hasField('parent'));
    }

    public function testCanRemoveField(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        $fieldMock->expects($this->once())->method('getName')->willReturn('enabled');

        $this->grid->addField($fieldMock);
        $this->grid->removeField('enabled');

        $this->assertFalse($this->grid->hasField('enabled'));
    }

    public function testCanReplaceField(): void
    {
        /** @var Field|MockObject $firstFieldMock */
        $firstFieldMock = $this->createMock(Field::class);

        /** @var Field|MockObject $secondFieldMock */
        $secondFieldMock = $this->createMock(Field::class);

        $firstFieldMock->expects($this->once())->method('getName')->willReturn('enabled');
        $secondFieldMock->expects($this->once())->method('getName')->willReturn('enabled');

        $this->grid->addField($firstFieldMock);
        $this->grid->setField($secondFieldMock);

        $this->assertSame($secondFieldMock, $this->grid->getField('enabled'));
    }

    public function testCanReturnFields(): void
    {
        /** @var Field|MockObject $firstFieldMock */
        $firstFieldMock = $this->createMock(Field::class);

        /** @var Field|MockObject $secondFieldMock */
        $secondFieldMock = $this->createMock(Field::class);

        $firstFieldMock->expects($this->once())->method('getName')->willReturn('first');
        $secondFieldMock->expects($this->once())->method('getName')->willReturn('second');

        $this->grid->addField($firstFieldMock);
        $this->grid->addField($secondFieldMock);

        $this->assertCount(2, $this->grid->getFields());
    }

    public function testCanReturnOnlyEnabledFields(): void
    {
        /** @var Field|MockObject $firstFieldMock */
        $firstFieldMock = $this->createMock(Field::class);

        /** @var Field|MockObject $secondFieldMock */
        $secondFieldMock = $this->createMock(Field::class);

        $firstFieldMock->expects($this->once())->method('getName')->willReturn('first');
        $firstFieldMock->expects($this->once())->method('isEnabled')->willReturn(true);
        $secondFieldMock->expects($this->once())->method('getName')->willReturn('second');
        $secondFieldMock->expects($this->once())->method('isEnabled')->willReturn(false);

        $this->grid->addField($firstFieldMock);
        $this->grid->addField($secondFieldMock);

        $this->assertCount(1, $this->grid->getEnabledFields());
    }

    public function testDoesNotHaveAnyActionGroupsByDefault(): void
    {
        $this->assertSame([], $this->grid->getActionGroups());
    }

    public function testCanHaveActionGroupDefinitions(): void
    {
        /** @var ActionGroup|MockObject $actionGroupMock */
        $actionGroupMock = $this->createMock(ActionGroup::class);

        $actionGroupMock->expects($this->once())->method('getName')->willReturn('default');

        $this->grid->addActionGroup($actionGroupMock);

        $this->assertSame($actionGroupMock, $this->grid->getActionGroup('default'));
    }

    public function testCannotHaveTwoActionGroupsWithTheSameName(): void
    {
        /** @var ActionGroup|MockObject $firstActionGroupMock */
        $firstActionGroupMock = $this->createMock(ActionGroup::class);

        /** @var ActionGroup|MockObject $secondActionGroupMock */
        $secondActionGroupMock = $this->createMock(ActionGroup::class);

        $firstActionGroupMock->expects($this->once())->method('getName')->willReturn('row');
        $secondActionGroupMock->expects($this->once())->method('getName')->willReturn('row');

        $this->grid->addActionGroup($firstActionGroupMock);

        $this->expectException(\InvalidArgumentException::class);

        $this->grid->addActionGroup($secondActionGroupMock);
    }

    public function testKnowsIfActionGroupWithGivenNameAlreadyExists(): void
    {
        /** @var ActionGroup|MockObject $actionGroupMock */
        $actionGroupMock = $this->createMock(ActionGroup::class);

        $actionGroupMock->expects($this->once())->method('getName')->willReturn('row');

        $this->grid->addActionGroup($actionGroupMock);

        $this->assertTrue($this->grid->hasActionGroup('row'));
        $this->assertFalse($this->grid->hasActionGroup('default'));
    }

    public function testCanRemoveActionGroup(): void
    {
        /** @var ActionGroup|MockObject $actionGroupMock */
        $actionGroupMock = $this->createMock(ActionGroup::class);

        $actionGroupMock->expects($this->once())->method('getName')->willReturn('row');

        $this->grid->addActionGroup($actionGroupMock);
        $this->grid->removeActionGroup('row');

        $this->assertFalse($this->grid->hasActionGroup('row'));
    }

    public function testCanReplaceActionGroup(): void
    {
        /** @var ActionGroup|MockObject $firstActionGroupMock */
        $firstActionGroupMock = $this->createMock(ActionGroup::class);

        /** @var ActionGroup|MockObject $secondActionGroupMock */
        $secondActionGroupMock = $this->createMock(ActionGroup::class);

        $firstActionGroupMock->expects($this->once())->method('getName')->willReturn('row');
        $secondActionGroupMock->expects($this->once())->method('getName')->willReturn('row');

        $this->grid->addActionGroup($firstActionGroupMock);
        $this->grid->setActionGroup($secondActionGroupMock);

        $this->assertSame($secondActionGroupMock, $this->grid->getActionGroup('row'));
    }

    public function testCanReturnActionGroups(): void
    {
        /** @var ActionGroup|MockObject $firstActionGroupMock */
        $firstActionGroupMock = $this->createMock(ActionGroup::class);

        /** @var ActionGroup|MockObject $secondActionGroupMock */
        $secondActionGroupMock = $this->createMock(ActionGroup::class);

        $firstActionGroupMock->expects($this->once())->method('getName')->willReturn('first');
        $secondActionGroupMock->expects($this->once())->method('getName')->willReturn('second');

        $this->grid->addActionGroup($firstActionGroupMock);
        $this->grid->addActionGroup($secondActionGroupMock);

        $this->assertCount(2, $this->grid->getActionGroups());
    }

    public function testCanReturnOnlyEnabledActionGroups(): void
    {
        /** @var ActionGroup|MockObject $firstActionGroupMock */
        $firstActionGroupMock = $this->createMock(ActionGroup::class);

        /** @var ActionGroup|MockObject $secondActionGroupMock */
        $secondActionGroupMock = $this->createMock(ActionGroup::class);

        $firstActionGroupMock->expects($this->once())->method('getName')->willReturn('first');
        $secondActionGroupMock->expects($this->once())->method('getName')->willReturn('second');

        $this->grid->addActionGroup($firstActionGroupMock);
        $this->grid->addActionGroup($secondActionGroupMock);

        $this->assertCount(2, $this->grid->getEnabledActionGroups());
    }

    public function testReturnsActionsForGivenGroup(): void
    {
        /** @var ActionGroup|MockObject $actionGroupMock */
        $actionGroupMock = $this->createMock(ActionGroup::class);

        /** @var Action|MockObject $actionMock */
        $actionMock = $this->createMock(Action::class);

        $actionGroupMock->expects($this->once())->method('getName')->willReturn('row');
        $actionGroupMock->expects($this->once())->method('getActions')->willReturn([$actionMock]);

        $this->grid->addActionGroup($actionGroupMock);

        $this->assertSame([$actionMock], $this->grid->getActions('row'));
    }

    public function testReturnsOnlyEnabledActionsForGivenGroup(): void
    {
        /** @var ActionGroup|MockObject $actionGroupMock */
        $actionGroupMock = $this->createMock(ActionGroup::class);

        /** @var Action|MockObject $firstActionMock */
        $firstActionMock = $this->createMock(Action::class);

        /** @var Action|MockObject $secondActionMock */
        $secondActionMock = $this->createMock(Action::class);

        $firstActionMock->expects($this->once())->method('isEnabled')->willReturn(true);
        $secondActionMock->expects($this->once())->method('isEnabled')->willReturn(false);
        $actionGroupMock->expects($this->once())->method('getName')->willReturn('row');
        $actionGroupMock->expects($this->once())->method('getActions')->willReturn([$firstActionMock, $secondActionMock]);

        $this->grid->addActionGroup($actionGroupMock);

        $this->assertSame([$firstActionMock], $this->grid->getEnabledActions('row'));
    }

    public function testDoesNotHaveAnyFiltersByDefault(): void
    {
        $this->assertSame([], $this->grid->getFilters());
    }

    public function testCanHaveFilterDefinitions(): void
    {
        /** @var Filter|MockObject $filterMock */
        $filterMock = $this->createMock(Filter::class);

        $filterMock->expects($this->once())->method('getName')->willReturn('enabled');

        $this->grid->addFilter($filterMock);

        $this->assertSame($filterMock, $this->grid->getFilter('enabled'));
    }

    public function testCannotHaveTwoFiltersWithTheSameName(): void
    {
        /** @var Filter|MockObject $firstFilterMock */
        $firstFilterMock = $this->createMock(Filter::class);

        /** @var Filter|MockObject $secondFilterMock */
        $secondFilterMock = $this->createMock(Filter::class);

        $firstFilterMock->expects($this->once())->method('getName')->willReturn('created_at');
        $secondFilterMock->expects($this->once())->method('getName')->willReturn('created_at');

        $this->grid->addFilter($firstFilterMock);

        $this->expectException(\InvalidArgumentException::class);

        $this->grid->addFilter($secondFilterMock);
    }

    public function testKnowsIfFilterWithGivenNameAlreadyExists(): void
    {
        /** @var Filter|MockObject $filterMock */
        $filterMock = $this->createMock(Filter::class);

        $filterMock->expects($this->once())->method('getName')->willReturn('enabled');

        $this->grid->addFilter($filterMock);

        $this->assertTrue($this->grid->hasFilter('enabled'));
        $this->assertFalse($this->grid->hasFilter('created_at'));
    }

    public function testCanRemoveFilter(): void
    {
        /** @var Filter|MockObject $filterMock */
        $filterMock = $this->createMock(Filter::class);

        $filterMock->expects($this->once())->method('getName')->willReturn('enabled');

        $this->grid->addFilter($filterMock);
        $this->grid->removeFilter('enabled');

        $this->assertFalse($this->grid->hasFilter('enabled'));
    }

    public function testCanReplaceFilter(): void
    {
        /** @var Filter|MockObject $firstFilterMock */
        $firstFilterMock = $this->createMock(Filter::class);

        /** @var Filter|MockObject $secondFilterMock */
        $secondFilterMock = $this->createMock(Filter::class);

        $firstFilterMock->expects($this->once())->method('getName')->willReturn('enabled');
        $secondFilterMock->expects($this->once())->method('getName')->willReturn('enabled');

        $this->grid->addFilter($firstFilterMock);
        $this->grid->setFilter($secondFilterMock);

        $this->assertSame($secondFilterMock, $this->grid->getFilter('enabled'));
    }

    public function testCanReturnFilters(): void
    {
        /** @var Filter|MockObject $firstFilterMock */
        $firstFilterMock = $this->createMock(Filter::class);

        /** @var Filter|MockObject $secondFilterMock */
        $secondFilterMock = $this->createMock(Filter::class);

        $firstFilterMock->expects($this->once())->method('getName')->willReturn('first');
        $secondFilterMock->expects($this->once())->method('getName')->willReturn('second');

        $this->grid->addFilter($firstFilterMock);
        $this->grid->addFilter($secondFilterMock);

        $this->assertCount(2, $this->grid->getFilters());
    }

    public function testCanReturnOnlyEnabledFilters(): void
    {
        /** @var Filter|MockObject $firstFilterMock */
        $firstFilterMock = $this->createMock(Filter::class);

        /** @var Filter|MockObject $secondFilterMock */
        $secondFilterMock = $this->createMock(Filter::class);

        $firstFilterMock->expects($this->once())->method('getName')->willReturn('first');
        $firstFilterMock->expects($this->once())->method('isEnabled')->willReturn(true);
        $secondFilterMock->expects($this->once())->method('getName')->willReturn('second');
        $secondFilterMock->expects($this->once())->method('isEnabled')->willReturn(false);

        $this->grid->addFilter($firstFilterMock);
        $this->grid->addFilter($secondFilterMock);

        $this->assertCount(1, $this->grid->getEnabledFilters());
    }
}

final class GridProviderCallable
{
    public static function getData(): array
    {
        return ['callable' => true];
    }
}
