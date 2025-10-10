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

namespace Sylius\Component\Grid\Tests\Unit\View;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Grid\View\GridViewFactory;
use Sylius\Component\Grid\View\GridViewFactoryInterface;

final class GridViewFactoryTest extends TestCase
{
    private GridViewFactoryInterface $gridViewFactory;

    private DataProviderInterface $dataProvider;

    protected function setUp(): void
    {
        $this->dataProvider = $this->createMock(DataProviderInterface::class);
        $this->gridViewFactory = new GridViewFactory($this->dataProvider);
    }

    public function testImplementsGridViewFactoryInterface(): void
    {
        $this->assertInstanceOf(GridViewFactoryInterface::class, $this->gridViewFactory);
    }

    public function testUsesDataProviderToCreateAViewWithDataAndDefinition(): void
    {
        $grid = $this->createMock(Grid::class);
        $parameters = new Parameters();

        $data = ['foo', 'bar'];
        $expectedGridView = new GridView($data, $grid, $parameters);

        $this->dataProvider
            ->expects($this->once())
            ->method('getData')
            ->with($grid, $parameters)
            ->willReturn($data);

        $actualGridView = $this->gridViewFactory->create($grid, $parameters);

        $this->assertEquals($expectedGridView, $actualGridView);
    }
}
