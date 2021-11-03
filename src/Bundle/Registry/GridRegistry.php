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

namespace Sylius\Bundle\GridBundle\Registry;

use Sylius\Bundle\GridBundle\Grid\GridInterface;

final class GridRegistry
{
    private array $grids = [];

    public function __construct(\Traversable $grids)
    {
        foreach ($grids as $grid) {
            $this->addGrid($grid);
        }
    }

    public function addGrid(GridInterface $grid): void
    {
        $this->grids[$grid::getName()] = $grid;
    }

    public function getGrid(string $code): ?GridInterface
    {
        return $this->grids[$code] ?? null;
    }
}
