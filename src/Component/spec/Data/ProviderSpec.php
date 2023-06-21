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

namespace spec\Sylius\Component\Grid\Data;

use PhpSpec\ObjectBehavior;
use Psr\Container\ContainerInterface;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Data\Provider;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

final class ProviderSpec extends ObjectBehavior
{
    function let(
        ContainerInterface $locator,
        DataProviderInterface $decorated,
    ): void {
        $this->beConstructedWith($locator, $decorated);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(Provider::class);
    }

    function it_calls_provider_from_decorated_service_when_grid_has_no_provider(
        Grid $grid,
        DataProviderInterface $decorated,
        \ArrayObject $data,
    ): void {
        $parameters = new Parameters();

        $grid->getProvider()->willReturn(null);

        $decorated->getData($grid, $parameters)->willReturn($data)->shouldBeCalled();

        $this->getData($grid, $parameters)->shouldReturn($data);
    }

    function it_calls_provider_from_grid_configuration_if_this_is_a_callable(
        Grid $grid,
    ): void {
        $parameters = new Parameters();

        $grid->getProvider()->willReturn([GridProviderCallable::class, 'getData']);

        $this->getData($grid, $parameters)->shouldReturn(['callable' => true]);
    }

    function it_calls_provider_from_grid_configuration_if_this_is_a_service_stored_in_the_locator(
        Grid $grid,
        ContainerInterface $locator,
        DataProviderInterface $provider,
        \ArrayObject $data,
    ): void {
        $parameters = new Parameters();

        $grid->getProvider()->willReturn('App\Provider');

        $locator->has('App\Provider')->willReturn(true);
        $locator->get('App\Provider')->willReturn($provider);

        $provider->getData($grid, $parameters)->willReturn($data)->shouldBeCalled();

        $this->getData($grid, $parameters)->shouldReturn($data);
    }

    function it_should_throw_an_exception_when_grid_provider_is_not_stored_in_the_locator(
        Grid $grid,
        ContainerInterface $locator,
        DataProviderInterface $provider,
        \ArrayObject $data,
    ): void {
        $parameters = new Parameters();

        $grid->getCode()->willReturn('app_dummy');
        $grid->getProvider()->willReturn('App\Provider');

        $locator->has('App\Provider')->willReturn(false);

        $this->shouldThrow(
            new \RuntimeException('Provider "App\Provider" not found on grid "app_dummy"'),
        )->during('getData', [$grid, $parameters]);
    }

    function it_should_throw_an_exception_when_grid_provider_does_not_implement_the_data_provider_interface(
        Grid $grid,
        ContainerInterface $locator,
        \stdClass $provider,
        \ArrayObject $data,
    ): void {
        $parameters = new Parameters();

        $grid->getCode()->willReturn('app_dummy');
        $grid->getProvider()->willReturn('App\Provider');

        $locator->has('App\Provider')->willReturn(true);
        $locator->get('App\Provider')->willReturn($provider);

        $this->shouldThrow(
            \InvalidArgumentException::class,
        )->during('getData', [$grid, $parameters]);
    }
}

final class GridProviderCallable
{
    public static function getData(): array
    {
        return ['callable' => true];
    }
}
