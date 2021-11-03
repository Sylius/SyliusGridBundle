<?php

namespace spec\Sylius\Bundle\GridBundle\Registry;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Registry\GridRegistry;
use Sylius\Component\Grid\Tests\Dummy\BarGrid;
use Sylius\Component\Grid\Tests\Dummy\FooGrid;
use Sylius\Component\Grid\Tests\Dummy\NoResourceGrid;

class GridRegistrySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(new \ArrayIterator([
            new FooGrid(),
            new BarGrid(),
            new NoResourceGrid(),
        ]));
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(GridRegistry::class);
    }

    function it_adds_grids(): void
    {
        $this->beConstructedWith(new \ArrayIterator([]));

        $grid = new FooGrid();

        $this->addGrid($grid);

        $this->getGrid('app_foo')->shouldReturn($grid);
    }

    function it_returns_grids_from_its_code(): void
    {
        $this->getGrid('app_foo')->shouldHaveType(FooGrid::class);
        $this->getGrid('app_bar')->shouldHaveType(BarGrid::class);
        $this->getGrid('app_no_resource')->shouldHaveType(NoResourceGrid::class);
    }

    function it_returns_null_when_grid_was_not_found(): void
    {
        $this->getGrid('not_found')->shouldReturn(null);
    }
}
