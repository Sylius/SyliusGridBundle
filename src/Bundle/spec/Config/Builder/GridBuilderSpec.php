<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Config\Builder;

use App\Entity\Book;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Config\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Config\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Config\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Config\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Config\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Config\Builder\ActionGroup\ActionGroup;
use Sylius\Bundle\GridBundle\Config\Builder\ActionGroup\ActionGroupInterface;
use Sylius\Bundle\GridBundle\Config\Builder\Field\Field;
use Sylius\Bundle\GridBundle\Config\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Config\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\Builder\GridBuilderInterface;

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
        $gridBuilder = $this->setDriver('doctrine/dbal');

        $gridBuilder->toArray()['driver']['name']->shouldReturn('doctrine/dbal');
    }

    function it_sets_a_repository_method(): void
    {
        $gridBuilder = $this->setRepositoryMethod('createListQueryBuilder', []);

        $gridBuilder->toArray()['driver']['options']['repository']->shouldReturn([
            'method' => 'createListQueryBuilder',
            'arguments' => [],
        ]);
    }

    function it_sets_a_repository_method_with_service(): void
    {
        $queryBuilder = new \stdClass();
        $gridBuilder = $this->setRepositoryMethod([$queryBuilder, 'method'], []);

        $gridBuilder->toArray()['driver']['options']['repository']->shouldReturn([
            'method' => [$queryBuilder, 'method'],
            'arguments' => [],
        ]);
    }

    function it_adds_fields(): void
    {
        $field = Field::create('title', 'string');
        $gridBuilder = $this->addField($field);

        $gridBuilder->toArray()['fields']->shouldHaveKey('title');
    }

    function it_remove_fields(): void
    {
        $field = Field::create('title', 'string');
        $this->addField($field);
        $gridBuilder = $this->removeField('title');

        $gridBuilder->toArray()->shouldNotHaveKey('fields');
    }

    function it_sets_orders(): void
    {
        $this->orderBy('title');
        $gridBuilder = $this->addOrderBy('createdAt', 'desc');

        $gridBuilder->toArray()['sorting']->shouldReturn(['title' => 'asc', 'createdAt' => 'desc']);
    }

    function it_sets_limits(): void
    {
        $gridBuilder = $this->setLimits([10, 5, 25]);

        $gridBuilder->toArray()['limits']->shouldReturn([10, 5, 25]);
    }

    function it_adds_filters(): void
    {
        $filter = Filter::create('search', 'string');
        $gridBuilder = $this->addFilter($filter);

        $gridBuilder->toArray()['filters']->shouldHaveKey('search');
    }

    function it_remove_filters(): void
    {
        $filter = Filter::create('search', 'string');
        $this->addFilter($filter);
        $gridBuilder = $this->removeFilter('search');

        $gridBuilder->toArray()->shouldNotHaveKey('filters');
    }

    function it_adds_actions_groups(ActionGroupInterface $actionGroup): void
    {
        $actionGroup->getName()->willReturn(ActionGroupInterface::MAIN_GROUP);
        $actionGroup->toArray()->willReturn([]);

        $gridBuilder = $this->addActionGroup($actionGroup);

        $gridBuilder->toArray()['actions']->shouldHaveKey(ActionGroupInterface::MAIN_GROUP);
    }

    function it_remove_actions_groups(): void
    {
        $actionGroup = ActionGroup::create('main');
        $this->addActionGroup($actionGroup);
        $actionGroup = ActionGroup::create('item');
        $this->addActionGroup($actionGroup);

        $gridBuilder = $this->removeActionGroup('main');

        $gridBuilder->toArray()['actions']->shouldNotHaveKey('main');
    }

    function it_adds_create_actions(): void
    {
        $gridBuilder = $this->addAction(CreateAction::create(), ActionGroupInterface::MAIN_GROUP);

        $gridBuilder->toArray()['actions']->shouldHaveKey(ActionGroupInterface::MAIN_GROUP);
        $gridBuilder->toArray()['actions'][ActionGroupInterface::MAIN_GROUP]->shouldHaveKey('create');
        $gridBuilder->toArray()['actions'][ActionGroupInterface::MAIN_GROUP]['create']->shouldHaveKey('label');
        $gridBuilder->toArray()['actions'][ActionGroupInterface::MAIN_GROUP]['create']['label']->shouldReturn('sylius.ui.create');
    }

    function it_adds_create_actions_on_a_specific_group(): void
    {
        $gridBuilder = $this->addAction(CreateAction::create(), 'custom');

        $gridBuilder->toArray()['actions']->shouldHaveKey('custom');
        $gridBuilder->toArray()['actions']['custom']->shouldHaveKey('create');
        $gridBuilder->toArray()['actions']['custom']['create']->shouldHaveKey('label');
        $gridBuilder->toArray()['actions']['custom']['create']['label']->shouldReturn('sylius.ui.create');
    }

    function it_adds_show_actions(): void
    {
        $gridBuilder = $this->addAction(ShowAction::create(), ActionGroupInterface::ITEM_GROUP);

        $gridBuilder->toArray()['actions']->shouldHaveKey(ActionGroupInterface::ITEM_GROUP);
        $gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]->shouldHaveKey('show');
        $gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['show']->shouldHaveKey('label');
        $gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['show']['label']->shouldReturn('sylius.ui.show');
    }

    function it_adds_show_actions_on_a_specific_group(): void
    {
        $gridBuilder = $this->addAction(ShowAction::create(), 'custom');

        $gridBuilder->toArray()['actions']->shouldHaveKey('custom');
        $gridBuilder->toArray()['actions']['custom']->shouldHaveKey('show');
        $gridBuilder->toArray()['actions']['custom']['show']->shouldHaveKey('label');
        $gridBuilder->toArray()['actions']['custom']['show']['label']->shouldReturn('sylius.ui.show');
    }

    function it_adds_update_actions(): void
    {
        $gridBuilder = $this->addAction(UpdateAction::create(), ActionGroupInterface::ITEM_GROUP);

        $gridBuilder->toArray()['actions']->shouldHaveKey(ActionGroupInterface::ITEM_GROUP);
        $gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]->shouldHaveKey('update');
        $gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['update']->shouldHaveKey('label');
        $gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['update']['label']->shouldReturn('sylius.ui.update');
    }

    function it_adds_update_actions_on_a_specific_group(): void
    {
        $gridBuilder = $this->addAction(UpdateAction::create(), 'custom');

        $gridBuilder->toArray()['actions']->shouldHaveKey('custom');
        $gridBuilder->toArray()['actions']['custom']->shouldHaveKey('update');
        $gridBuilder->toArray()['actions']['custom']['update']->shouldHaveKey('label');
        $gridBuilder->toArray()['actions']['custom']['update']['label']->shouldReturn('sylius.ui.update');
    }

    function it_adds_delete_actions(): void
    {
        $gridBuilder = $this->addAction(DeleteAction::create(), ActionGroupInterface::ITEM_GROUP);

        $gridBuilder->toArray()['actions']->shouldHaveKey(ActionGroupInterface::ITEM_GROUP);
        $gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]->shouldHaveKey('delete');
        $gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['delete']->shouldHaveKey('label');
        $gridBuilder->toArray()['actions'][ActionGroupInterface::ITEM_GROUP]['delete']['label']->shouldReturn('sylius.ui.delete');
    }

    function it_adds_delete_actions_on_a_specific_group(): void
    {
        $gridBuilder = $this->addAction(DeleteAction::create(), 'custom');

        $gridBuilder->toArray()['actions']->shouldHaveKey('custom');
        $gridBuilder->toArray()['actions']['custom']->shouldHaveKey('delete');
        $gridBuilder->toArray()['actions']['custom']['delete']->shouldHaveKey('label');
        $gridBuilder->toArray()['actions']['custom']['delete']['label']->shouldReturn('sylius.ui.delete');
    }

    function it_remove_actions(): void
    {
        $action = Action::create('update', 'update');
        $this->addAction($action, 'item');
        $action = Action::create('delete', 'delete');
        $this->addAction($action, 'item');

        $gridBuilder = $this->removeAction('delete', 'item');

        $gridBuilder->toArray()['actions']['item']->shouldNotHaveKey('delete');
    }
}
