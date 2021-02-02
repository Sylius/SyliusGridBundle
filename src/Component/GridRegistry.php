<?php

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
