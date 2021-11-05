<?php

namespace spec\Sylius\Bundle\GridBundle\Registry;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Grid\GridInterface;
use Sylius\Bundle\GridBundle\Registry\GridRegistry;
use Sylius\Component\Grid\Tests\Dummy\BarGrid;
use Sylius\Component\Grid\Tests\Dummy\FooGrid;
use Sylius\Component\Grid\Tests\Dummy\NoResourceGrid;
use Symfony\Component\DependencyInjection\ServiceLocator;

class GridRegistrySpec extends ObjectBehavior
{
    function let(ServiceLocator $serviceLocator): void
    {
        $this->beConstructedWith($serviceLocator);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(GridRegistry::class);
    }

    function it_returns_grids_from_its_code(
        ServiceLocator $serviceLocator,
        GridInterface $bookGrid
    ): void {
        $serviceLocator->has('app_book')->willReturn(true);
        $serviceLocator->get('app_book')->willReturn($bookGrid);

        $this->getGrid('app_book')->shouldReturn($bookGrid);
    }

    function it_returns_null_when_grid_was_not_found(ServiceLocator $serviceLocator): void
    {
        $serviceLocator->has('not_found')->willReturn(false);

        $this->getGrid('not_found')->shouldReturn(null);
    }
}
