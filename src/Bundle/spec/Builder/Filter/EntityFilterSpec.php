<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Builder\Filter;

use App\Entity\Author;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Builder\Filter\EntityFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface;

final class EntityFilterSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(EntityFilter::class);
    }

    function it_creates_entity_filters(): void
    {
        $filter = $this::create('author', Author::class);

        $filter->shouldHaveType(FilterInterface::class);
        $filter->toArray()->shouldReturn([
            'type' => 'entity',
            'form_options' => [
                'class' => Author::class,
            ],
        ]);
    }

    function it_creates_entity_filters_with_multiple_option(): void
    {
        $filter = $this::create('author', Author::class, true);

        $filter->shouldHaveType(FilterInterface::class);
        $filter->toArray()->shouldReturn([
            'type' => 'entity',
            'form_options' => [
                'class' => Author::class,
                'multiple' => true,
            ],
        ]);
    }
}
