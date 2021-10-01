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

namespace Sylius\Bundle\GridBundle\Builder\Filter;

final class EntityFilter
{
    public static function create(string $name, string $resourceClass, ?array $fields = null): FilterInterface
    {
        $filter = Filter::create($name, 'entity');

        $filter->setFormOptions(['class' => $resourceClass]);

        if (null !== $fields) {
            $filter->setOptions(['fields' => $fields]);
        }

        return $filter;
    }
}
