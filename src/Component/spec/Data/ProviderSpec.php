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

namespace Sylius\Component\Grid\Tests\Unit\Data;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Data\Provider;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

final class ProviderTest extends TestCase
{
    private ContainerInterface|MockObject $locatorMock;

    private DataProviderInterface|MockObject $decoratedMock;

    private Provider $provider;

    protected function setUp(): void
    {
        $this->locatorMock = $this->createMock(ContainerInterface::class);
        $this->decoratedMock = $this->createMock(DataProviderInterface::class);
        $this->provider = new Provider($this->locatorMock, $this->decoratedMock);
    }

    public function testCallsProviderFromDecoratedServiceWhenGridHasNoProvider(): void
    {
        /** @var Grid|MockObject $gridMock */
        $gridMock = $this->createMock(Grid::class);

        /** @var \ArrayObject|MockObject $dataMock */
        $dataMock = $this->createMock(\ArrayObject::class);

        $parameters = new Parameters();

        $gridMock->expects($this->once())->method('getProvider')->willReturn(null);
        $this->decoratedMock->expects($this->once())->method('getData')->with($gridMock, $parameters)->willReturn($dataMock);

        $this->assertSame($dataMock, $this->provider->getData($gridMock, $parameters));
    }

    public function testCallsProviderFromGridConfigurationIfThisIsACallable(): void
    {
        /** @var Grid|MockObject $gridMock */
        $gridMock = $this->createMock(Grid::class);

        $parameters = new Parameters();

        $gridMock->expects($this->once())->method('getProvider')->willReturn([GridProviderCallable::class, 'getData']);

        $this->assertSame(['callable' => true], $this->provider->getData($gridMock, $parameters));
    }

    public function testCallsProviderFromGridConfigurationIfThisIsAServiceStoredInTheLocator(): void
    {
        /** @var Grid|MockObject $gridMock */
        $gridMock = $this->createMock(Grid::class);

        /** @var DataProviderInterface|MockObject $providerMock */
        $providerMock = $this->createMock(DataProviderInterface::class);

        /** @var \ArrayObject|MockObject $dataMock */
        $dataMock = $this->createMock(\ArrayObject::class);

        $parameters = new Parameters();

        $gridMock->expects($this->once())->method('getProvider')->willReturn('App\Provider');
        $this->locatorMock->expects($this->once())->method('has')->with('App\Provider')->willReturn(true);
        $this->locatorMock->expects($this->once())->method('get')->with('App\Provider')->willReturn($providerMock);
        $providerMock->expects($this->once())->method('getData')->with($gridMock, $parameters)->willReturn($dataMock);

        $this->assertSame($dataMock, $this->provider->getData($gridMock, $parameters));
    }

    public function testThrowAnExceptionWhenGridProviderIsNotStoredInTheLocator(): void
    {
        /** @var Grid|MockObject $gridMock */
        $gridMock = $this->createMock(Grid::class);

        /** @var DataProviderInterface|MockObject $providerMock */
        $providerMock = $this->createMock(DataProviderInterface::class);

        /** @var \ArrayObject|MockObject $dataMock */
        $dataMock = $this->createMock(\ArrayObject::class);
        $parameters = new Parameters();

        $gridMock->expects($this->once())->method('getCode')->willReturn('app_dummy');
        $gridMock->expects($this->once())->method('getProvider')->willReturn('App\Provider');
        $this->locatorMock->expects($this->once())->method('has')->with('App\Provider')->willReturn(false);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Provider "App\Provider" not found on grid "app_dummy"');

        $this->provider->getData($gridMock, $parameters);
    }

    public function testThrowAnExceptionWhenGridProviderDoesNotImplementTheDataProviderInterface(): void
    {
        /** @var Grid|MockObject $gridMock */
        $gridMock = $this->createMock(Grid::class);

        /** @var \stdClass|MockObject $providerMock */
        $providerMock = $this->createMock(\stdClass::class);

        $parameters = new Parameters();

        $gridMock->expects($this->once())->method('getProvider')->willReturn('App\Provider');
        $this->locatorMock->expects($this->once())->method('has')->with('App\Provider')->willReturn(true);
        $this->locatorMock->expects($this->once())->method('get')->with('App\Provider')->willReturn($providerMock);

        $this->expectException(\InvalidArgumentException::class);

        $this->provider->getData($gridMock, $parameters);
    }
}

final class GridProviderCallable
{
    public static function getData(): array
    {
        return ['callable' => true];
    }
}
