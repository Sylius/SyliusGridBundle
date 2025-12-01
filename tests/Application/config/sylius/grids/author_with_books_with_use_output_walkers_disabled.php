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

use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(
        GridBuilder::create('app_author_with_books_with_use_output_walkers_disabled')
            ->extends('app_author')
            ->setRepositoryMethod('createWithBooksQueryBuilder')
            ->setDriverOption('pagination', ['use_output_walkers' => false])
            ->addField(
                StringField::create('book')
                    ->setPath('books[0].title')
                ->setSortable(true, 'book.title'),
            ),
    );
};
