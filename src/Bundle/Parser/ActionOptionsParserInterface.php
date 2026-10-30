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

namespace Sylius\Bundle\GridBundle\Parser;

use Sylius\Component\Grid\View\GridViewInterface;

/**
 * @experimental
 */
interface ActionOptionsParserInterface
{
    /**
     * Resolves the "$name" and "@=expression" options with the grid view variables (see VariablesAwareGridViewInterface
     * and VariablesResolverInterface), and the "resource.field" options from the data of the action (e.g. the row resource),
     * also available as "resource" in the expressions.
     *
     * @param array<array-key, mixed> $options
     *
     * @return array<array-key, mixed>
     */
    public function parseActionOptions(array $options, GridViewInterface $gridView, mixed $data = null): array;
}
