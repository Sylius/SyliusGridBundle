<?php

namespace spec\Sylius\Component\Grid\Builder;

use App\Entity\Book;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Builder\Action;
use Sylius\Component\Grid\Builder\Field;
use Sylius\Component\Grid\Builder\FieldInterface;
use Sylius\Component\Grid\Builder\Filter;
use Sylius\Component\Grid\Builder\GridBuilder;
use Sylius\Component\Grid\Definition\Grid;

class GridBuilderSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('admin_book_grid', Book::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GridBuilder::class);
    }

    function it_return_its_definition(): void
    {
        $this->getDefinition()->shouldHaveType(Grid::class);
    }

    function it_sets_a_repository_method(): void
    {
        $gridBuilder = $this->setRepositoryMethod('createListQueryBuilder', []);

        $gridBuilder->getDefinition()->getDriverConfiguration()->shouldReturn([
            'class' => 'App\Entity\Book',
            'repository' => [
                'method' => 'createListQueryBuilder',
                'arguments' => [],
            ]
        ]);
    }

    function it_add_fields(): void
    {
        $field = Field::create('title', 'string');
        $gridBuilder = $this->addField($field);

        $gridBuilder->getDefinition()->hasField('title')->shouldReturn(true);
    }

    function it_remove_fields(): void
    {
        $field = Field::create('title', 'string');
        $this->addField($field);
        $gridBuilder = $this->removeField('title');

        $gridBuilder->getDefinition()->hasField('title')->shouldReturn(false);
    }

    function it_sets_orders(): void
    {
        $gridBuilder = $this->orderBy('title', 'desc');

        $gridBuilder->getDefinition()->getSorting()->shouldReturn(['title' => 'desc']);
    }

    function it_add_filters(): void
    {
        $filter = Filter::create('search', 'string');
        $gridBuilder = $this->addFilter($filter);

        $gridBuilder->getDefinition()->hasFilter('search')->shouldReturn(true);
    }

    function it_remove_filters(): void
    {
        $filter = Filter::create('search', 'string');
        $this->addFilter($filter);
        $gridBuilder = $this->removeFilter('search');

        $gridBuilder->getDefinition()->hasFilter('search')->shouldReturn(false);
    }

    function it_add_actions_groups(): void
    {
        $gridBuilder = $this->addActionGroup('main');

        $gridBuilder->getDefinition()->hasActionGroup('main')->shouldReturn(true);
    }

    function it_add_main_actions(): void
    {
        $action = Action::create('create', 'create');
        $gridBuilder = $this->addMainAction($action);

        $gridBuilder->getDefinition()->hasActionGroup('main')->shouldReturn(true);
        $gridBuilder->getDefinition()->getActionGroup('main')->hasAction('create')->shouldReturn(true);
    }

    function it_add_item_actions(): void
    {
        $action = Action::create('update', 'update');
        $gridBuilder = $this->addItemAction($action);

        $gridBuilder->getDefinition()->hasActionGroup('item')->shouldReturn(true);
        $gridBuilder->getDefinition()->getActionGroup('item')->hasAction('update')->shouldReturn(true);
    }

    function it_add_create_actions(): void
    {
        $gridBuilder = $this->addCreateAction();

        $gridBuilder->getDefinition()->hasActionGroup('main')->shouldReturn(true);
        $gridBuilder->getDefinition()->getActionGroup('main')->hasAction('create')->shouldReturn(true);
    }

    function it_add_update_actions(): void
    {
        $gridBuilder = $this->addUpdateAction();

        $gridBuilder->getDefinition()->hasActionGroup('item')->shouldReturn(true);
        $gridBuilder->getDefinition()->getActionGroup('item')->hasAction('update')->shouldReturn(true);
    }

    function it_add_delete_actions(): void
    {
        $gridBuilder = $this->addDeleteAction();

        $gridBuilder->getDefinition()->hasActionGroup('item')->shouldReturn(true);
        $gridBuilder->getDefinition()->getActionGroup('item')->hasAction('delete')->shouldReturn(true);
    }
}
