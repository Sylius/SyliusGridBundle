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

namespace Sylius\Bundle\GridBundle\Grid;

use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;

abstract class AbstractGrid implements GridInterface
{
    public function toArray(): array
    {
        $gridBuilder = $this->createGridBuilder();

        $this->buildGrid($gridBuilder);

        return $gridBuilder->toArray();
    }

    private function createGridBuilder(): GridBuilderInterface
    {
        if ($this instanceof ResourceAwareGridInterface) {
            return GridBuilder::create($this::getName(), $this->getResourceClass());
        }

        return GridBuilder::create($this::getName());
    }
}
