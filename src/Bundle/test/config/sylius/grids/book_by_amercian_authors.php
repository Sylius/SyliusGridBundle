<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use App\Entity\Book;
use App\Entity\Nationality;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\EntityFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(
        GridBuilder::create('app_book_by_american_authors', Book::class)
        ->setRepositoryMethod('createAmericanBooksQueryBuilder')
        ->addFilter(StringFilter::create('title'))
        ->addFilter(EntityFilter::create('author', Nationality::class))
        ->addFilter(EntityFilter::create(
            'nationality',
            Nationality::class,
            null,
            ['author.nationality'],
        ))
        ->orderBy('title', 'asc')
        ->addField(
            StringField::create('title')
            ->setLabel('Title')
            ->setSortable(true),
        )
        ->addField(
            StringField::create('author')
            ->setLabel('Author')
            ->setPath('author.name')
            ->setSortable(true, 'author.name'),
        )
        ->addField(
            StringField::create('nationality')
            ->setLabel('Nationality')
            ->setPath('na.name')
            ->setSortable(true, 'na.name'),
        )
        ->setLimits([10, 5, 15]),
    );
};
