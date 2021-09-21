<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Config\Builder\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Config\Builder\Action\ActionInterface;
use Sylius\Bundle\GridBundle\Config\Builder\Action\DeleteAction;

final class DeleteActionSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(DeleteAction::class);
    }

    function it_builds_create_actions(): void
    {
        $action = $this::create();

        $action->shouldHaveType(ActionInterface::class);
        $action->toArray()->shouldReturn([
            'type' => 'delete',
            'label' => 'sylius.ui.delete',
        ]);
    }

    function it_builds_create_actions_with_options(): void
    {
        $action = $this::create(['custom' => true]);

        $action->shouldHaveType(ActionInterface::class);
        $action->toArray()->shouldReturn([
            'type' => 'delete',
            'label' => 'sylius.ui.delete',
            'options' => [
                'custom' => true,
            ],
        ]);
    }
}
