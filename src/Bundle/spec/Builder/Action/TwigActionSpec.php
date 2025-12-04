<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Builder\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Builder\Action\ActionInterface;
use Sylius\Bundle\GridBundle\Builder\Action\TwigAction;

final class TwigActionSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(TwigAction::class);
    }

    function it_builds_create_actions(): void
    {
        $action = $this::create(name: 'dummy', template: 'path/to/dummy/template');

        $action->shouldHaveType(ActionInterface::class);
        $action->toArray()->shouldReturn([
            'type' => 'dummy',
            'options' => [
                'template' => 'path/to/dummy/template',
            ],
        ]);
    }

    function it_builds_create_action_with_options(): void
    {
        $action = $this::create(name: 'dummy', template: 'path/to/dummy/template', options: ['custom' => true]);

        $action->shouldHaveType(ActionInterface::class);
        $action->toArray()->shouldReturn([
            'type' => 'dummy',
            'options' => [
                'custom' => true,
                'template' => 'path/to/dummy/template',
            ],
        ]);
    }
}
