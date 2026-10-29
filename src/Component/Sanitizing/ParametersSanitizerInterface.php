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

namespace Sylius\Component\Grid\Sanitizing;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

interface ParametersSanitizerInterface
{
    /**
     * Sanitizes the page, limit and sorting parameters (e.g. coming from the URL) against the grid definition,
     * so invalid values are ignored instead of breaking the grid. Other parameters are kept as is.
     */
    public function sanitize(Grid $grid, Parameters $parameters): Parameters;
}
