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

namespace Sylius\Bundle\GridBundle\Storage;

interface FilterStorageInterface
{
    /**
     * @param array<string, mixed> $filters
     */
    public function set(array $filters): void;

    /**
     * @return array<string, mixed>
     */
    public function all(): array;

    public function hasFilters(): bool;
}
