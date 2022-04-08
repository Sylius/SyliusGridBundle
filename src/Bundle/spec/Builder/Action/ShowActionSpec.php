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

namespace spec\Sylius\Bundle\GridBundle\Builder\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Builder\Action\ActionInterface;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;

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
