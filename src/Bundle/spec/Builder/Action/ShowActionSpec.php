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
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;

final class ShowActionTest extends TestCase
{
    public function testBuildsShowActions(): void
    {
        $action = ShowAction::create();

        $this->assertInstanceOf(ActionInterface::class, $action);

        $this->assertEquals([
            'type' => 'show',
            'label' => 'sylius.ui.show',
        ], $action->toArray());
    }

    public function testBuildsShowActionsWithOptions(): void
    {
        $action = ShowAction::create(['custom' => true]);

        $this->assertInstanceOf(ActionInterface::class, $action);

        $this->assertEquals([
            'type' => 'show',
            'label' => 'sylius.ui.show',
            'options' => [
                'custom' => true,
            ],
        ], $action->toArray());
    }
}
