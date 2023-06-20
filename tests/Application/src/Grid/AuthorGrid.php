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

use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class AuthorGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    private string $authorClass;

    public function __construct(string $authorClass)
    {
        $this->authorClass = $authorClass;
    }

    public static function getName(): string
    {
        return 'app_author';
    }

    public function getResourceClass(): string
    {
        return $this->authorClass;
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addFilter(Filter::create('name', 'string'))
            ->orderBy('name', 'asc')
            ->addField(
                StringField::create('id')
                ->setSortable(true)
                ->setEnabled(false),
            )
            ->addField(
                StringField::create('name')
                ->setLabel('Name')
                ->setSortable(true),
            )
            ->addField(
                StringField::create('nationality')
                ->setLabel('Name')
                ->setPath('nationality.name')
                ->setSortable(true, 'nationality.name'),
            )
            ->setLimits([10, 5, 15])
        ;
    }
}
