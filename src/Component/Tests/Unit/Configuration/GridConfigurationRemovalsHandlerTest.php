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

namespace Sylius\Component\Grid\Tests\Unit\Configuration;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandler;

final class GridConfigurationRemovalsHandlerTest extends TestCase
{
    private GridConfigurationRemovalsHandler $gridConfigurationRemovalsHandler;

    protected function setUp(): void
    {
        $this->gridConfigurationRemovalsHandler = new GridConfigurationRemovalsHandler();
    }

    public function testRemovesValues(): void
    {
        $gridConfiguration = [
            'fields' => ['customer' => []],
            'actions' => [
                'item' => [
                    'show' => [],
                ],
                'subitem' => [
                    'edit' => [],
                ],
            ],
            'removals' => [
                'fields' => ['customer'],
                'actions' => [
                    0 => 'item', // this remove the item action group
                    'item' => [
                        'show', // this remove the show action in the item action group
                    ],
                    'subitem' => [
                        'edit',
                    ],
                ],
            ],
        ];

        $this->assertSame([
            'fields' => [],
            'actions' => [
                'subitem' => [],
            ],
        ], $this->gridConfigurationRemovalsHandler->handle($gridConfiguration));
    }
}
