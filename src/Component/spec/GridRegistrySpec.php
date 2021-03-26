<?php

namespace spec\Sylius\Component\Grid;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\GridInterface;
use Sylius\Component\Grid\GridRegistry;
use Sylius\Component\Grid\Tests\Dummy\Bar;
use Sylius\Component\Grid\Tests\Dummy\BarGrid;
use Sylius\Component\Grid\Tests\Dummy\FooGrid;

class GridRegistrySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(new \ArrayIterator([
            new FooGrid(),
            new BarGrid(),
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
    }

    function it_returns_null_when_grid_was_not_found(GridInterface $firstGrid, GridInterface $secondGrid): void
    {
        $this->getGrid('not_found')->shouldReturn(null);
    }
}
