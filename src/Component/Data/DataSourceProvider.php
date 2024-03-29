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

namespace Sylius\Component\Grid\Data;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class DataSourceProvider implements DataSourceProviderInterface
{
    private ServiceRegistryInterface $driversRegistry;

    public function __construct(ServiceRegistryInterface $driversRegistry)
    {
        $this->driversRegistry = $driversRegistry;
    }

    public function getDataSource(Grid $grid, Parameters $parameters): DataSourceInterface
    {
        $driverName = $grid->getDriver();

        if (!$this->driversRegistry->has($driverName)) {
            throw new UnsupportedDriverException($driverName);
        }

        /** @var DriverInterface $driver */
        $driver = $this->driversRegistry->get($driverName);

        return $driver->getDataSource($grid->getDriverConfiguration(), $parameters);
    }
}
