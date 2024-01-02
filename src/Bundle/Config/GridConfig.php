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

namespace Sylius\Bundle\GridBundle\Config;

use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;

final class GridConfig implements GridConfigInterface
{
    private array $grids = [];

    public function addGrid(GridBuilderInterface $gridBuilder): GridConfigInterface
    {
        $this->grids[$gridBuilder->getName()] = $gridBuilder;

        return $this;
    }

    public function toArray(): array
    {
        $output = [];

        if (count($this->grids) > 0) {
            $output['grids'] = array_map(function (GridBuilderInterface $gridBuilder): array { return $gridBuilder->toArray(); }, $this->grids);
        }

        return $output;
    }

    public function getExtensionAlias(): string
    {
        return 'sylius_grid';
    }
}
