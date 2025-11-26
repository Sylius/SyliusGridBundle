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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Provider;

use App\Grid\BookGrid;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Grid\GridInterface;
use Sylius\Bundle\GridBundle\Provider\ServiceGridProvider;
use Sylius\Bundle\GridBundle\Registry\GridRegistryInterface;
use Sylius\Component\Grid\Configuration\GridConfigurationExtender;
use Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandlerInterface;
use Sylius\Component\Grid\Configuration\GridConfigurationSortingHandlerInterface;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\GridProviderInterface;

final class ServiceGridProviderTest extends TestCase
{
    private ArrayToDefinitionConverterInterface $converter;

    private GridRegistryInterface $gridRegistry;

    private GridConfigurationRemovalsHandlerInterface $removalsHandler;

    private GridConfigurationSortingHandlerInterface $sortingHandler;

    private ServiceGridProvider $provider;

    protected function setUp(): void
    {
        $this->converter = $this->createMock(ArrayToDefinitionConverterInterface::class);
        $this->gridRegistry = $this->createMock(GridRegistryInterface::class);
        $this->removalsHandler = $this->createMock(GridConfigurationRemovalsHandlerInterface::class);
        $this->sortingHandler = $this->createMock(GridConfigurationSortingHandlerInterface::class);

        $this->provider = new ServiceGridProvider(
            $this->converter,
            $this->gridRegistry,
            new GridConfigurationExtender(),
            $this->removalsHandler,
            $this->sortingHandler,
        );
    }

    public function testIsAGridProvider(): void
    {
        $this->assertInstanceOf(GridProviderInterface::class, $this->provider);
    }

    public function testGetsGridDefinitionByCode(): void
    {
        $grid = $this->createMock(GridInterface::class);
        $gridDefinition = $this->createMock(Grid::class);

        $this->gridRegistry->method('getGrid')->with('app_book')->willReturn($grid);
        $grid->method('toArray')->willReturn([]);

        $this->removalsHandler->method('handle')->willReturn([]);
        $this->sortingHandler->method('handle')->willReturn([]);
        $this->converter->method('convert')->with('app_book', [])->willReturn($gridDefinition);

        $this->assertSame($gridDefinition, $this->provider->get('app_book'));
    }

    public function testGetsGridDefinitionByClassName(): void
    {
        $bookGrid = new BookGrid();
        $gridDefinition = $this->createMock(Grid::class);

        $this->gridRegistry->method('getGrid')->with('app_book')->willReturn($bookGrid);
        $this->removalsHandler->method('handle')->willReturn([]);
        $this->sortingHandler->method('handle')->willReturn([]);
        $this->converter->method('convert')->with('app_book', [])->willReturn($gridDefinition);

        $this->assertSame($gridDefinition, $this->provider->get(BookGrid::class));
    }

    public function testSupportsGridInheritance(): void
    {
        $fooGrid = $this->createMock(GridInterface::class);
        $fooFightersGrid = $this->createMock(GridInterface::class);
        $fooGridDefinition = $this->createMock(Grid::class);
        $fooFightersGridDefinition = $this->createMock(Grid::class);

        $this->gridRegistry
            ->method('getGrid')
            ->willReturnMap([
                ['app_foo', $fooGrid],
                ['app_foo_fighters', $fooFightersGrid],
            ]);

        $fooGrid->method('toArray')->willReturn(['configuration_foo' => 'foo']);
        $fooFightersGrid->method('toArray')->willReturn(['extends' => 'app_foo', 'configuration_foo_fighters' => 'foo_fighters']);

        $config = ['configuration_foo' => 'foo', 'configuration_foo_fighters' => 'foo_fighters'];

        $this->removalsHandler->method('handle')->willReturn($config);
        $this->sortingHandler->method('handle')->willReturn($config);
        $this->converter->method('convert')->with('app_foo_fighters', $config)->willReturn($fooFightersGridDefinition);

        $this->assertSame($fooFightersGridDefinition, $this->provider->get('app_foo_fighters'));
    }

    public function testThrowsUndefinedGridExceptionWhenGridIsNotFound(): void
    {
        $this->gridRegistry->method('getGrid')->willReturn(null);

        $this->expectException(UndefinedGridException::class);

        $this->provider->get('app_book');
    }

    public function testThrowsInvalidArgumentExceptionWhenParentGridIsNotFound(): void
    {
        $grid = $this->createMock(GridInterface::class);

        $this->gridRegistry->method('getGrid')->willReturnMap([
            ['app_foo_fighters', $grid],
            ['app_foo', null],
        ]);

        $grid->method('toArray')->willReturn(['extends' => 'app_foo']);

        $this->expectException(\InvalidArgumentException::class);

        $this->provider->get('app_foo_fighters');
    }

    public function testSupportsGridRemovals(): void
    {
        $grid = $this->createMock(GridInterface::class);
        $gridDefinition = $this->createMock(Grid::class);

        $this->gridRegistry->method('getGrid')->with('app_foo')->willReturn($grid);

        $grid->method('toArray')->willReturn([
            'fields' => ['customer' => []],
            'removals' => ['fields' => ['customer']],
        ]);

        $this->removalsHandler->method('handle')->willReturn(['fields' => []]);
        $this->sortingHandler->method('handle')->willReturn(['fields' => []]);
        $this->converter->method('convert')->with('app_foo', ['fields' => []])->willReturn($gridDefinition);

        $this->assertSame($gridDefinition, $this->provider->get('app_foo'));
    }

    public function testMakesFieldsSortableIfSortingIsEnabled(): void
    {
        $grid = $this->createMock(GridInterface::class);
        $gridDefinition = $this->createMock(Grid::class);

        $this->gridRegistry->method('getGrid')->with('app_foo')->willReturn($grid);

        $config = [
            'fields' => ['title' => []],
            'sorting' => ['title' => 'asc'],
        ];

        $sortableConfig = [
            'fields' => ['title' => ['sortable' => true]],
            'sorting' => ['title' => 'asc'],
        ];

        $grid->method('toArray')->willReturn($config);
        $this->removalsHandler->method('handle')->willReturn($config);
        $this->sortingHandler->method('handle')->willReturn($sortableConfig);
        $this->converter->method('convert')->with('app_foo', $sortableConfig)->willReturn($gridDefinition);

        $this->assertSame($gridDefinition, $this->provider->get('app_foo'));
    }
}
