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

namespace Sylius\Component\Grid\Tests\Unit\Configuration;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Configuration\GridConfigurationExtender;

final class GridConfigurationExtenderTest extends TestCase
{
    private GridConfigurationExtender $gridConfigurationExtender;

    protected function setUp(): void
    {
        $this->gridConfigurationExtender = new GridConfigurationExtender();
    }

    public function testExtendsGridConfigurationFromAnotherGrid(): void
    {
        $gridConfiguration = ['foo' => 'fighters'];
        $parentGridConfiguration = ['configuration1' => 'value1', 'foo' => 'bar'];

        $this->assertSame([
            'configuration1' => 'value1',
            'foo' => 'fighters',
        ], $this->gridConfigurationExtender->extends($gridConfiguration, $parentGridConfiguration));
    }

    public function testDoesNotExtendSortingConfiguration(): void
    {
        $gridConfiguration = ['foo' => 'fighters'];
        $parentGridConfiguration = ['sorting' => ['name' => 'asc']];

        $this->assertSame([
            'foo' => 'fighters',
        ], $this->gridConfigurationExtender->extends($gridConfiguration, $parentGridConfiguration));
    }

    public function testRemovesExtendsKey(): void
    {
        $gridConfiguration = ['extends' => 'Artist'];
        $parentGridConfiguration = [];

        $this->assertSame([], $this->gridConfigurationExtender->extends($gridConfiguration, $parentGridConfiguration));
    }
}
