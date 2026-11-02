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

final class MaxPerPageResolver implements MaxPerPageResolverInterface
{
    public const DEFAULT_MAX_PER_PAGE = 10;

    public function __construct(private int $defaultMaxPerPage = self::DEFAULT_MAX_PER_PAGE)
    {
    }

    public function resolve(?int $requestedLimit, array $gridLimits = [], ?int $fallbackLimit = null): int
    {
        $gridLimits = array_values(array_filter($gridLimits, static fn (int $limit): bool => $limit > 0));

        if (null === $requestedLimit || $requestedLimit < 1) {
            return $gridLimits[0] ?? $fallbackLimit ?? $this->defaultMaxPerPage;
        }

        if ([] !== $gridLimits) {
            // Cannot retrieve more items than configured in the grid
            return min($requestedLimit, max($gridLimits));
        }

        return $requestedLimit;
    }
}
