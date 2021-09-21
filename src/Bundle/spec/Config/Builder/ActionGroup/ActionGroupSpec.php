<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Config\Builder\ActionGroup;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Config\Builder\Action\ActionInterface;
use Sylius\Bundle\GridBundle\Config\Builder\ActionGroup\ActionGroup;
use Sylius\Bundle\GridBundle\Config\Builder\ActionGroup\ActionGroupInterface;

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
