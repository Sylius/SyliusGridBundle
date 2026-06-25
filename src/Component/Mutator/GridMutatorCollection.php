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

namespace Sylius\Component\Grid\Mutator;

/**
 * @internal
 */
final class GridMutatorCollection implements GridMutatorCollectionInterface
{
    /** @var array<string, list<GridMutatorInterface>> */
    private array $mutators = [];

    public function add(string $grid, GridMutatorInterface $mutator): void
    {
        $this->mutators[$grid][] = $mutator;
    }

    public function get(string $id): array
    {
        return $this->mutators[$id] ?? [];
    }

    public function has(string $id): bool
    {
        return isset($this->mutators[$id]);
    }
}
