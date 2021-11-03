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

namespace Sylius\Bundle\GridBundle\Builder\Filter;

final class ExistsFilter
{
    public static function create(string $name, ?string $field = null): FilterInterface
    {
        $filter = Filter::create($name, 'exists');

        if (null !== $field) {
            $filter->setOptions(['field' => $field]);
        }

        return $filter;
    }
}
