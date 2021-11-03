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

final class SelectFilter
{
    public static function create(string $name, array $choices, ?bool $multiple = null, ?string $field = null): FilterInterface
    {
        $filter = Filter::create($name, 'select');

        $filter->setFormOptions(['choices' => $choices]);

        if (null !== $field) {
            $filter->setOptions(['field' => $field]);
        }

        if (null !== $multiple) {
            $filter->addFormOption('multiple', $multiple);
        }

        return $filter;
    }
}
