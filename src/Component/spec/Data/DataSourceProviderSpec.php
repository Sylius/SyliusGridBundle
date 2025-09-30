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
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\DataSourceProvider;
use Sylius\Component\Grid\Data\DataSourceProviderInterface;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Data\UnsupportedDriverException;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class DataSourceProviderTest extends TestCase
{
    private ServiceRegistryInterface|MockObject $driversRegistryMock;

    private DataSourceProvider $dataSourceProvider;

    protected function setUp(): void
    {
        $this->driversRegistryMock = $this->createMock(ServiceRegistryInterface::class);
        $this->dataSourceProvider = new DataSourceProvider($this->driversRegistryMock);
    }

    public function testImplementsGridDataProviderInterface(): void
    {
        $this->assertInstanceOf(DataSourceProviderInterface::class, $this->dataSourceProvider);
    }

    public function testUsesACorrectDriverToGetTheDataForAGrid(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var DriverInterface|MockObject $driverMock */
        $driverMock = $this->createMock(DriverInterface::class);

        /** @var Grid|MockObject $gridMock */
        $gridMock = $this->createMock(Grid::class);

        $parameters = new Parameters();

        $gridMock->expects($this->once())->method('getDriver')->willReturn('doctrine/orm');
        $gridMock->expects($this->once())->method('getDriverConfiguration')->willReturn(['resource' => 'sylius.tax_category']);
        $this->driversRegistryMock->expects($this->once())->method('has')->with('doctrine/orm')->willReturn(true);
        $this->driversRegistryMock->expects($this->once())->method('get')->with('doctrine/orm')->willReturn($driverMock);
        $driverMock->expects($this->once())->method('getDataSource')->with(['resource' => 'sylius.tax_category'], $parameters)->willReturn($dataSourceMock);

        $this->assertSame($dataSourceMock, $this->dataSourceProvider->getDataSource($gridMock, $parameters));
    }

    public function testThrowsAnExceptionIfDriverIsNotSupported(): void
    {
        /** @var Grid|MockObject $gridMock */
        $gridMock = $this->createMock(Grid::class);

        $parameters = new Parameters();

        $gridMock->expects($this->once())->method('getDriver')->willReturn('doctrine/banana');
        $this->driversRegistryMock->expects($this->once())->method('has')->with('doctrine/banana')->willReturn(false);

        $this->expectException(UnsupportedDriverException::class);
        $this->expectExceptionMessage('doctrine/banana');

        $this->dataSourceProvider->getDataSource($gridMock, $parameters);
    }
}
