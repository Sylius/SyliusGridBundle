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

namespace Sylius\Component\Grid\Pagination;

interface MaxPerPageResolverInterface
{
    /**
     * Resolves the number of items per page:
     * - without a (positive) requested limit, the first grid limit is used, or the fallback limit when the grid has none,
     * - a requested limit greater than the greatest grid limit is clamped to it.
     *
     * @param int[] $gridLimits
     */
    public function resolve(?int $requestedLimit, array $gridLimits = [], ?int $fallbackLimit = null): int;
}
