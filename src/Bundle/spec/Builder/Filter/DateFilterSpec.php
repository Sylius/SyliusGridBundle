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

namespace spec\Sylius\Bundle\GridBundle\Builder\Filter;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Builder\Filter\DateFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface;

final class DateFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DateFilter::class);
    }

    function it_creates_date_filters(): void
    {
        $filter = $this::create('publishedAt');

        $filter->shouldHaveType(FilterInterface::class);
        $filter->toArray()->shouldReturn([
            'type' => 'date',
        ]);
    }
}
