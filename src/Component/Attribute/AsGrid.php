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
final class AsGrid
{
    public function __construct(
        public readonly ?string $resourceClass = null,
        public readonly ?string $name = null,
        public readonly ?string $buildMethod = null,
        public readonly ?string $provider = null,
    ) {
    }
}
