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

namespace Sylius\Component\Grid\Event;

use Sylius\Component\Grid\Definition\Grid;
use SyliusLabs\Polyfill\Symfony\EventDispatcher\Event;

final class GridDefinitionConverterEvent extends Event
{
    private Grid $grid;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    public function getGrid(): Grid
    {
        return $this->grid;
    }
}
