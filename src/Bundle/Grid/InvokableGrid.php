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
use Sylius\Component\Grid\Attribute\AsGrid;
use Sylius\Component\Grid\Exception\InvalidArgumentException;
use Sylius\Component\Grid\Exception\LogicException;

/** @internal */
final class InvokableGrid
{
    private string $name;

    /** @var callable */
    private $grid;

    private ?string $resourceClass;

    public function __construct(
        callable $grid,
    ) {
        if (!\is_object($grid) || $grid instanceof \Closure) {
            throw new InvalidArgumentException('The grid must be an invokable object.');
        }

        $attributes = (new \ReflectionClass($grid))->getAttributes(AsGrid::class);

        if (0 === \count($attributes)) {
            throw new LogicException(sprintf('The grid must use the "%s" attribute.', AsGrid::class));
        }

        /** @var AsGrid $attribute */
        $attribute = $attributes[0]->newInstance();

        $this->name = $attribute->name ?? $grid::class;
        $this->grid = $grid;
        $this->resourceClass = $attribute->resourceClass;
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder->setDriverOption('class', $this->resourceClass);

        ($this->grid)($gridBuilder);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
