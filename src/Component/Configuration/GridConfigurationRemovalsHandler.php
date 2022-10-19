<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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

        $this->handleRemovals($gridConfiguration, $gridConfiguration['removals']);
        unset($gridConfiguration['removals']);

        return $gridConfiguration;
    }

    private function handleRemovals(array &$gridConfiguration, array $removals): void
    {
        foreach ($removals as $type => $name) {
            if (!is_array($name)) {
                unset($gridConfiguration[$name]);

                continue;
            }

            if (isset($gridConfiguration[$type])) {
                $this->handleRemovals($gridConfiguration[$type], $name);
            }
        }
    }
}
