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

namespace Sylius\Component\Grid\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class AsField
{
    public const SERVICE_TAG = 'sylius.grid_field';

    public function __construct(
        public readonly ?string $type = null,
    ) {
    }
}
