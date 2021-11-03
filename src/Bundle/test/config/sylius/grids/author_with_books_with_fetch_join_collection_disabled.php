<?php

use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_author_with_books_with_fetch_join_collection_disabled')
        ->extends('app_author')
        ->setRepositoryMethod(["expr:service('app.authors_with_books_query_builder')", 'create'])
        ->setDriverOption('pagination', ['fetch_join_collection' => false])
    );
};
