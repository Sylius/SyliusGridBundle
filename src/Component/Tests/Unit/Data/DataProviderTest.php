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
use Sylius\Component\Grid\Data\DataProvider;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\DataSourceProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filtering\FiltersApplicatorInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Sorting\SorterInterface;

final class DataProviderTest extends TestCase
{
    private DataSourceProviderInterface|MockObject $dataSourceProviderMock;

    private FiltersApplicatorInterface|MockObject $filtersApplicatorMock;

    private SorterInterface|MockObject $sorterMock;

    private DataProvider $dataProvider;

    protected function setUp(): void
    {
        $this->dataSourceProviderMock = $this->createMock(DataSourceProviderInterface::class);
        $this->filtersApplicatorMock = $this->createMock(FiltersApplicatorInterface::class);
        $this->sorterMock = $this->createMock(SorterInterface::class);
        $this->dataProvider = new DataProvider($this->dataSourceProviderMock, $this->filtersApplicatorMock, $this->sorterMock);
    }

    public function testImplementsGridDataProviderInterface(): void
    {
        $this->assertInstanceOf(DataProviderInterface::class, $this->dataProvider);
    }

    public function testGetsDataFromTheDataSource(): void
    {
        /** @var DataSourceInterface|MockObject $dataSourceMock */
        $dataSourceMock = $this->createMock(DataSourceInterface::class);

        /** @var Grid|MockObject $gridMock */
        $gridMock = $this->createMock(Grid::class);

        $parameters = new Parameters();

        $this->dataSourceProviderMock->expects($this->once())->method('getDataSource')->with($gridMock, $parameters)->willReturn($dataSourceMock);
        $this->filtersApplicatorMock->expects($this->once())->method('apply')->with($dataSourceMock, $gridMock, $parameters);
        $this->sorterMock->expects($this->once())->method('sort')->with($dataSourceMock, $gridMock, $parameters);
        $dataSourceMock->expects($this->once())->method('getData')->with($parameters)->willReturn(['foo', 'bar']);

        $this->assertSame(['foo', 'bar'], $this->dataProvider->getData($gridMock, $parameters));
    }
}
