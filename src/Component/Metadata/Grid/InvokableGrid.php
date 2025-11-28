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

namespace Sylius\Component\Grid\Metadata\Grid;

use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;

final class InvokableGrid extends Grid
{
    private \ReflectionFunction $invokable;

    public function __construct(
        string $name,
        callable $grid,
        private ?string $resourceClass = null,
    ) {
        parent::__construct(name: $name);

        $this->invokable = new \ReflectionFunction($this->getClosure($grid));
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $this->invokable->invoke($gridBuilder);
    }

    public function getResourceClass(): ?string
    {
        return $this->resourceClass;
    }

    private function getClosure(callable $grid): \Closure
    {
        if (!$grid instanceof \Closure) {
            return $grid(...);
        }

        if (null !== (new \ReflectionFunction($grid))->getClosureThis()) {
            return $grid;
        }

        return $grid;
    }
}
