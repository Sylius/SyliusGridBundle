<?php

namespace spec\Sylius\Component\Grid;

use App\Entity\Book;
use App\Grid\AuthorGrid;
use App\Grid\BookByEnglishAuthorsGrid;
use App\Grid\BookGrid;
use App\QueryBuilder\EnglishBooksQueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\GridInterface;
use Sylius\Component\Grid\GridRegistry;

class GridRegistrySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(new \ArrayIterator([
            new AuthorGrid(),
            new BookGrid(),
        ]));
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(GridRegistry::class);
    }

    function it_adds_grids(): void
    {
        $this->beConstructedWith(new \ArrayIterator([]));

        $grid = new BookGrid();

        $this->addGrid($grid);

        $this->getGrid('app_book')->shouldReturn($grid);
    }

    function it_returns_grids_from_its_code(): void
    {
        $this->getGrid('app_author')->shouldHaveType(AuthorGrid::class);
        $this->getGrid('app_book')->shouldHaveType(BookGrid::class);
    }

    function it_returns_null_when_grid_was_not_found(GridInterface $firstGrid, GridInterface $secondGrid): void
    {
        $this->getGrid('not_found')->shouldReturn(null);
    }
}
