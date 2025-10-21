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
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;

final class UpdateActionTest extends TestCase
{
    public function testBuildsUpdateActions(): void
    {
        $action = UpdateAction::create();

        $this->assertInstanceOf(ActionInterface::class, $action);

        $this->assertEquals([
            'type' => 'update',
            'label' => 'sylius.ui.edit',
        ], $action->toArray());
    }

    public function testBuildsUpdateActionsWithOptions(): void
    {
        $action = UpdateAction::create(['custom' => true]);

        $this->assertInstanceOf(ActionInterface::class, $action);

        $this->assertEquals([
            'type' => 'update',
            'label' => 'sylius.ui.edit',
            'options' => [
                'custom' => true,
            ],
        ], $action->toArray());
    }
}
