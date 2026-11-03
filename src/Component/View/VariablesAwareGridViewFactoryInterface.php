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

namespace Sylius\Component\Grid\View;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

/**
 * @experimental
 */
interface VariablesAwareGridViewFactoryInterface extends GridViewFactoryInterface
{
    /**
     * The "$name" values of the grid driver configuration (e.g. a "$productId" repository argument)
     * are resolved from the given variables.
     *
     * @param array<string, mixed> $variables
     */
    public function createWithVariables(Grid $grid, Parameters $parameters, array $variables): VariablesAwareGridViewInterface;
}
