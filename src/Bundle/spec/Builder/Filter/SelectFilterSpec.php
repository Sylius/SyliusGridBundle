<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Builder\Filter;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface;
use Sylius\Bundle\GridBundle\Builder\Filter\SelectFilter;

final class SelectFilterSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(SelectFilter::class);
    }

    function it_creates_select_filters(): void
    {
        $filter = $this::create('search', ['sylius.ui.new' => 'new', 'sylius.ui.published' => 'published']);

        $filter->shouldHaveType(FilterInterface::class);
        $filter->toArray()->shouldReturn([
            'type' => 'select',
            'form_options' => [
                'choices' => ['sylius.ui.new' => 'new', 'sylius.ui.published' => 'published'],
            ],
        ]);
    }
}
