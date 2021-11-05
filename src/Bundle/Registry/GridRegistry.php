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

namespace Sylius\Bundle\GridBundle\Registry;

use Sylius\Bundle\GridBundle\Grid\GridInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class GridRegistry implements GridRegistryInterface
{
    private ServiceLocator $gridLocator;

    public function __construct(ServiceLocator $gridLocator)
    {
        $this->gridLocator = $gridLocator;
    }

    public function getGrid(string $code): ?GridInterface
    {
        return $this->gridLocator->has($code) ? $this->gridLocator->get($code) : null;
    }
}
