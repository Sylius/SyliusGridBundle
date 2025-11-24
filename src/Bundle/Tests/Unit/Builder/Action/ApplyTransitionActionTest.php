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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Builder\Action;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Builder\Action\ActionInterface;
use Sylius\Bundle\GridBundle\Builder\Action\ApplyTransitionAction;

final class ApplyTransitionActionTest extends TestCase
{
    public function testBuildsApplyTransitionActions(): void
    {
        $action = ApplyTransitionAction::create('publish', 'app_book_publish', ['id' => 'resource.id']);

        $this->assertInstanceOf(ActionInterface::class, $action);
        $this->assertEquals([
            'type' => 'apply_transition',
            'options' => [
                'link' => [
                    'route' => 'app_book_publish',
                    'parameters' => ['id' => 'resource.id'],
                ],
                'transition' => 'publish',
            ],
        ], $action->toArray());
    }

    public function testBuildsApplyTransitionWithOptions(): void
    {
        $action = ApplyTransitionAction::create(
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

        $this->assertInstanceOf(ActionInterface::class, $action);
        $this->assertEquals([
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
        ], $action->toArray());
    }
}
