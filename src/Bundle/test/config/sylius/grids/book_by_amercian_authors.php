<?php

use App\Entity\Book;
use App\Entity\Nationality;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_book_by_american_authors', Book::class)
        ->setRepositoryMethod('createAmericanBooksQueryBuilder')
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
        ->addField(StringField::create('title')
            ->setLabel('Title')
            ->setSortable(true)
        )
        ->addField(StringField::create('author')
            ->setLabel('Author')
            ->setPath('author.name')
            ->setSortable(true, 'author.name')
        )
        ->addField(StringField::create('nationality')
            ->setLabel('Nationality')
            ->setPath('na.name')
            ->setSortable(true, 'na.name')
        )
        ->setLimits([10, 5, 15])
    );
};
