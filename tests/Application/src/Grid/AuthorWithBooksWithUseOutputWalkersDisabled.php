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

use App\Entity\Author;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class AuthorWithBooksWithUseOutputWalkersDisabled extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
        return 'app_author_with_books_with_use_output_walkers_disabled';
    }

    public function getResourceClass(): string
    {
        return Author::class;
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->extends('app_author')
            ->setRepositoryMethod(["expr:service('app.authors_with_books_query_builder')", 'create'])
            ->setDriverOption('pagination', ['use_output_walkers' => false])
            ->addField(
                StringField::create('book')
                ->setSortable(true, 'book.title'),
            )
        ;
    }
}
