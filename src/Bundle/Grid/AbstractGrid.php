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
use Sylius\Component\Grid\Exception\LogicException;

abstract class AbstractGrid implements GridInterface
{
    public static function getName(): string
    {
        return self::getAsGridAttribute()?->name ?? static::class;
    }

    public function toArray(): array
    {
        $gridBuilder = $this->createGridBuilder();

        $provider = self::getAsGridAttribute()?->provider ?? null;

        if (null !== $provider) {
            $gridBuilder->setProvider($provider);
        }

        $buildMethod = self::getAsGridAttribute()?->buildMethod;

        if (null === $buildMethod && method_exists($this, '__invoke')) {
            $buildMethod = '__invoke';
        }

        $buildMethod ??= 'buildGrid';

        if (!method_exists($this, $buildMethod)) {
            throw new LogicException(sprintf('The configured build method "%s" does not exist.', $buildMethod));
        }

        $this->$buildMethod($gridBuilder);

        return $gridBuilder->toArray();
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
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
