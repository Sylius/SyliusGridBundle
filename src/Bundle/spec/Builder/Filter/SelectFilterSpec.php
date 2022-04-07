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

    function it_creates_select_filters_with_multiple_option(): void
    {
        $filter = $this::create('search', ['sylius.ui.new' => 'new', 'sylius.ui.published' => 'published'], true);

        $filter->shouldHaveType(FilterInterface::class);
        $filter->toArray()->shouldReturn([
            'type' => 'select',
            'form_options' => [
                'choices' => ['sylius.ui.new' => 'new', 'sylius.ui.published' => 'published'],
                'multiple' => true,
            ],
        ]);
    }
}
