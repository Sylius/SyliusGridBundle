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

namespace Sylius\Component\Grid\View;

/**
 * A grid view that knows the values of the "$name" placeholders of its configuration
 * (e.g. the route parameters of the page displaying it), so that they can be resolved without any request.
 *
 * @experimental
 */
interface VariablesAwareGridViewInterface extends GridViewInterface
{
    /** @return array<string, mixed> */
    public function getVariables(): array;
}
