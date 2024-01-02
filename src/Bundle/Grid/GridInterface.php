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

namespace Sylius\Bundle\GridBundle\Grid;

use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;

interface GridInterface
{
    public static function getName(): string;

    public function toArray(): array;

    public function buildGrid(GridBuilderInterface $gridBuilder): void;
}
