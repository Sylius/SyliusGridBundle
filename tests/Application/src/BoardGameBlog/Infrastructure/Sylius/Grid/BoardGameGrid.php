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

namespace App\BoardGameBlog\Infrastructure\Sylius\Grid;

use App\BoardGameBlog\Infrastructure\Sylius\Grid\DataProvider\BoardGameGridProvider;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Component\Grid\Attribute\AsGrid;
use Sylius\Component\Grid\Builder\GridBuilderInterface;

#[AsGrid(name: 'app_board_game', provider: BoardGameGridProvider::class)]
final class BoardGameGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addField(
                StringField::create('name')
                    ->setLabel('Name'),
            )
            ->addField(
                StringField::create('shortDescription')
                    ->setLabel('Short Description'),
            )
        ;
    }
}
