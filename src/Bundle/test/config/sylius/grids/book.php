<?php

use App\Entity\Book;
use Sylius\Component\Grid\Config\Builder\Action;
use Sylius\Component\Grid\Config\Builder\Field;
use Sylius\Component\Grid\Config\Builder\Filter;
use Sylius\Component\Grid\Config\Builder\GridBuilder;
use Sylius\Component\Grid\Config\Builder\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_book', Book::class)
        ->addField(Field::create('firstName', 'string')
            ->setLabel('sylius.ui.first_name')
            ->setSortable(true)
        )
        ->addField(Field::create('lastName', 'string')
            ->setLabel('sylius.ui.last_name')
            ->setSortable(true)
        )
        ->addOrderBy('lastName')
        ->addFilter(Filter::create('search', 'string')
            ->setOptions([
                'fields' => ['firstName', 'lastName']
            ])
        )
        ->addCreateAction()
        ->addItemAction(Action::create('showBooks', 'default')
            ->setLabel('app.ui.show_books')
            ->setIcon('book')
            ->setOptions([
                'link' => [
                    'route' => 'app_backend_book_index',
                    'parameters' => [
                        'criteria' => ['author' => 'resource.id'],
                    ],
                ],
            ])
        )
        ->addUpdateAction()
        ->addDeleteAction()
    );
};
