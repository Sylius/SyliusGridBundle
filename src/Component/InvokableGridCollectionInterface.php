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

namespace Sylius\Component\Grid;

/** @experimental */
interface InvokableGridCollectionInterface
{
    public function add(callable $grid): void;

    public function get(string $name): callable;

    public function has(string $name): bool;
}
