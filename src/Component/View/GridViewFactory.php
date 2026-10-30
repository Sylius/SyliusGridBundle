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

use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

final class GridViewFactory implements VariablesAwareGridViewFactoryInterface
{
    private DataProviderInterface $dataProvider;

    private VariablesResolverInterface $variablesResolver;

    public function __construct(DataProviderInterface $dataProvider, ?VariablesResolverInterface $variablesResolver = null)
    {
        $this->dataProvider = $dataProvider;
        $this->variablesResolver = $variablesResolver ?? new VariablesResolver();
    }

    public function create(Grid $grid, Parameters $parameters): GridViewInterface
    {
        return new GridView($this->dataProvider->getData($grid, $parameters), $grid, $parameters);
    }

    public function createWithVariables(Grid $grid, Parameters $parameters, array $variables): VariablesAwareGridViewInterface
    {
        // The grid definition may be shared (e.g. cached by the grid provider), its driver configuration must not be resolved in place
        $grid = clone $grid;
        $grid->setDriverConfiguration($this->variablesResolver->resolve($grid->getDriverConfiguration(), $variables));

        return new GridView($this->dataProvider->getData($grid, $parameters), $grid, $parameters, $variables);
    }
}
