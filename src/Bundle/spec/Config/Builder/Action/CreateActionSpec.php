<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Config\Builder\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Config\Builder\Action\ActionInterface;
use Sylius\Bundle\GridBundle\Config\Builder\Action\CreateAction;

final class CreateActionSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(CreateAction::class);
    }

    function it_builds_create_actions(): void
    {
        $action = $this::create();

        $action->shouldHaveType(ActionInterface::class);
        $action->toArray()->shouldReturn([
            'type' => 'create',
            'label' => 'sylius.ui.create',
        ]);
    }

    function it_builds_create_action_with_options(): void
    {
        $action = $this::create(['custom' => true]);

        $action->shouldHaveType(ActionInterface::class);
        $action->toArray()->shouldReturn([
            'type' => 'create',
            'label' => 'sylius.ui.create',
            'options' => [
                'custom' => true,
            ],
        ]);
    }
}
