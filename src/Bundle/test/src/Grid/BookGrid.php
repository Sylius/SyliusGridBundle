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

namespace App\Grid;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Nationality;
use Sylius\Bundle\GridBundle\AbstractGrid;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;

class BookGrid extends AbstractGrid
{
    public static function getName(): string
    {
        return 'app_book';
    }

    public static function getResourceClass(): string
    {
        return Book::class;
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addFilter(Filter::create('title', 'string'))
            ->addFilter(Filter::create('author', 'entity')
                ->setFormOptions([
                    'class' => Author::class,
                    'multiple' => true,
                ])
            )
            ->addFilter(Filter::create('nationality', 'entity')
                ->setOptions([
                    'fields' => ['author.nationality'],
                ])
                ->setFormOptions([
                    'class' => Nationality::class,
                ])
            )
            ->addFilter(Filter::create('currencyCode', 'string')
                ->setOptions([
                    'fields' => ['price.currencyCode'],
                ])
            )
            ->addFilter(Filter::create('state', 'select')
                ->setFormOptions([
                    'multiple' => true,
                    'choices' => [
                        'initial' => 'initial',
                        'published' => 'published',
                        'unpublished' => 'unpublished',
                    ],
                ])
            )
            ->orderBy('title', 'asc')
            ->addField(
                StringField::create('title')
                    ->setLabel('Title')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('author')
                    ->setLabel('Author')
                    ->setPath('author.name')
                    ->setSortable(true, 'author.name')
            )
            ->addField(
                StringField::create('nationality')
                    ->setLabel('Nationality')
                    ->setPath('author.nationality.name')
                    ->setSortable(true, 'author.nationality.name')
            )
            ->setLimits([10, 5, 15])
        ;
    }
}
