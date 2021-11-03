<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Builder\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Builder\Action\ActionInterface;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;

final class UpdateActionSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(UpdateAction::class);
    }

    function it_builds_update_actions(): void
    {
        $action = $this::create();

        $action->shouldHaveType(ActionInterface::class);

        $action->toArray()->shouldReturn([
            'type' => 'update',
            'label' => 'sylius.ui.edit',
        ]);
    }

    function it_builds_update_actions_with_options(): void
    {
        $action = $this::create(['custom' => true]);

        $action->shouldHaveType(ActionInterface::class);

        $action->toArray()->shouldReturn([
            'type' => 'update',
            'label' => 'sylius.ui.edit',
            'options' => [
                'custom' => true,
            ],
        ]);
    }
}
