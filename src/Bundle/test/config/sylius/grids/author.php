<?php

use App\Entity\Author;
use Sylius\Bundle\GridBundle\Builder\Field\Field;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_author', '%app.model.author.class%')
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
