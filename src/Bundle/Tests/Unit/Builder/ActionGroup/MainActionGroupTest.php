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
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ActionGroupInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;

final class MainActionGroupTest extends TestCase
{
    public function testBuildsAnActionGroup(): void
    {
        $subject = MainActionGroup::create();
        $this->assertInstanceOf(ActionGroupInterface::class, $subject);
    }

    public function testBuildsAnActionGroupWithActions(): void
    {
        $firstAction = $this->createMock(ActionInterface::class);
        $firstAction->method('getName')->willReturn('first');
        $firstAction->method('toArray')->willReturn([]);

        $secondAction = $this->createMock(ActionInterface::class);
        $secondAction->method('getName')->willReturn('second');
        $secondAction->method('toArray')->willReturn([]);

        $actionGroup = MainActionGroup::create($firstAction, $secondAction);

        $this->assertArrayHasKey('first', $actionGroup->toArray());
        $this->assertEquals([], $actionGroup->toArray()['first']);
        $this->assertArrayHasKey('second', $actionGroup->toArray());
        $this->assertEquals([], $actionGroup->toArray()['second']);
    }
}
