<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Builder;

use App\Entity\Book;
use PhpSpec\ObjectBehavior;
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

final class GridBuilderSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedThrough('create', ['admin_book_grid', Book::class]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(GridBuilder::class);
    }

    function it_implements_an_interface(): void
    {
        $this->shouldImplement(GridBuilderInterface::class);
    }

    function it_sets_driver(): void
    {
        $this->setDriver('doctrine/dbal');

        $this->toArray()['driver']['name']->shouldReturn('doctrine/dbal');
    }

    function it_sets_driver_options(): void
    {
        $gridBuilder = $this->setDriverOption('pagination', [
            'fetch_join_collection' => false,
        ]);

        $gridBuilder->toArray()['driver']['options']['pagination']->shouldReturn([
            'fetch_join_collection' => false,
        ]);
    }

    function it_sets_a_repository_method(): void
    {
        $this->setRepositoryMethod('createListQueryBuilder', []);

        $this->toArray()['driver']['options']['repository']->shouldReturn([
            'method' => 'createListQueryBuilder',
            'arguments' => [],
        ]);
    }

    function it_sets_a_repository_method_with_service(): void
    {
        $queryBuilder = new \stdClass();
        $this->setRepositoryMethod([$queryBuilder, 'method'], []);

        $this->toArray()['driver']['options']['repository']->shouldReturn([
            'method' => [$queryBuilder, 'method'],
            'arguments' => [],
        ]);
    }

    function it_adds_fields(): void
    {
        $field = Field::create('title', 'string');
        $this->addField($field);

        $this->toArray()['fields']->shouldHaveKey('title');
    }

    function it_remove_fields(): void
    {
        $field = Field::create('title', 'string');
        $this->addField($field);
        $this->removeField('title');

        $this->toArray()->shouldNotHaveKey('fields');
    }

    function it_sets_orders(): void
    {
        $this->orderBy('title');
        $this->addOrderBy('createdAt', 'desc');

        $this->toArray()['sorting']->shouldReturn(['title' => 'asc', 'createdAt' => 'desc']);
    }

    function it_sets_limits(): void
    {
        $this->setLimits([10, 5, 25]);

        $this->toArray()['limits']->shouldReturn([10, 5, 25]);
    }

    function it_adds_filters(): void
    {
        $filter = Filter::create('search', 'string');
        $this->addFilter($filter);

        $this->toArray()['filters']->shouldHaveKey('search');
    }

    function it_remove_filters(): void
    {
        $filter = Filter::create('search', 'string');
        $this->addFilter($filter);
        $this->removeFilter('search');

        $this->toArray()->shouldNotHaveKey('filters');
    }

    function it_adds_actions_groups(ActionGroupInterface $actionGroup): void
    {
        $actionGroup->getName()->willReturn(ActionGroupInterface::MAIN_GROUP);
        $actionGroup->toArray()->willReturn([]);

        $this->addActionGroup($actionGroup);

        $this->toArray()['actions']->shouldHaveKey(ActionGroupInterface::MAIN_GROUP);
    }

    function it_remove_actions_groups(): void
    {
        $actionGroup = ActionGroup::create('main');
        $this->addActionGroup($actionGroup);
        $actionGroup = ActionGroup::create('item');
        $this->addActionGroup($actionGroup);

        $this->removeActionGroup('main');

        $this->toArray()['actions']->shouldNotHaveKey('main');
    }

    function it_adds_create_actions(): void
    {
        $this->addAction(CreateAction::create(), ActionGroupInterface::MAIN_GROUP);

        $this->toArray()['actions']->shouldHaveKey(ActionGroupInterface::MAIN_GROUP);
        $this->toArray()['actions'][ActionGroupInterface::MAIN_GROUP]->shouldHaveKey('create');
        $this->toArray()['actions'][ActionGroupInterface::MAIN_GROUP]['create']->shouldHaveKey('label');
        $this->toArray()['actions'][ActionGroupInterface::MAIN_GROUP]['create']['label']->shouldReturn('sylius.ui.create');
    }

    function it_adds_create_actions_on_a_specific_group(): void
    {
        $this->addAction(CreateAction::create(), 'custom');

        $this->toArray()['actions']->shouldHaveKey('custom');
        $this->toArray()['actions']['custom']->shouldHaveKey('create');
        $this->toArray()['actions']['custom']['create']->shouldHaveKey('label');
        $this->toArray()['actions']['custom']['create']['label']->shouldReturn('sylius.ui.create');
    }

    function it_adds_show_actions(): void
    {
        $this->addAction(ShowAction::create(), ActionGroupInterface::ITEM_GROUP);

        $this->toArray()['actions']->shouldHaveKey(ActionGroupInterface::ITEM_GROUP);
        $this->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]->shouldHaveKey('show');
        $this->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['show']->shouldHaveKey('label');
        $this->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['show']['label']->shouldReturn('sylius.ui.show');
    }

    function it_adds_show_actions_on_a_specific_group(): void
    {
        $this->addAction(ShowAction::create(), 'custom');

        $this->toArray()['actions']->shouldHaveKey('custom');
        $this->toArray()['actions']['custom']->shouldHaveKey('show');
        $this->toArray()['actions']['custom']['show']->shouldHaveKey('label');
        $this->toArray()['actions']['custom']['show']['label']->shouldReturn('sylius.ui.show');
    }

    function it_adds_update_actions(): void
    {
        $this->addAction(UpdateAction::create(), ActionGroupInterface::ITEM_GROUP);

        $this->toArray()['actions']->shouldHaveKey(ActionGroupInterface::ITEM_GROUP);
        $this->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]->shouldHaveKey('update');
        $this->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['update']->shouldHaveKey('label');
        $this->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['update']['label']->shouldReturn('sylius.ui.edit');
    }

    function it_adds_update_actions_on_a_specific_group(): void
    {
        $this->addAction(UpdateAction::create(), 'custom');

        $this->toArray()['actions']->shouldHaveKey('custom');
        $this->toArray()['actions']['custom']->shouldHaveKey('update');
        $this->toArray()['actions']['custom']['update']->shouldHaveKey('label');
        $this->toArray()['actions']['custom']['update']['label']->shouldReturn('sylius.ui.edit');
    }

    function it_adds_delete_actions(): void
    {
        $this->addAction(DeleteAction::create(), ActionGroupInterface::ITEM_GROUP);

        $this->toArray()['actions']->shouldHaveKey(ActionGroupInterface::ITEM_GROUP);
        $this->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]->shouldHaveKey('delete');
        $this->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['delete']->shouldHaveKey('label');
        $this->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['delete']['label']->shouldReturn('sylius.ui.delete');
    }

    function it_adds_delete_actions_on_a_specific_group(): void
    {
        $this->addAction(DeleteAction::create(), 'custom');

        $this->toArray()['actions']->shouldHaveKey('custom');
        $this->toArray()['actions']['custom']->shouldHaveKey('delete');
        $this->toArray()['actions']['custom']['delete']->shouldHaveKey('label');
        $this->toArray()['actions']['custom']['delete']['label']->shouldReturn('sylius.ui.delete');
    }

    function it_remove_actions(): void
    {
        $action = Action::create('update', 'update');
        $this->addAction($action, 'item');
        $action = Action::create('delete', 'delete');
        $this->addAction($action, 'item');

        $this->removeAction('delete', 'item');

        $this->toArray()['actions']['item']->shouldNotHaveKey('delete');
    }

    function it_can_build_extended_grids(): void
    {
        $gridBuilder = $this->extends('app_author');

        $gridBuilder->toArray()['extends']->shouldReturn('app_author');
    }
}
