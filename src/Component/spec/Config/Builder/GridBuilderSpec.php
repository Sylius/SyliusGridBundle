<?php

namespace spec\Sylius\Component\Grid\Config\Builder;

use App\Entity\Book;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Config\Builder\Action;
use Sylius\Component\Grid\Config\Builder\Field;
use Sylius\Component\Grid\Config\Builder\Filter;
use Sylius\Component\Grid\Config\Builder\GridBuilder;

class GridBuilderSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('admin_book_grid', Book::class);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(GridBuilder::class);
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

    function it_add_fields(): void
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

    function it_add_filters(): void
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

    function it_add_actions_groups(): void
    {
        $gridBuilder = $this->addActionGroup('main');

        $gridBuilder->toArray()['actions']->shouldHaveKey('main');
    }

    function it_add_main_actions(): void
    {
        $action = Action::create('create', 'create');
        $gridBuilder = $this->addMainAction($action);

        $gridBuilder->toArray()['actions']->shouldHaveKey('main');
        $gridBuilder->toArray()['actions']['main']->shouldHaveKey('create');
    }

    function it_add_item_actions(): void
    {
        $action = Action::create('update', 'update');
        $gridBuilder = $this->addItemAction($action);

        $gridBuilder->toArray()['actions']->shouldHaveKey('item');
        $gridBuilder->toArray()['actions']['item']->shouldHaveKey('update');
    }

    function it_add_create_actions(): void
    {
        $gridBuilder = $this->addCreateAction();

        $gridBuilder->toArray()['actions']->shouldHaveKey('main');
        $gridBuilder->toArray()['actions']['main']->shouldHaveKey('create');
    }

    function it_add_update_actions(): void
    {
        $gridBuilder = $this->addUpdateAction();

        $gridBuilder->toArray()['actions']->shouldHaveKey('item');
        $gridBuilder->toArray()['actions']['item']->shouldHaveKey('update');
    }

    function it_add_delete_actions(): void
    {
        $gridBuilder = $this->addDeleteAction();

        $gridBuilder->toArray()['actions']->shouldHaveKey('item');
        $gridBuilder->toArray()['actions']['item']->shouldHaveKey('delete');
    }
}
