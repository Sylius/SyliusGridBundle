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

namespace Sylius\Component\Grid\Tests\Dummy;

use Sylius\Component\Grid\AbstractGrid;
use Sylius\Component\Grid\Config\Builder\GridBuilderInterface;

final class BarGrid extends AbstractGrid
{
    public static function getName(): string
    {
        return 'app_bar';
    }

    public static function getResourceClass(): string
    {
        return Bar::class;
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
    }
}
