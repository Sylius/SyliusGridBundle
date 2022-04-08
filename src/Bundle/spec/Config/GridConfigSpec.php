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

namespace spec\Sylius\Bundle\GridBundle\Config;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Config\GridConfig;
use Sylius\Bundle\GridBundle\Config\GridConfigInterface;

final class GridConfigSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(GridConfig::class);
    }

    function it_implements_an_interface(): void
    {
        $this->shouldImplement(GridConfigInterface::class);
    }

    function it_adds_grids(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder->getName()->willReturn('my_grid');
        $gridBuilder->toArray()->willReturn(['my_grid' => []]);

        $this->addGrid($gridBuilder);
        $this->toArray()['grids']->shouldHaveKey('my_grid');
    }
}
