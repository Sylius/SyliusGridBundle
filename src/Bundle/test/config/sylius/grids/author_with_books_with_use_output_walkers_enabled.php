<?php

use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_author_with_books_with_use_output_walkers_enabled')
        ->extends('app_author')
        ->setRepositoryMethod(["expr:service('app.authors_with_books_query_builder')", 'create'])
        ->addField(StringField::create('book')
            ->setSortable(true, 'book.title')
        )
    );
};
