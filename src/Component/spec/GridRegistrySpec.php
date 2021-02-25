<?php

namespace spec\Sylius\Component\Grid;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\GridInterface;
use Sylius\Component\Grid\GridRegistry;

class GridRegistrySpec extends ObjectBehavior
{
    function let(GridInterface $firstGrid, GridInterface $secondGrid): void
    {
        $this->beConstructedWith(new \ArrayIterator([
            'first' => $firstGrid->getWrappedObject(),
            'second' => $secondGrid->getWrappedObject(),
        ]));
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(GridRegistry::class);
    }

    function it_returns_grids_from_its_code(GridInterface $firstGrid, GridInterface $secondGrid): void
    {
        $this->getGrid('first')->shouldReturn($firstGrid);
        $this->getGrid('second')->shouldReturn($secondGrid);
    }

    function it_returns_null_when_grid_was_not_found(GridInterface $firstGrid, GridInterface $secondGrid): void
    {
        $this->getGrid('not_found')->shouldReturn(null);
    }
}
