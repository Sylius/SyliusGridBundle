<?php

use App\Entity\Author;
use Sylius\Component\Grid\Config\Builder\Field;
use Sylius\Component\Grid\Config\Builder\Filter;
use Sylius\Component\Grid\Config\Builder\GridBuilder;
use Sylius\Component\Grid\Config\Builder\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_author', Author::class)
        ->addFilter(Filter::create('name', 'string'))
        ->orderBy('name', 'asc')
        ->addField(Field::create('name', 'string')
            ->setLabel('Name')
            ->setSortable(true)
        )
        ->addField(Field::create('nationality', 'string')
            ->setLabel('Name')
            ->setPath('nationality.name')
            ->setSortable(true, 'nationality.name')
        )
        ->setLimits([10, 5, 15])
    );
};
