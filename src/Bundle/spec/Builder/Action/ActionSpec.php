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
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\ActionInterface;

final class ActionTest extends TestCase
{
    private Action $action;

    protected function setUp(): void
    {
        $this->action = Action::create('create', 'create');
    }

    public function testImplementsAnInterface(): void
    {
        $this->assertInstanceOf(ActionInterface::class, $this->action);
    }

    public function testSetsLabel(): void
    {
        $this->action->setLabel('Create');

        $this->assertEquals('Create', $this->action->toArray()['label']);
    }

    public function testEnablesActions(): void
    {
        $this->action->setEnabled(true);

        $this->assertTrue($this->action->toArray()['enabled']);
    }

    public function testDisablesActions(): void
    {
        $this->action->setEnabled(false);

        $this->assertFalse($this->action->toArray()['enabled']);
    }

    public function testHasNoTemplateByDefault(): void
    {
        $this->assertNull($this->action->getTemplate());
    }

    public function testSetsTemplate(): void
    {
        $this->action->setTemplate('/path/to/template');

        $this->assertEquals('/path/to/template', $this->action->getTemplate());
    }

    public function testSetsIcon(): void
    {
        $this->action->setIcon('cogs');

        $this->assertEquals('cogs', $this->action->toArray()['icon']);
    }

    public function testSetsOptions(): void
    {
        $this->action->setOptions(['custom' => true]);

        $this->assertEquals(['custom' => true], $this->action->toArray()['options']);
    }

    public function testSetsPosition(): void
    {
        $this->action->setPosition(42);

        $this->assertEquals(42, $this->action->toArray()['position']);
    }
}
