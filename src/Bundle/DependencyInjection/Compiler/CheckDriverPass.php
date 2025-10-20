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

namespace Sylius\Bundle\GridBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CheckDriverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('sylius.grids_definitions')) {
            return;
        }

        $availableDrivers = $this->getAvailableDrivers($container);
        /** @var array<string, array{driver?: array{name?: string|false|null}}> $gridsDefinitions */
        $gridsDefinitions = $container->getParameter('sylius.grids_definitions');

        $this->validateGridDrivers($gridsDefinitions, $availableDrivers);
    }

    /**
     * @return string[]
     */
    private function getAvailableDrivers(ContainerBuilder $container): array
    {
        /** @var array<string> $drivers */
        $drivers = [];

        foreach ($container->findTaggedServiceIds('sylius.grid_driver') as $attributes) {
            foreach ($attributes as $attribute) {
                if (!is_array($attribute)) {
                    continue;
                }

                $alias = $attribute['alias'] ?? null;
                if (!is_string($alias)) {
                    continue;
                }

                $drivers[] = $alias;
            }
        }

        return $drivers;
    }

    /**
     * @param array<string, array{driver?: array{name?: string|false|null}}> $gridsDefinitions
     * @param array<string> $availableDrivers
     */
    private function validateGridDrivers(array $gridsDefinitions, array $availableDrivers): void
    {
        foreach ($gridsDefinitions as $gridName => $gridDefinition) {
            $driverName = null;
            $driver = $gridDefinition['driver'] ?? [];
            $driverName = $driver['name'] ?? null;

            if (!is_string($driverName)) {
                continue;
            }

            if (in_array($driverName, $availableDrivers, true)) {
                continue;
            }

            throw new \InvalidArgumentException(sprintf(
                'Grid "%s" uses driver "%s" which is not registered. Available drivers are: %s',
                $gridName,
                $driverName,
                empty($availableDrivers) ? 'none' : implode(', ', $availableDrivers),
            ));
        }
    }
}
