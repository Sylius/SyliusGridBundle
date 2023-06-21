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
use Sylius\Bundle\GridBundle\Builder\Action\ApplyTransitionAction;

final class ApplyTransitionActionSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(ApplyTransitionAction::class);
    }

    function it_builds_apply_transition_actions(): void
    {
        $action = $this::create('publish', 'app_book_publish', ['id' => 'resource.id']);

        $action->shouldHaveType(ActionInterface::class);
        $action->toArray()->shouldReturn([
            'type' => 'apply_transition',
            'options' => [
                'link' => [
                    'route' => 'app_book_publish',
                    'parameters' => ['id' => 'resource.id'],
                ],
                'transition' => 'publish',
            ],
        ]);
    }

    function it_builds_apply_transition_with_options(): void
    {
        $action = $this::create(
            'publish',
            'app_book_publish',
            [
                'id' => 'resource.id',
            ],
            [
                'class' => 'green',
                'graph' => 'sylius_book_publishing',
            ],
        );

        $action->shouldHaveType(ActionInterface::class);
        $action->toArray()->shouldReturn([
            'type' => 'apply_transition',
            'options' => [
                'link' => [
                    'route' => 'app_book_publish',
                    'parameters' => ['id' => 'resource.id'],
                ],
                'transition' => 'publish',
                'class' => 'green',
                'graph' => 'sylius_book_publishing',
            ],
        ]);
    }
}
