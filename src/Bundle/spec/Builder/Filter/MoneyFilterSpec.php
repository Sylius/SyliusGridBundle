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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Builder\Filter;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface;
use Sylius\Bundle\GridBundle\Builder\Filter\MoneyFilter;

final class MoneyFilterTest extends TestCase
{
    public function testItCreatesMoneyFilters(): void
    {
        $filter = MoneyFilter::create('search', 'EUR');

        $this->assertInstanceOf(FilterInterface::class, $filter);
        $this->assertEquals([
            'type' => 'money',
            'options' => [
                'currency_field' => 'EUR',
                'scale' => 2,
            ],
            'form_options' => [
                'scale' => 2,
            ],
        ], $filter->toArray());
    }

    public function testCreatesMoneyFiltersWithCustomScale(): void
    {
        $filter = MoneyFilter::create('search', 'EUR', 0);

        $this->assertInstanceOf(FilterInterface::class, $filter);
        $this->assertEquals([
            'type' => 'money',
            'options' => [
                'currency_field' => 'EUR',
                'scale' => 0,
            ],
            'form_options' => [
                'scale' => 0,
            ],
        ], $filter->toArray());
    }
}
