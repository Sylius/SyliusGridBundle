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

namespace Sylius\Component\Grid\Maker\Helper;

class GridHelper implements GridHelperInterface
{
    /** @var array */
    private $syliusGridFilters;

    public function __construct(array $syliusGridFilters)
    {
        $this->syliusGridFilters = $syliusGridFilters;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterIds(): array
    {
        return array_keys($this->syliusGridFilters);
    }
}
