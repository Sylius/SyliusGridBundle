<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Config\Builder\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Config\Builder\Action\ActionInterface;
use Sylius\Bundle\GridBundle\Config\Builder\Action\ShowAction;

final class ShowActionSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(ShowAction::class);
    }

    function it_builds_show_actions(): void
    {
        $action = $this::create();

        $action->shouldHaveType(ActionInterface::class);

        $action->toArray()->shouldReturn([
            'type' => 'show',
            'label' => 'sylius.ui.show',
        ]);
    }

    function it_builds_show_actions_with_options(): void
    {
        $action = $this::create(['custom' => true]);

        $action->shouldHaveType(ActionInterface::class);

        $action->toArray()->shouldReturn([
            'type' => 'show',
            'label' => 'sylius.ui.show',
            'options' => [
                'custom' => true,
            ],
        ]);
    }
}
