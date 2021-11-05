<?php

namespace spec\Sylius\Bundle\GridBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Grid\GridInterface;
use Sylius\Bundle\GridBundle\Provider\ServiceGridProvider;
use Sylius\Bundle\GridBundle\Registry\GridRegistryInterface;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverter;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\GridProviderInterface;

class ServiceGridProviderSpec extends ObjectBehavior
{
    function let(ArrayToDefinitionConverterInterface $converter, GridRegistryInterface $gridRegistry): void
    {
        $this->beConstructedWith($converter, $gridRegistry);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ServiceGridProvider::class);
    }

    function it_is_a_grid_provider(): void
    {
        $this->shouldImplement(GridProviderInterface::class);
    }

    function it_gets_grids_definitions_by_code(
        ArrayToDefinitionConverterInterface $converter,
        GridRegistryInterface $gridRegistry,
        GridInterface $bookGrid,
        Grid $gridDefinition
    ): void {
        $gridRegistry->getGrid('app_book')->willReturn($bookGrid);
        $bookGrid->toArray()->willReturn([]);

        $converter->convert('app_book', [])->willReturn($gridDefinition);

        $this->get('app_book')->shouldReturn($gridDefinition);
    }

    function it_throws_an_undefined_grid_exception_when_grid_is_not_found(
        GridRegistryInterface $gridRegistry
    ): void {
        $gridRegistry->getGrid('app_book')->willReturn(null);

        $this->shouldThrow(UndefinedGridException::class)->during('get', ['app_book']);
    }
}
