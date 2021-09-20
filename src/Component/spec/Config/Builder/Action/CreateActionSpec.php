<?php

declare(strict_types=1);

namespace spec\Sylius\Component\Grid\Config\Builder\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Config\Builder\Action\ActionInterface;
use Sylius\Component\Grid\Config\Builder\Action\CreateAction;

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
        $action->getName()->shouldReturn('create');
    }
}
