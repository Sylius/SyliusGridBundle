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
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;

final class CreateActionTest extends TestCase
{
    public function testBuildsCreateActions(): void
    {
        $action = CreateAction::create();

        $this->assertInstanceOf(ActionInterface::class, $action);
        $this->assertEquals([
            'type' => 'create',
            'label' => 'sylius.ui.create',
        ], $action->toArray());
    }

    public function testBuildsCreateActionWithOptions(): void
    {
        $action = CreateAction::create(['custom' => true]);

        $this->assertInstanceOf(ActionInterface::class, $action);
        $this->assertEquals([
            'type' => 'create',
            'label' => 'sylius.ui.create',
            'options' => [
                'custom' => true,
            ],
        ], $action->toArray());
    }
}
