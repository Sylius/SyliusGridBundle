<?php

declare(strict_types=1);

namespace spec\Sylius\Component\Grid\Config\Builder;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Config\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Config\Builder\GridConfig;
use Sylius\Component\Grid\Config\Builder\GridConfigInterface;

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
