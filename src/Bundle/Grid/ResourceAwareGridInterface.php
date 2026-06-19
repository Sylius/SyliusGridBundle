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

namespace Sylius\Bundle\GridBundle\Grid;

/**
 * @deprecated since Sylius Grid 1.16, will be removed in 2.0, use the Sylius\Component\Grid\Attribute\AsGrid attribute only.
 */
interface ResourceAwareGridInterface extends GridInterface
{
    public function getResourceClass(): string;
}
