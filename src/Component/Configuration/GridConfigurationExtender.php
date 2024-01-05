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

final class GridConfigurationExtender implements GridConfigurationExtenderInterface
{
    public function extends(array $gridConfiguration, array $parentGridConfiguration): array
    {
        unset($parentGridConfiguration['sorting']); // Do not inherit sorting.

        $configuration = array_replace_recursive($parentGridConfiguration, $gridConfiguration) ?: [];

        unset($configuration['extends']);

        return $configuration;
    }
}
