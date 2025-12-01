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
use App\Entity\Book;
use App\Grid\Builder\NationalityFilter;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\CallableField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(resourceClass: Book::class, name: 'app_book')]
final class BookGrid extends AbstractGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->orderBy('title', 'asc')
            ->withFilters(
                Filter::create('title', 'string'),
                Filter::create('author', 'entity')
                    ->setFormOptions([
                        'class' => Author::class,
                        'multiple' => true,
                    ]),
                NationalityFilter::create('nationality', null, ['author.nationality']),
                Filter::create('currencyCode', 'string')
                    ->setOptions([
                        'fields' => ['price.currencyCode'],
                    ]),
                Filter::create('state', 'select')
                    ->setFormOptions([
                        'multiple' => true,
                        'choices' => [
                            'initial' => 'initial',
                            'published' => 'published',
                            'unpublished' => 'unpublished',
                        ],
                    ]),
            )
            ->withFields(
                CallableField::create('title', 'strtoupper')
                    ->setLabel('Title'),
                StringField::create('author')
                    ->setLabel('Author')
                    ->setPath('author.name')
                    ->setSortable(true, 'author.name'),
                StringField::create('nationality')
                    ->setLabel('Nationality')
                    ->setPath('author.nationality.name')
                    ->setSortable(true, 'author.nationality.name'),
                StringField::create('currency')
                    ->setLabel('Currency')
                    ->setPath('price.currencyCode')
                    ->setSortable(true, 'price.currencyCode')
                    ->setOption('vars', ['th_class' => 'text-end']),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    ShowAction::create()
                        ->setTemplate('book/grid/action/show.html.twig'),
                ),
            )
            ->setLimits([10, 5, 15])
        ;
    }
}
