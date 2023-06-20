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

namespace Sylius\Component\Grid\Tests\Dummy;

use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;

final class NoResourceGrid extends AbstractGrid
{
    public static function getName(): string
    {
        return 'app_no_resource';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
    }
}
