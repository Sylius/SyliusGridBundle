<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Sylius\Bundle\GridBundle\Builder\Field\CallableField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(
        GridBuilder::create('app_author', '%app.model.author.class%')
            ->addFilter(StringFilter::create('name'))
            ->orderBy('name', 'asc')
            ->addField(
                CallableField::create('id', ['App\\Helper\\GridHelper', 'addHashPrefix'])
                    ->setSortable(true),
            )
            ->addField(
                StringField::create('name')
                    ->setLabel('Name')
                    ->setSortable(true),
            )
            ->addField(
                StringField::create('nationality')
                    ->setLabel('Nationality')
                    ->setSortable(true, 'nationality.name'),
            )
            ->setLimits([10, 5, 15]),
    );
};
