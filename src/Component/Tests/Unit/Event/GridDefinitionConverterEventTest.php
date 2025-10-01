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

namespace Sylius\Component\Grid\Tests\Unit\Event;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;

final class GridDefinitionConverterEventTest extends TestCase
{
    private Grid|MockObject $gridMock;

    private GridDefinitionConverterEvent $gridDefinitionConverterEvent;

    protected function setUp(): void
    {
        $this->gridMock = $this->createMock(Grid::class);
        $this->gridDefinitionConverterEvent = new GridDefinitionConverterEvent($this->gridMock);
    }

    public function testHasAGrid(): void
    {
        $this->assertSame($this->gridMock, $this->gridDefinitionConverterEvent->getGrid());
    }
}
