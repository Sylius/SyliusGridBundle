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

namespace Sylius\Component\Grid;

final class GridRegistry
{
    private array $grids;

    public function __construct(\Traversable $grids)
    {
        $this->grids = iterator_to_array($grids);
    }

    public function getGrid(string $code): ?GridInterface
    {
        return $this->grids[$code] ?? null;
    }
}
