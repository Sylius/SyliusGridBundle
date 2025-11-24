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

use App\Kernel;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;
use Symfony\Component\DependencyInjection\Loader\Configurator\App;

$gridBuilder = GridBuilder::create('app_author_with_books_with_fetch_join_collection_enabled')
    ->extends('app_author')
    ->setRepositoryMethod('createWithBooksQueryBuilder')
;

if (Kernel::MAJOR_VERSION < 8) {
    return static function (GridConfig $grid) use ($gridBuilder): void {
        $grid->addGrid($gridBuilder);
    };
}

return App::config(['sylius_grid' => (new GridConfig())->addGrid($gridBuilder)->toArray()]);
