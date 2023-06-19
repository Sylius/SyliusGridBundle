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

namespace App\Grid\Builder;

use App\Entity\Nationality;
use App\Filter\NationalityFilter as GridNationalityFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface;

final class NationalityFilter
{
    public static function create(string $name, ?bool $multiple = null, ?array $fields = null): FilterInterface
    {
        $filter = Filter::create($name, GridNationalityFilter::getType());

        $filter->setFormOptions(['class' => Nationality::class]);

        if (null !== $fields) {
            $filter->setOptions(['fields' => $fields]);
        }

        if (null !== $multiple) {
            $filter->addFormOption('multiple', $multiple);
        }

        return $filter;
    }
}
