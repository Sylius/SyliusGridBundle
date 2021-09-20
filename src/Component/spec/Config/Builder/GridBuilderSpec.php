<?php

declare(strict_types=1);

namespace spec\Sylius\Component\Grid\Config\Builder;

use App\Entity\Book;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Config\Builder\Action\Action;
use Sylius\Component\Grid\Config\Builder\Action\CreateAction;
use Sylius\Component\Grid\Config\Builder\Action\DeleteAction;
use Sylius\Component\Grid\Config\Builder\Action\ShowAction;
use Sylius\Component\Grid\Config\Builder\Action\UpdateAction;
use Sylius\Component\Grid\Config\Builder\ActionGroup\ActionGroupInterface;
use Sylius\Component\Grid\Config\Builder\Field\Field;
use Sylius\Component\Grid\Config\Builder\Filter\Filter;
use Sylius\Component\Grid\Config\Builder\GridBuilder;
use Sylius\Component\Grid\Config\Builder\GridBuilderInterface;

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
        $actionGroup->getName()->willReturn('main');
        $actionGroup->toArray()->willReturn([]);

        $gridBuilder = $this->addActionGroup($actionGroup);

        $gridBuilder->toArray()['actions']->shouldHaveKey('main');
    }

    function it_adds_main_actions(): void
    {
        $action = Action::create('create', 'create');
        $gridBuilder = $this->addMainAction($action);

        $gridBuilder->toArray()['actions']->shouldHaveKey('main');
        $gridBuilder->toArray()['actions']['main']->shouldHaveKey('create');
    }

    function it_adds_item_actions(): void
    {
        $action = Action::create('update', 'update');
        $gridBuilder = $this->addItemAction($action);

        $gridBuilder->toArray()['actions']->shouldHaveKey('item');
        $gridBuilder->toArray()['actions']['item']->shouldHaveKey('update');
    }

    function it_adds_sub_item_actions(): void
    {
        $action = Action::create('addresses', 'links');
        $gridBuilder = $this->addSubItemAction($action);

        $gridBuilder->toArray()['actions']->shouldHaveKey('subitem');
        $gridBuilder->toArray()['actions']['subitem']->shouldHaveKey('addresses');
    }

    function it_adds_create_actions(): void
    {
        $gridBuilder = $this->addMainAction(CreateAction::create());

        $gridBuilder->toArray()['actions']->shouldHaveKey('main');
        $gridBuilder->toArray()['actions']['main']->shouldHaveKey('create');
        $gridBuilder->toArray()['actions']['main']['create']->shouldHaveKey('label');
        $gridBuilder->toArray()['actions']['main']['create']['label']->shouldReturn('sylius.ui.create');
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
        $gridBuilder = $this->addItemAction(ShowAction::create());

        $gridBuilder->toArray()['actions']->shouldHaveKey('item');
        $gridBuilder->toArray()['actions']['item']->shouldHaveKey('show');
        $gridBuilder->toArray()['actions']['item']['show']->shouldHaveKey('label');
        $gridBuilder->toArray()['actions']['item']['show']['label']->shouldReturn('sylius.ui.show');
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
        $gridBuilder = $this->addItemAction(UpdateAction::create());

        $gridBuilder->toArray()['actions']->shouldHaveKey('item');
        $gridBuilder->toArray()['actions']['item']->shouldHaveKey('update');
        $gridBuilder->toArray()['actions']['item']['update']->shouldHaveKey('label');
        $gridBuilder->toArray()['actions']['item']['update']['label']->shouldReturn('sylius.ui.update');
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
        $gridBuilder = $this->addItemAction(DeleteAction::create());

        $gridBuilder->toArray()['actions']->shouldHaveKey('item');
        $gridBuilder->toArray()['actions']['item']->shouldHaveKey('delete');
        $gridBuilder->toArray()['actions']['item']['delete']->shouldHaveKey('label');
        $gridBuilder->toArray()['actions']['item']['delete']['label']->shouldReturn('sylius.ui.delete');
    }

    function it_adds_delete_actions_on_a_specific_group(): void
    {
        $gridBuilder = $this->addAction(DeleteAction::create(), 'custom');

        $gridBuilder->toArray()['actions']->shouldHaveKey('custom');
        $gridBuilder->toArray()['actions']['custom']->shouldHaveKey('delete');
        $gridBuilder->toArray()['actions']['custom']['delete']->shouldHaveKey('label');
        $gridBuilder->toArray()['actions']['custom']['delete']['label']->shouldReturn('sylius.ui.delete');
    }

    function it_adds_bulk_actions(): void
    {
        $gridBuilder = $this->addBulkAction(DeleteAction::create());

        $gridBuilder->toArray()['actions']->shouldHaveKey('bulk');
        $gridBuilder->toArray()['actions']['bulk']->shouldHaveKey('delete');
        $gridBuilder->toArray()['actions']['bulk']['delete']->shouldHaveKey('label');
        $gridBuilder->toArray()['actions']['bulk']['delete']['label']->shouldReturn('sylius.ui.delete');
    }
}
