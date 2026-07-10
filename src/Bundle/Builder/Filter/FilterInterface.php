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

namespace Sylius\Bundle\GridBundle\Builder\Filter;

\trigger_deprecation('sylius/grid-bundle', '1.16', '"%s" is deprecated, use "%s" instead.', FilterInterface::class, \Sylius\Component\Grid\Builder\Filter\FilterInterface::class);

interface_exists(\Sylius\Component\Grid\Builder\Filter\FilterInterface::class);

if (false) {
    /**
     * @method mixed getDefaultValue()
     * @method self setDefaultValue(mixed $defaultValue)
     * @method array<string, mixed> getCriteria()
     */
    interface FilterInterface extends \Sylius\Component\Grid\Builder\Filter\FilterInterface
    {
    }
}
