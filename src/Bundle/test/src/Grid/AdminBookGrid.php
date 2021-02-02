<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\Book;
use Sylius\Component\Grid\AbstractGrid;
use Sylius\Component\Grid\Builder\Field;
use Sylius\Component\Grid\Builder\Filter;
use Sylius\Component\Grid\Builder\GridBuilderInterface;

class AdminBookGrid extends AbstractGrid
{
    public static function getName(): string
    {
        return 'app_backend_book';
    }

    public static function getResourceClass(): string
    {
        return Book::class;
    }

    protected function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createListQueryBuilder')
            ->addField(
                Field::create('title', 'string')
                    ->setLabel('sylius.ui.title')
                    ->setSortable(true)
            )
            ->addField(
                Field::create('author', 'string')
                    ->setLabel('app.ui.author')
                    ->setSortable(true)
            )
            ->orderBy('title', 'desc')
            ->addFilter(
                Filter::create('search', 'string')
                    ->setOptions([
                        'fields' => [
                            'title', 'author.firstName', 'author.lastName',
                        ],
                    ])
            )
            ->addCreateAction()
            ->addUpdateAction()
            ->addDeleteAction()
        ;
    }
}
