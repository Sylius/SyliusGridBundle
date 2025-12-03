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

namespace Sylius\Component\Grid;

use Sylius\Bundle\GridBundle\Grid\InvokableGrid;
use Sylius\Component\Grid\Exception\UndefinedGridException;

/**
 * @internal
 */
final class InvokableGridCollection implements InvokableGridCollectionInterface
{
    /** @var array<string, callable> */
    private array $grids = [];

    public function add(callable $grid): void
    {
        if (!$grid instanceof InvokableGrid) {
            $grid = new InvokableGrid($grid);
        }

        $this->grids[$grid->getName()] = $grid;
    }

    public function get(string $name): callable
    {
        if (!$this->has($name)) {
            throw new UndefinedGridException($name);
        }

        return $this->grids[$name];
    }

    public function has(string $name): bool
    {
        return isset($this->grids[$name]);
    }
}
