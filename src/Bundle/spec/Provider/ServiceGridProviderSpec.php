<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Grid\GridInterface;
use Sylius\Bundle\GridBundle\Provider\ServiceGridProvider;
use Sylius\Bundle\GridBundle\Registry\GridRegistryInterface;
use Sylius\Component\Grid\Configuration\GridConfigurationExtender;
use Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandlerInterface;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\GridProviderInterface;

class ServiceGridProviderSpec extends ObjectBehavior
{
    function let(
        ArrayToDefinitionConverterInterface $converter,
        GridRegistryInterface $gridRegistry,
        GridConfigurationRemovalsHandlerInterface $gridConfigurationRemovalsHandler,
    ): void {
        $this->beConstructedWith(
            $converter,
            $gridRegistry,
            new GridConfigurationExtender(),
            $gridConfigurationRemovalsHandler,
        );
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
        GridConfigurationRemovalsHandlerInterface $gridConfigurationRemovalsHandler,
        GridRegistryInterface $gridRegistry,
        GridInterface $bookGrid,
        Grid $gridDefinition,
    ): void {
        $gridRegistry->getGrid('app_book')->willReturn($bookGrid);
        $bookGrid->toArray()->willReturn([]);

        $converter->convert('app_book', [])->willReturn($gridDefinition);
        $gridConfigurationRemovalsHandler->handle([])->willReturn([]);

        $this->get('app_book')->shouldReturn($gridDefinition);
    }

    function it_supports_grid_inheritance(
        ArrayToDefinitionConverterInterface $converter,
        GridConfigurationRemovalsHandlerInterface $gridConfigurationRemovalsHandler,
        GridRegistryInterface $gridRegistry,
        GridInterface $fooGrid,
        GridInterface $fooFightersGrid,
        Grid $fooGridDefinition,
        Grid $fooFightersGridDefinition,
    ): void {
        $gridRegistry->getGrid('app_foo')->willReturn($fooGrid);
        $gridRegistry->getGrid('app_foo_fighters')->willReturn($fooFightersGrid);

        $fooGrid->toArray()->willReturn(['configuration_foo' => 'foo']);
        $fooFightersGrid->toArray()->willReturn(['extends' => 'app_foo', 'configuration_foo_fighters' => 'foo_fighters']);

        $converter->convert('app_foo', ['configuration_foo' => 'foo'])->willReturn($fooGridDefinition);
        $converter->convert('app_foo_fighters', ['configuration_foo' => 'foo', 'configuration_foo_fighters' => 'foo_fighters'])->willReturn($fooFightersGridDefinition);

        $gridConfigurationRemovalsHandler->handle(['configuration_foo' => 'foo', 'configuration_foo_fighters' => 'foo_fighters'])->willReturn(['configuration_foo' => 'foo', 'configuration_foo_fighters' => 'foo_fighters']);

        $this->get('app_foo_fighters')->shouldReturn($fooFightersGridDefinition);
    }

    function it_throws_an_undefined_grid_exception_when_grid_is_not_found(
        GridRegistryInterface $gridRegistry,
    ): void {
        $gridRegistry->getGrid('app_book')->willReturn(null);

        $this->shouldThrow(UndefinedGridException::class)->during('get', ['app_book']);
    }

    function it_throws_an_invalid_argument_exception_when_parent_grid_is_not_found(
        GridRegistryInterface $gridRegistry,
        GridInterface $grid,
    ): void {
        $gridRegistry->getGrid('app_foo_fighters')->willReturn($grid);
        $gridRegistry->getGrid('app_foo')->willReturn(null);

        $grid->toArray()->willReturn(['extends' => 'app_foo']);

        $this->shouldThrow(\InvalidArgumentException::class)->during('get', ['app_foo_fighters']);
    }

    function it_supports_grid_removals(
        ArrayToDefinitionConverterInterface $converter,
        GridRegistryInterface $gridRegistry,
        GridConfigurationRemovalsHandlerInterface $gridConfigurationRemovalsHandler,
        GridInterface $fooGrid,
        Grid $fooGridDefinition,
    ): void {
        $gridRegistry->getGrid('app_foo')->willReturn($fooGrid);

        $fooGrid->toArray()->willReturn([
            'fields' => ['customer' => []],
            'removals' => [
                'fields' => ['customer'],
            ],
        ]);

        $gridConfigurationRemovalsHandler->handle([
            'fields' => ['customer' => []],
            'removals' => [
                'fields' => ['customer'],
            ],
        ])->willReturn([
            'fields' => [],
        ]);

        $converter->convert('app_foo', [
            'fields' => [],
        ])->willReturn($fooGridDefinition);

        $this->get('app_foo')->shouldReturn($fooGridDefinition);
    }
}
