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
use Sylius\Component\Grid\AbstractGrid;
use Sylius\Component\Grid\Config\Builder\Field;
use Sylius\Component\Grid\Config\Builder\Filter;
use Sylius\Component\Grid\Config\Builder\GridBuilderInterface;

class AuthorGrid extends AbstractGrid
{
    public static function getName(): string
    {
        return 'app_author';
    }

    public static function getResourceClass(): string
    {
        return Author::class;
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addFilter(Filter::create('name', 'string'))
            ->orderBy('name', 'asc')
            ->addField(Field::create('name', 'string')
                ->setLabel('Name')
                ->setSortable(true)
            )
            ->addField(Field::create('nationality', 'string')
                ->setLabel('Name')
                ->setPath('nationality.name')
                ->setSortable(true, 'nationality.name')
            )
            ->setLimits([10, 5, 15])
        ;
    }
}
