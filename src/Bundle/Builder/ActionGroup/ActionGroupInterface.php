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

namespace Sylius\Bundle\GridBundle\Builder\ActionGroup;

\trigger_deprecation('sylius/grid-bundle', '1.16', '"%s" is deprecated, use "%s" instead.', ActionGroupInterface::class, \Sylius\Component\Grid\Builder\ActionGroup\ActionGroupInterface::class);

interface_exists(\Sylius\Component\Grid\Builder\ActionGroup\ActionGroupInterface::class);

if (false) {
    interface ActionGroupInterface extends \Sylius\Component\Grid\Builder\ActionGroup\ActionGroupInterface
    {
    }
}
