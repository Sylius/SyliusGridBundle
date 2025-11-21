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

namespace Sylius\Component\Grid\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Configuration\GridConfigurationExtender;
use Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandlerInterface;
use Sylius\Component\Grid\Configuration\GridConfigurationSortingHandlerInterface;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\ArrayGridProvider;
use Sylius\Component\Grid\Provider\GridProviderInterface;

final class ArrayGridProviderTest extends TestCase
{
    private ArrayGridProvider $provider;

    private ArrayToDefinitionConverterInterface $converter;

    private GridConfigurationRemovalsHandlerInterface $removalsHandler;

    private GridConfigurationSortingHandlerInterface $sortingHandler;

    private Grid $firstGrid;

    private Grid $secondGrid;

    private Grid $thirdGrid;

    private Grid $fourthGrid;

    private Grid $fifthGrid;

    private Grid $sixthGrid;

    private Grid $seventhGrid;

    protected function setUp(): void
    {
        $this->converter = $this->createMock(ArrayToDefinitionConverterInterface::class);
        $this->removalsHandler = $this->createMock(GridConfigurationRemovalsHandlerInterface::class);
        $this->sortingHandler = $this->createMock(GridConfigurationSortingHandlerInterface::class);

        $this->firstGrid = $this->createMock(Grid::class);
        $this->secondGrid = $this->createMock(Grid::class);
        $this->thirdGrid = $this->createMock(Grid::class);
        $this->fourthGrid = $this->createMock(Grid::class);
        $this->fifthGrid = $this->createMock(Grid::class);
        $this->sixthGrid = $this->createMock(Grid::class);
        $this->seventhGrid = $this->createMock(Grid::class);

        $this->converter
            ->method('convert')
            ->willReturnCallback(function (string $name): Grid {
                return match ($name) {
                    'sylius_admin_tax_category' => $this->firstGrid,
                    'sylius_admin_product' => $this->secondGrid,
                    'sylius_admin_order' => $this->thirdGrid,
                    'sylius_admin_product_from_taxon' => $this->fourthGrid,
                    'sylius_admin_book' => $this->fifthGrid,
                    'sylius_admin_customer' => $this->sixthGrid,
                    'sylius_admin_book_per_author' => $this->seventhGrid,
                    default => throw new UndefinedGridException($name),
                };
            });

        $this->removalsHandler
            ->method('handle')
            ->willReturnCallback(static fn (array $config): array => $config);

        $this->sortingHandler
            ->method('handle')
            ->willReturnCallback(static fn (array $config): array => $config);

        $this->provider = new ArrayGridProvider(
            $this->converter,
            [
                'sylius_admin_tax_category' => ['configuration1'],
                'sylius_admin_product' => ['configuration2' => 'foo'],
                'sylius_admin_order' => ['configuration3'],
                'sylius_admin_product_from_taxon' => [
                    'extends' => 'sylius_admin_product',
                    'configuration4' => 'bar',
                ],
                'sylius_admin_book' => ['extends' => '404'],
                'sylius_admin_customer' => [
                    'fields' => ['customer' => []],
                    'removals' => ['fields' => ['customer']],
                ],
                'sylius_admin_book_per_author' => [
                    'fields' => [
                        'title' => [],
                    ],
                    'sorting' => [
                        'title' => 'asc',
                    ],
                ],
            ],
            new GridConfigurationExtender(),
            $this->removalsHandler,
            $this->sortingHandler,
        );
    }

    public function testImplementsGridProviderInterface(): void
    {
        self::assertInstanceOf(GridProviderInterface::class, $this->provider);
    }

    public function testReturnsClonedGridDefinitionByName(): void
    {
        self::assertSame($this->firstGrid, $this->provider->get('sylius_admin_tax_category'));
        self::assertSame($this->secondGrid, $this->provider->get('sylius_admin_product'));
        self::assertSame($this->thirdGrid, $this->provider->get('sylius_admin_order'));
    }

    public function testSupportsGridInheritance(): void
    {
        self::assertSame($this->fourthGrid, $this->provider->get('sylius_admin_product_from_taxon'));
    }

    public function testThrowsAnExceptionIfGridDoesNotExist(): void
    {
        $this->expectException(UndefinedGridException::class);
        $this->expectExceptionMessage('sylius_admin_order_item');

        $this->provider->get('sylius_admin_order_item');
    }

    public function testThrowsAnInvalidArgumentExceptionWhenParentGridIsNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->provider->get('sylius_admin_book');
    }

    public function testSupportsGridRemovals(): void
    {
        self::assertSame($this->sixthGrid, $this->provider->get('sylius_admin_customer'));
    }

    public function testMakesFieldsSortableIfSortingIsEnabledForIt(): void
    {
        self::assertSame($this->seventhGrid, $this->provider->get('sylius_admin_book_per_author'));
    }
}
