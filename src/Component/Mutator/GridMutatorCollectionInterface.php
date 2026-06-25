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

namespace Sylius\Component\Grid\Mutator;

use Psr\Container\ContainerInterface;

/**
 * Collection of Grid mutators to mutate Grid definitions.
 *
 * @experimental
 */
interface GridMutatorCollectionInterface extends ContainerInterface
{
    /**
     * @return list<GridMutatorInterface>
     */
    public function get(string $id): array;
}
