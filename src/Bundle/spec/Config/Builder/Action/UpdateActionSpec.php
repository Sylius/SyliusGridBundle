<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Config\Builder\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Config\Builder\Action\ActionInterface;
use Sylius\Bundle\GridBundle\Config\Builder\Action\UpdateAction;

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
        $action->getName()->shouldReturn('update');
    }
}
