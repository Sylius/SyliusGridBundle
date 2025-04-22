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

use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

abstract class AbstractGrid implements GridInterface
{
    public static function getName(): string
    {
        return self::getAsGridAttribute()?->name ?? static::class;
    }

    public function toArray(): array
    {
        $gridBuilder = $this->createGridBuilder();

        $this->buildGrid($gridBuilder);

        return $gridBuilder->toArray();
    }

    private function createGridBuilder(): GridBuilderInterface
    {
        $resourceClass = self::getAsGridAttribute()?->resourceClass ?? null;

        if (null === $resourceClass && $this instanceof ResourceAwareGridInterface) {
            $resourceClass = $this->getResourceClass();
        }

        return GridBuilder::create($this::getName(), $resourceClass);
    }

    private static function getAsGridAttribute(): ?AsGrid
    {
        $reflection = (new \ReflectionClass(static::class))->getAttributes(AsGrid::class)[0] ?? null;

        return $reflection?->newInstance();
    }
}
