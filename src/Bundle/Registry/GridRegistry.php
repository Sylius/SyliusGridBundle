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

namespace Sylius\Bundle\GridBundle\Registry;

use Sylius\Bundle\GridBundle\Grid\GridInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class GridRegistry implements GridRegistryInterface
{
    public function __construct(
        /** @var ServiceLocator<GridInterface> */
        private ServiceLocator $gridLocator,
    ) {
    }

    public function getGrid(string $code): ?GridInterface
    {
        return $this->gridLocator->has($code) ? $this->gridLocator->get($code) : null;
    }
}
