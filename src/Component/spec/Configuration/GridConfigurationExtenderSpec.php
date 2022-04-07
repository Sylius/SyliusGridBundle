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
use Sylius\Component\Grid\Configuration\GridConfigurationExtender;

final class GridConfigurationExtenderSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(GridConfigurationExtender::class);
    }

    function it_extends_grid_configuration_from_another_grid(): void
    {
        $gridConfiguration = ['foo' => 'fighters'];
        $parentGridConfiguration = ['configuration1' => 'value1', 'foo' => 'bar'];

        $this->extends($gridConfiguration, $parentGridConfiguration)->shouldReturn([
            'configuration1' => 'value1',
            'foo' => 'fighters',
        ]);
    }

    function it_does_not_extend_sorting_configuration(): void
    {
        $gridConfiguration = ['foo' => 'fighters'];
        $parentGridConfiguration = ['sorting' => ['name' => 'asc']];

        $this->extends($gridConfiguration, $parentGridConfiguration)->shouldReturn([
            'foo' => 'fighters',
        ]);
    }

    function it_removes_extends_key(): void
    {
        $gridConfiguration = ['extends' => 'Artist'];
        $parentGridConfiguration = [];

        $this->extends($gridConfiguration, $parentGridConfiguration)->shouldReturn([]);
    }
}
