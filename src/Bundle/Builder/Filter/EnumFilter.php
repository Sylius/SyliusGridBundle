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

namespace Sylius\Bundle\GridBundle\Builder\Filter;

use Sylius\Component\Grid\Builder\Filter\FilterInterface;

final class EnumFilter
{
    public static function create(string $name, string $enumClass, ?bool $multiple = null, ?string $field = null): FilterInterface
    {
        $filter = Filter::create($name, 'enum');

        $filter->setFormOptions(['class' => $enumClass]);

        if (null !== $field) {
            $filter->setOptions(['field' => $field]);
        }

        if (null !== $multiple) {
            $filter->addFormOption('multiple', $multiple);
        }

        return $filter;
    }
}
