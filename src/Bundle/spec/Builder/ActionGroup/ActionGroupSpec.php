<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Builder\ActionGroup;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Builder\Action\ActionInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ActionGroupInterface;

final class ActionGroupSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedThrough('create', [ActionGroupInterface::MAIN_GROUP]);
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

    function it_allows_to_add_several_action_during_instantioning(ActionInterface $createAction, ActionInterface $updateAction)
    {
        $createAction->getName()->willReturn('create');
        $createAction->toArray()->willReturn([]);

        $updateAction->getName()->willReturn('update');
        $updateAction->toArray()->willReturn([]);

        $this->beConstructedThrough('create', [ActionGroupInterface::MAIN_GROUP, $createAction, $updateAction]);

        $this->toArray()->shouldReturn([
            'create' => [],
            'update' => [],
        ]);
    }
}
