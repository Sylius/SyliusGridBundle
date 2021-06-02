<?php

use App\Entity\Book;
use App\Entity\Nationality;
use App\QueryBuilder\EnglishBooksQueryBuilder;
use Sylius\Component\Grid\Config\Builder\Field;
use Sylius\Component\Grid\Config\Builder\Filter;
use Sylius\Component\Grid\Config\Builder\GridBuilder;
use Sylius\Component\Grid\Config\Builder\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_book_by_english_authors', Book::class)
        ->setRepositoryMethod([EnglishBooksQueryBuilder::class, 'create'])
        ->addFilter(Filter::create('title', 'string'))
        ->addFilter(Filter::create('author', 'entity')
            ->setFormOptions([
                'class' => Nationality::class,
            ])
        )
        ->addFilter(Filter::create('nationality', 'entity')
            ->setOptions([
                'fields' => ['author.nationality'],
            ])
            ->setFormOptions([
                'class' => Nationality::class,
            ])
        )
        ->orderBy('title', 'asc')
        ->addField(Field::create('title', 'string')
            ->setLabel('Title')
            ->setSortable(true)
        )
        ->addField(Field::create('author', 'string')
            ->setLabel('Author')
            ->setPath('author.name')
            ->setSortable(true, 'author.name')
        )
        ->addField(Field::create('nationality', 'string')
            ->setLabel('Nationality')
            ->setPath('na.name')
            ->setSortable(true, 'na.name')
        )
        ->setLimits([10, 5, 15])
    );
};
