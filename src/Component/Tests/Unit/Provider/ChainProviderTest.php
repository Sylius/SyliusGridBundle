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
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\ChainProvider;
use Sylius\Component\Grid\Provider\GridProviderInterface;

final class ChainProviderTest extends TestCase
{
    private GridProviderInterface $firstGridProvider;

    private GridProviderInterface $secondGridProvider;

    private ChainProvider $chainProvider;

    protected function setUp(): void
    {
        $this->firstGridProvider = $this->createMock(GridProviderInterface::class);
        $this->secondGridProvider = $this->createMock(GridProviderInterface::class);

        $this->chainProvider = new ChainProvider([
            $this->firstGridProvider,
            $this->secondGridProvider,
        ]);
    }

    public function testIsInitializable(): void
    {
        $this->assertInstanceOf(ChainProvider::class, $this->chainProvider);
    }

    public function testGetsGridsFromItsProviders(): void
    {
        $gridDefinition = $this->createMock(Grid::class);

        $this->firstGridProvider
            ->method('get')
            ->with('app_book')
            ->will($this->throwException(new UndefinedGridException('app_book')));

        $this->secondGridProvider
            ->method('get')
            ->with('app_book')
            ->willReturn($gridDefinition);

        $this->assertSame($gridDefinition, $this->chainProvider->get('app_book'));
    }

    public function testThrowsUndefinedGridExceptionWhenNoProviderHasDefinition(): void
    {
        $this->firstGridProvider
            ->method('get')
            ->with('app_book')
            ->will($this->throwException(new UndefinedGridException('app_book')));

        $this->secondGridProvider
            ->method('get')
            ->with('app_book')
            ->will($this->throwException(new UndefinedGridException('app_book')));

        $this->expectException(UndefinedGridException::class);
        $this->chainProvider->get('app_book');
    }
}
