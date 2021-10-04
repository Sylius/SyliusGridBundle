<?php

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
}
