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

use Sylius\Component\Grid\Filter\StringFilter as GridStringFilter;

final class StringFilter
{
    public const NAME = GridStringFilter::NAME;

    public const TYPE_EQUAL = GridStringFilter::TYPE_EQUAL;

    public const TYPE_NOT_EQUAL = GridStringFilter::TYPE_NOT_EQUAL;

    public const TYPE_EMPTY = GridStringFilter::TYPE_EMPTY;

    public const TYPE_NOT_EMPTY = GridStringFilter::TYPE_NOT_EMPTY;

    public const TYPE_CONTAINS = GridStringFilter::TYPE_CONTAINS;

    public const TYPE_NOT_CONTAINS = GridStringFilter::TYPE_NOT_CONTAINS;

    public const TYPE_STARTS_WITH = GridStringFilter::TYPE_STARTS_WITH;

    public const TYPE_ENDS_WITH = GridStringFilter::TYPE_ENDS_WITH;

    public const TYPE_IN = GridStringFilter::TYPE_IN;

    public const TYPE_NOT_IN = GridStringFilter::TYPE_NOT_IN;

    /**
     * @param mixed $type
     */
    public static function create(string $name, ?array $fields = null, $type = null): FilterInterface
    {
        $filter = Filter::create($name, 'string');

        if (null !== $fields) {
            $filter->setOptions(['fields' => $fields]);
        }

        if (null !== $type) {
            $filter->setFormOptions(['type' => $type]);
        }

        return $filter;
    }
}
