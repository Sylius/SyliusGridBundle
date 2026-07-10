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

use App\Entity\Author;
use App\Kernel;
use Sylius\Bundle\GridBundle\Builder\Field\CallableField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Config\GridConfig;
use Sylius\Component\Grid\Builder\GridBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\App;

$gridBuilder = GridBuilder::create('app_author', Author::class)
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
        CallableField::createForService('nationality', \App\Helper\GridHelper::class)
            ->setLabel('Nationality')
            ->setSortable(true, 'nationality.name'),
    )
    ->setLimits([10, 5, 15, 100])
;

if (Kernel::MAJOR_VERSION < 8) {
    return static function (GridConfig $grid) use ($gridBuilder) {
        $grid->addGrid($gridBuilder);
    };
}

return App::config(['sylius_grid' => (new GridConfig())->addGrid($gridBuilder)->toArray()]);
