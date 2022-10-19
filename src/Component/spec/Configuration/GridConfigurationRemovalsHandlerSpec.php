<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Grid\Configuration;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandler;

final class GridConfigurationRemovalsHandlerSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(GridConfigurationRemovalsHandler::class);
    }

    function it_removes_values(): void
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

        $this->handle($gridConfiguration)->shouldReturn([
            'fields' => [],
            'actions' => [
                'subitem' => [],
            ],
        ]);
    }
}
