<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Builder\Filter;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Builder\Filter\ExistsFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface;

final class ExistsFilterSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(ExistsFilter::class);
    }

    function it_creates_exists_filters(): void
    {
        $filter = $this::create('publishedAt');

        $filter->shouldHaveType(FilterInterface::class);
        $filter->toArray()->shouldReturn([
            'type' => 'exists',
        ]);
    }
}
