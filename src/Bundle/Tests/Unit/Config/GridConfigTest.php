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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Config\GridConfig;
use Sylius\Bundle\GridBundle\Config\GridConfigInterface;

final class GridConfigTest extends TestCase
{
    public function testImplementsAnInterface(): void
    {
        $gridConfig = new GridConfig();
        self::assertInstanceOf(GridConfigInterface::class, $gridConfig);
    }

    public function testAddsGrids(): void
    {
        $gridBuilder = $this->createMock(GridBuilderInterface::class);
        $gridBuilder->method('getName')->willReturn('my_grid');
        $gridBuilder->method('toArray')->willReturn(['my_grid' => []]);

        $gridConfig = new GridConfig();
        $gridConfig->addGrid($gridBuilder);
        self::assertArrayHasKey('my_grid', $gridConfig->toArray()['grids']);
    }
}
