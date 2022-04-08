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

namespace spec\Sylius\Bundle\GridBundle\Builder\Filter;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface;
use Sylius\Bundle\GridBundle\Builder\Filter\MoneyFilter;

final class MoneyFilterSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(MoneyFilter::class);
    }

    function it_creates_money_filters(): void
    {
        $filter = $this::create('search', 'EUR');

        $filter->shouldHaveType(FilterInterface::class);
        $filter->toArray()->shouldReturn([
            'type' => 'money',
            'options' => [
                'currency_field' => 'EUR',
                'scale' => 2,
            ],
            'form_options' => [
                'scale' => 2,
            ],
        ]);
    }

    function it_creates_money_filters_with_custom_scale(): void
    {
        $filter = $this::create('search', 'EUR', 0);

        $filter->shouldHaveType(FilterInterface::class);
        $filter->toArray()->shouldReturn([
            'type' => 'money',
            'options' => [
                'currency_field' => 'EUR',
                'scale' => 0,
            ],
            'form_options' => [
                'scale' => 0,
            ],
        ]);
    }
}
