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

namespace Sylius\Bundle\GridBundle\Builder;

\trigger_deprecation('sylius/grid-bundle', '1.16', '"%s" is deprecated, use "%s" instead.', GridBuilderInterface::class, \Sylius\Component\Grid\Builder\GridBuilderInterface::class);

interface_exists(\Sylius\Component\Grid\Builder\GridBuilderInterface::class);

if (false) {
    interface GridBuilderInterface extends \Sylius\Component\Grid\Builder\GridBuilderInterface
    {
    }
}
