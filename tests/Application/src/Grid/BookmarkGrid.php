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

namespace App\Grid;

use App\Entity\Book;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Component\Grid\Attribute\AsGrid;
use Sylius\Component\Grid\Builder\GridBuilderInterface;

#[AsGrid(resourceClass: Book::class, name: 'app_bookmark')]
final class BookmarkGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->extends('app_book')
            ->addField(StringField::create('id'))
            ->removeField('nationality')
            ->removeFilter('nationality')
            ->removeActionGroup('main')
            ->removeAction('edit', 'item')
            ->removeAction('delete', 'item')
        ;
    }
}
