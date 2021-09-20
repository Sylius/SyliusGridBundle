<?php

declare(strict_types=1);

namespace spec\Sylius\Component\Grid\Config\Builder\ActionGroup;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Config\Builder\Action\ActionInterface;
use Sylius\Component\Grid\Config\Builder\ActionGroup\ActionGroup;
use Sylius\Component\Grid\Config\Builder\ActionGroup\ActionGroupInterface;

final class ActionGroupSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedThrough('create', ['main']);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ActionGroup::class);
    }

    function it_implements_an_interface(): void
    {
        $this->shouldImplement(ActionGroupInterface::class);
    }

    function it_adds_actions(ActionInterface $action)
    {
        $action->getName()->willReturn('create');
        $action->toArray()->willReturn([]);

        $this->addAction($action);

        $this->toArray()['create']->shouldReturn([]);
    }
}
