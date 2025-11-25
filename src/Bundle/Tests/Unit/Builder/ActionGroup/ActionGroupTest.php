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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Builder\ActionGroup;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Builder\Action\ActionInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ActionGroupInterface;

final class ActionGroupTest extends TestCase
{
    public function testImplementsAnInterface(): void
    {
        $subject = ActionGroup::create(ActionGroupInterface::MAIN_GROUP);
        $this->assertInstanceOf(ActionGroupInterface::class, $subject);
    }

    public function testAddsActions(): void
    {
        $subject = ActionGroup::create(ActionGroupInterface::MAIN_GROUP);
        $action = $this->createMock(ActionInterface::class);

        $action->method('getName')->willReturn('create');
        $action->method('toArray')->willReturn([]);

        $subject->addAction($action);

        $this->assertEquals([], $subject->toArray()['create']);
    }

    public function testAllowsToAddSeveralActionDuringInstantiation(): void
    {
        $createAction = $this->createMock(ActionInterface::class);
        $createAction->method('getName')->willReturn('create');
        $createAction->method('toArray')->willReturn([]);

        $updateAction = $this->createMock(ActionInterface::class);
        $updateAction->method('getName')->willReturn('update');
        $updateAction->method('toArray')->willReturn([]);

        $subject = ActionGroup::create(ActionGroupInterface::MAIN_GROUP, $createAction, $updateAction);

        $this->assertEquals([
            'create' => [],
            'update' => [],
        ], $subject->toArray());
    }
}
