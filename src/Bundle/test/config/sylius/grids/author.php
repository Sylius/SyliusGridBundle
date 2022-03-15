<?php

use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_author', '%app.model.author.class%')
        ->addFilter(StringFilter::create('name'))
        ->orderBy('name', 'asc')
        ->addField(StringField::create('id')
            ->setSortable(true)
            ->setEnabled(false)
        )
        ->addField(StringField::create('name')
            ->setLabel('Name')
            ->setSortable(true)
        )
        ->addField(StringField::create('nationality')
            ->setLabel('Name')
            ->setPath('nationality.name')
            ->setSortable(true, 'nationality.name')
        )
        ->setLimits([10, 5, 15])
    );
};
