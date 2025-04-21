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
use Sylius\Component\Grid\Configuration\GridConfigurationSortingHandler;

final class GridConfigurationSortingHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $gridConfiguration = [
            'fields' => [
                'title' => [],
                'author' => ['sortable' => false],
                'price' => ['sortable' => true],
                'name' => ['sortable' => 'translation.name'],
            ],
            'sorting' => [
                'title' => 'asc',
                'author' => 'asc',
                'price' => 'asc',
                'name' => 'asc',
            ],
        ];

        $this->assertSame([
            'fields' => [
                'title' => ['sortable' => true],
                'author' => ['sortable' => false],
                'price' => ['sortable' => true],
                'name' => ['sortable' => 'translation.name'],
            ],
            'sorting' => [
                'title' => 'asc',
                'author' => 'asc',
                'price' => 'asc',
                'name' => 'asc',
            ],
        ], (new GridConfigurationSortingHandler())->handle($gridConfiguration));
    }
}
