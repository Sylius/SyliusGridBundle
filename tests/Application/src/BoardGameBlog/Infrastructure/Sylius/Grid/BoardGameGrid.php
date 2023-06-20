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

namespace App\BoardGameBlog\Infrastructure\Sylius\Grid;

use App\BoardGameBlog\Infrastructure\Sylius\Grid\DataProvider\BoardGameGridProvider;
use App\BoardGameBlog\Infrastructure\Sylius\Resource\BoardGameResource;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class BoardGameGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
        return 'app_board_game';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setProvider(BoardGameGridProvider::class)
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

    public function getResourceClass(): string
    {
        return BoardGameResource::class;
    }
}
