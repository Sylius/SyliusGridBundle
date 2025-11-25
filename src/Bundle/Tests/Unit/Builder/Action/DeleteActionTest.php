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
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;

final class DeleteActionTest extends TestCase
{
    public function testBuildsDeleteActions(): void
    {
        $action = DeleteAction::create();

        $this->assertInstanceOf(ActionInterface::class, $action);
        $this->assertEquals([
            'type' => 'delete',
            'label' => 'sylius.ui.delete',
        ], $action->toArray());
    }

    public function testBuildsDeleteActionsWithOptions(): void
    {
        $action = DeleteAction::create(['custom' => true]);

        $this->assertInstanceOf(ActionInterface::class, $action);
        $this->assertEquals([
            'type' => 'delete',
            'label' => 'sylius.ui.delete',
            'options' => [
                'custom' => true,
            ],
        ], $action->toArray());
    }
}
