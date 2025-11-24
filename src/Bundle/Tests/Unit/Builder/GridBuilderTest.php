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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Builder;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ActionGroupInterface;
use Sylius\Bundle\GridBundle\Builder\Field\Field;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;

final class GridBuilderTest extends TestCase
{
    private GridBuilder $gridBuilder;

    protected function setUp(): void
    {
        $this->gridBuilder = GridBuilder::create('admin_book_grid', Book::class);
    }

    public function testImplementsAnInterface(): void
    {
        $this->assertInstanceOf(GridBuilderInterface::class, $this->gridBuilder);
    }

    public function testSetsDriver(): void
    {
        $this->gridBuilder->setDriver('doctrine/dbal');

        $this->assertSame('doctrine/dbal', $this->gridBuilder->toArray()['driver']['name']);
    }

    public function testSetsDriverOptions(): void
    {
        $gridBuilder = $this->gridBuilder->setDriverOption('pagination', [
            'fetch_join_collection' => false,
        ]);

        $this->assertSame([
            'fetch_join_collection' => false,
        ], $gridBuilder->toArray()['driver']['options']['pagination']);
    }

    public function testSetsARepositoryMethod(): void
    {
        $this->gridBuilder->setRepositoryMethod('createListQueryBuilder', []);

        $this->assertSame([
            'method' => 'createListQueryBuilder',
            'arguments' => [],
        ], $this->gridBuilder->toArray()['driver']['options']['repository']);
    }

    public function testSetsARepositoryMethodWithService(): void
    {
        $queryBuilder = new \stdClass();
        $this->gridBuilder->setRepositoryMethod([$queryBuilder, 'method'], []);

        $this->assertSame([
            'method' => [$queryBuilder, 'method'],
            'arguments' => [],
        ], $this->gridBuilder->toArray()['driver']['options']['repository']);
    }

    public function testSetsProviderWithAString(): void
    {
        $this->gridBuilder->setProvider('App/Driver');

        $this->assertSame('App/Driver', $this->gridBuilder->toArray()['provider']);
    }

    public function testSetsProviderWithACallable(): void
    {
        $this->gridBuilder->setProvider([CallableProvider::class, 'getData']);

        $this->assertIsCallable($this->gridBuilder->toArray()['provider']);
    }

    public function testAddsFields(): void
    {
        $field = Field::create('title', 'string');
        $this->gridBuilder->addField($field);

        $this->assertArrayHasKey('title', $this->gridBuilder->toArray()['fields']);
    }

    public function testRemoveFields(): void
    {
        $field = Field::create('title', 'string');
        $this->gridBuilder->addField($field);
        $this->gridBuilder->removeField('title');

        $this->assertArrayNotHasKey('fields', $this->gridBuilder->toArray());
        $this->assertContains('title', $this->gridBuilder->toArray()['removals']['fields']);
    }

    public function testSetsOrders(): void
    {
        $this->gridBuilder->orderBy('title');
        $this->gridBuilder->addOrderBy('createdAt', 'desc');

        $this->assertSame(['title' => 'asc', 'createdAt' => 'desc'], $this->gridBuilder->toArray()['sorting']);
    }

    public function testSetsLimits(): void
    {
        $this->gridBuilder->setLimits([10, 5, 25]);

        $this->assertSame([10, 5, 25], $this->gridBuilder->toArray()['limits']);
    }

    public function testAddsFilters(): void
    {
        $filter = Filter::create('search', 'string');
        $this->gridBuilder->addFilter($filter);

        $this->assertArrayHasKey('search', $this->gridBuilder->toArray()['filters']);
    }

    public function testRemoveFilters(): void
    {
        $filter = Filter::create('search', 'string');
        $this->gridBuilder->addFilter($filter);
        $this->gridBuilder->removeFilter('search');

        $this->assertArrayNotHasKey('filters', $this->gridBuilder->toArray());
        $this->assertContains('search', $this->gridBuilder->toArray()['removals']['filters']);
    }

    public function testAddsActionsGroups(): void
    {
        $actionGroup = $this->createMock(ActionGroupInterface::class);
        $actionGroup->method('getName')->willReturn(ActionGroupInterface::MAIN_GROUP);
        $actionGroup->method('toArray')->willReturn([]);

        $this->gridBuilder->addActionGroup($actionGroup);

        $this->assertArrayHasKey(ActionGroupInterface::MAIN_GROUP, $this->gridBuilder->toArray()['actions']);
    }

    public function testRemoveActionsGroups(): void
    {
        $actionGroup = ActionGroup::create('main');
        $this->gridBuilder->addActionGroup($actionGroup);
        $actionGroup = ActionGroup::create('item');
        $this->gridBuilder->addActionGroup($actionGroup);

        $this->gridBuilder->removeActionGroup('main');

        $this->assertArrayNotHasKey('main', $this->gridBuilder->toArray()['actions']);
        $this->assertContains('main', $this->gridBuilder->toArray()['removals']['actions']);
    }

