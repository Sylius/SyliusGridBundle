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

namespace Sylius\Component\Grid\Configuration;

final class GridConfigurationRemovalsHandler implements GridConfigurationRemovalsHandlerInterface
{
    public function handle(array $gridConfiguration): array
    {
        if (false === isset($gridConfiguration['removals'])) {
            return $gridConfiguration;
        }

        /** @var array<string, mixed> $removals */
        $removals = $gridConfiguration['removals'];
        $this->handleRemovals($gridConfiguration, $removals);
        unset($gridConfiguration['removals']);

        return $gridConfiguration;
    }

    /**
     * @param array<string, mixed> $gridConfiguration
     * @param array<string, mixed> $removals
     */
    private function handleRemovals(array &$gridConfiguration, array $removals): void
    {
        foreach ($removals as $type => $name) {
            if (!is_array($name)) {
                unset($gridConfiguration[$name]);

                continue;
            }

            if (isset($gridConfiguration[$type])) {
                /** @var array<string, mixed> $subConfiguration */
                $subConfiguration = $gridConfiguration[$type];
                /** @var array<string, mixed> $name */
                $this->handleRemovals($subConfiguration, $name);
                $gridConfiguration[$type] = $subConfiguration;
            }
        }
    }
}
