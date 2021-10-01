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

use Sylius\Component\Grid\Filter\MoneyFilter as GridMoneyFilter;

final class MoneyFilter
{
    public static function create(string $name, string $currencyCode, ?int $scale = null): FilterInterface
    {
        $filter = Filter::create($name, 'money');

        $scale = $scale ?? GridMoneyFilter::DEFAULT_SCALE;

        $filter->setFormOptions(['scale' => $scale]);
        $filter->setOptions([
            'currency_field' => $currencyCode,
            'scale' => $scale,
        ]);

        return $filter;
    }
}