    public function testAddsCreateActions(): void
    {
        $this->gridBuilder->addAction(CreateAction::create(), ActionGroupInterface::MAIN_GROUP);

        $this->assertArrayHasKey(ActionGroupInterface::MAIN_GROUP, $this->gridBuilder->toArray()['actions']);
        $this->assertArrayHasKey('create', $this->gridBuilder->toArray()['actions'][ActionGroupInterface::MAIN_GROUP]);
        $this->assertArrayHasKey('label', $this->gridBuilder->toArray()['actions'][ActionGroupInterface::MAIN_GROUP]['create']);
        $this->assertSame('sylius.ui.create', $this->gridBuilder->toArray()['actions'][ActionGroupInterface::MAIN_GROUP]['create']['label']);
    }

    public function testAddsCreateActionsOnASpecificGroup(): void
    {
        $this->gridBuilder->addAction(CreateAction::create(), 'custom');

        $this->assertArrayHasKey('custom', $this->gridBuilder->toArray()['actions']);
        $this->assertArrayHasKey('create', $this->gridBuilder->toArray()['actions']['custom']);
        $this->assertArrayHasKey('label', $this->gridBuilder->toArray()['actions']['custom']['create']);
        $this->assertSame('sylius.ui.create', $this->gridBuilder->toArray()['actions']['custom']['create']['label']);
    }

    public function testAddsShowActions(): void
    {
        $this->gridBuilder->addAction(ShowAction::create(), ActionGroupInterface::ITEM_GROUP);

        $this->assertArrayHasKey(ActionGroupInterface::ITEM_GROUP, $this->gridBuilder->toArray()['actions']);
        $this->assertArrayHasKey('show', $this->gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]);
        $this->assertArrayHasKey('label', $this->gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['show']);
        $this->assertSame('sylius.ui.show', $this->gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['show']['label']);
    }

    public function testAddsShowActionsOnASpecificGroup(): void
    {
        $this->gridBuilder->addAction(ShowAction::create(), 'custom');

        $this->assertArrayHasKey('custom', $this->gridBuilder->toArray()['actions']);
        $this->assertArrayHasKey('show', $this->gridBuilder->toArray()['actions']['custom']);
        $this->assertArrayHasKey('label', $this->gridBuilder->toArray()['actions']['custom']['show']);
        $this->assertSame('sylius.ui.show', $this->gridBuilder->toArray()['actions']['custom']['show']['label']);
    }

    public function testAddsUpdateActions(): void
    {
        $this->gridBuilder->addAction(UpdateAction::create(), ActionGroupInterface::ITEM_GROUP);

        $this->assertArrayHasKey(ActionGroupInterface::ITEM_GROUP, $this->gridBuilder->toArray()['actions']);
        $this->assertArrayHasKey('update', $this->gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]);
        $this->assertArrayHasKey('label', $this->gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['update']);
        $this->assertSame('sylius.ui.edit', $this->gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['update']['label']);
    }

    public function testAddsUpdateActionsOnASpecificGroup(): void
    {
        $this->gridBuilder->addAction(UpdateAction::create(), 'custom');

        $this->assertArrayHasKey('custom', $this->gridBuilder->toArray()['actions']);
        $this->assertArrayHasKey('update', $this->gridBuilder->toArray()['actions']['custom']);
        $this->assertArrayHasKey('label', $this->gridBuilder->toArray()['actions']['custom']['update']);
        $this->assertSame('sylius.ui.edit', $this->gridBuilder->toArray()['actions']['custom']['update']['label']);
    }

    public function testAddsDeleteActions(): void
    {
        $this->gridBuilder->addAction(DeleteAction::create(), ActionGroupInterface::ITEM_GROUP);

        $this->assertArrayHasKey(ActionGroupInterface::ITEM_GROUP, $this->gridBuilder->toArray()['actions']);
        $this->assertArrayHasKey('delete', $this->gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]);
        $this->assertArrayHasKey('label', $this->gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['delete']);
        $this->assertSame('sylius.ui.delete', $this->gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['delete']['label']);
    }

    public function testAddsDeleteActionsOnASpecificGroup(): void
    {
        $this->gridBuilder->addAction(DeleteAction::create(), 'custom');

        $this->assertArrayHasKey('custom', $this->gridBuilder->toArray()['actions']);
        $this->assertArrayHasKey('delete', $this->gridBuilder->toArray()['actions']['custom']);
        $this->assertArrayHasKey('label', $this->gridBuilder->toArray()['actions']['custom']['delete']);
        $this->assertSame('sylius.ui.delete', $this->gridBuilder->toArray()['actions']['custom']['delete']['label']);
    }

    public function testRemoveActions(): void
    {
        $action = Action::create('update', 'update');
        $this->gridBuilder->addAction($action, 'item');
        $action = Action::create('delete', 'delete');
        $this->gridBuilder->addAction($action, 'item');

        $this->gridBuilder->removeAction('delete', 'item');

        $this->assertArrayNotHasKey('delete', $this->gridBuilder->toArray()['actions']['item']);
        $this->assertContains('delete', $this->gridBuilder->toArray()['removals']['actions']['item']);
    }

    public function testCanBuildExtendedGrids(): void
    {
        $gridBuilder = $this->gridBuilder->extends('app_author');

        $this->assertSame('app_author', $gridBuilder->toArray()['extends']);
    }
}

final class CallableProvider
{
    public static function getData(): array
    {
        return [];
    }
}
