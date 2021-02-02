<?php

declare(strict_types=1);

namespace Sylius\Component\Grid;

use Sylius\Component\Grid\Builder\GridBuilder;
use Sylius\Component\Grid\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Definition\Grid;

abstract class AbstractGrid implements GridInterface
{
    public function getDefinition(): Grid
    {
        $gridBuilder = GridBuilder::create(static::getName(), static::getResourceClass());

        $this->buildGrid($gridBuilder);

        return $gridBuilder->getDefinition();
    }

    protected function buildGrid(GridBuilderInterface $gridBuilder): void
    {
    }
}
