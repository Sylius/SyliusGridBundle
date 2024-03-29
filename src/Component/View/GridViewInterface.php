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

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

interface GridViewInterface
{
    /**
     * @return mixed
     */
    public function getData();

    public function getDefinition(): Grid;

    public function getParameters(): Parameters;

    public function getSortingOrder(string $fieldName): ?string;

    public function isSortedBy(string $fieldName): bool;
}
