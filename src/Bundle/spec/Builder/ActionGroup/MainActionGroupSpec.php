<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Builder\ActionGroup;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Builder\Action\ActionInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ActionGroupInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;

final class MainActionGroupSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(MainActionGroup::class);
    }

    function it_builds_an_action_group(): void
    {
        $this::create()->shouldHaveType(ActionGroupInterface::class);
    }

    function it_builds_an_action_group_with_actions(
        ActionInterface $firstAction,
        ActionInterface $secondAction
    ): void {
        $firstAction->getName()->willReturn('first');
        $firstAction->toArray()->willReturn([]);
        $secondAction->getName()->willReturn('second');
        $secondAction->toArray()->willReturn([]);

        $actionGroup = $this::create($firstAction, $secondAction);

        $actionGroup->toArray()->shouldHaveKey('first');
        $actionGroup->toArray()['first']->shouldReturn([]);
        $actionGroup->toArray()->shouldHaveKey('second');
        $actionGroup->toArray()['second']->shouldReturn([]);
    }
}
