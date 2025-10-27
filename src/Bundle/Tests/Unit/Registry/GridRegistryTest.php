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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Registry;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Grid\GridInterface;
use Sylius\Bundle\GridBundle\Registry\GridRegistry;
use Symfony\Component\DependencyInjection\ServiceLocator;

class GridRegistryTest extends TestCase
{
    private GridRegistry $registry;

    private ServiceLocator $serviceLocator;

    protected function setUp(): void
    {
        $this->serviceLocator = $this->createMock(ServiceLocator::class);
        $this->registry = new GridRegistry($this->serviceLocator);
    }

    public function testReturnsGridsFromItsCode(): void
    {
        $bookGrid = $this->createMock(GridInterface::class);

        $this->serviceLocator->method('has')->with('app_book')->willReturn(true);
        $this->serviceLocator->method('get')->with('app_book')->willReturn($bookGrid);

        $this->assertSame($bookGrid, $this->registry->getGrid('app_book'));
    }

    public function testReturnsNullWhenGridWasNotFound(): void
    {
        $this->serviceLocator->method('has')->with('not_found')->willReturn(false);

        $this->assertNull($this->registry->getGrid('not_found'));
    }
}
