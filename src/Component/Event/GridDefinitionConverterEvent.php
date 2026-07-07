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

use Sylius\Component\Grid\Attribute\AsGridMutator;
use Sylius\Component\Grid\Definition\Grid;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @deprecated use the Grid mutators instead
 */
final class GridDefinitionConverterEvent extends Event
{
    private Grid $grid;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    public function getGrid(): Grid
    {
        \trigger_deprecation('sylius/grid-bundle', '1.16', 'Grid events are deprecated, use the grid mutators with the "%s" attribute instead.', AsGridMutator::class);

        return $this->grid;
    }
}
