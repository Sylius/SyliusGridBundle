<?php

/*
 * This file is part of the SyliusGridBundle project.
 *
 * (c) Mobizel
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\GridBundle\Config\Builder\Filter;

final class SelectFilter
{
    public static function create(string $name, array $choices, ?string $field): FilterInterface
    {
        $filter = Filter::create($name, 'select');

        $filter->setFormOptions(['choices' => $choices]);

        if (null !== $field) {
            $filter->setOptions(['field' => $field]);
        }

        return $filter;
    }
}
