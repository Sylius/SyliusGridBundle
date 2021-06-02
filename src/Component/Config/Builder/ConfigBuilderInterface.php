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

namespace Sylius\Component\Grid\Config\Builder;

use Symfony\Component\Config\Builder\ConfigBuilderInterface as SymfonyConfigBuilderInterface;

/**
 * @psalm-suppress UnrecognizedStatement
 */
if (\class_exists(SymfonyConfigBuilderInterface::class)) {
    interface ConfigBuilderInterface extends SymfonyConfigBuilderInterface
    {
    }
} else {
    interface ConfigBuilderInterface
    {
        /**
         * Gets all configuration represented as an array.
         */
        public function toArray(): array;

        /**
         * Gets the alias for the extension which config we are building.
         */
        public function getExtensionAlias(): string;
    }
}
