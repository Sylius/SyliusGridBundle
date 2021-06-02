<?php

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Nationality;
use Sylius\Component\Grid\Config\Builder\Action;
use Sylius\Component\Grid\Config\Builder\Field;
use Sylius\Component\Grid\Config\Builder\Filter;
use Sylius\Component\Grid\Config\Builder\GridBuilder;
use Sylius\Component\Grid\Config\Builder\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_book', Book::class)
        ->addFilter(Filter::create('title', 'string'))
        ->addFilter(Filter::create('author', 'entity')
            ->setFormOptions([
                'class' => Author::class,
                'multiple' => true,
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
        ->addFilter(Filter::create('currencyCode', 'string')
            ->setOptions([
                'fields' => ['price.currencyCode'],
            ])
        )
        ->addFilter(Filter::create('state', 'select')
            ->setFormOptions([
                'multiple' => true,
                'choices' => [
                    'initial' => 'initial',
                    'published' => 'published',
                    'unpublished' => 'unpublished',
                ],
            ])
        )
        ->orderBy('title', 'asc')
        ->addField(
            Field::create('title', 'string')
                ->setLabel('Title')
                ->setSortable(true)
        )
        ->addField(
            Field::create('author', 'string')
                ->setLabel('Author')
                ->setPath('author.name')
                ->setSortable(true, 'author.name')
        )
        ->addField(
            Field::create('nationality', 'string')
                ->setLabel('Nationality')
                ->setPath('author.nationality.name')
                ->setSortable(true, 'author.nationality.name')
        )
        ->setLimits([10, 5, 15])
    );
};
