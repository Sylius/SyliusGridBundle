<?php

namespace spec\Sylius\Component\Grid\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\ChainProvider;
use Sylius\Component\Grid\Provider\GridProviderInterface;

class ChainProviderSpec extends ObjectBehavior
{
    function let(GridProviderInterface $firstGridProvider, GridProviderInterface $secondGridProvider): void
    {
        $this->beConstructedWith([
            $firstGridProvider->getWrappedObject(),
            $secondGridProvider->getWrappedObject(),
        ]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ChainProvider::class);
    }

    function it_get_grids_from_its_providers(
        GridProviderInterface $firstGridProvider,
        GridProviderInterface $secondGridProvider,
        Grid $gridDefinition
    ): void
    {
        $firstGridProvider->get('app_book')->willThrow(UndefinedGridException::class);
        $secondGridProvider->get('app_book')->willReturn($gridDefinition);

        $this->get('app_book')->shouldReturn($gridDefinition);
    }

    function it_throws_an_undefined_grid_exception_when_its_providers_do_not_contains_definition(
        GridProviderInterface $firstGridProvider,
        GridProviderInterface $secondGridProvider
    ): void
    {
        $firstGridProvider->get('app_book')->willThrow(UndefinedGridException::class);
        $secondGridProvider->get('app_book')->willThrow(UndefinedGridException::class);

        $this->shouldThrow(UndefinedGridException::class)->during('get', ['app_book']);
    }
}
