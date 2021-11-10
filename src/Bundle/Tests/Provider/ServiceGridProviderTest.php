<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\GridBundle\Tests\Provider;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ServiceGridProviderTest extends KernelTestCase
{
    /**
     * @test
     */
    public function test_grids_inheritance(): void
    {
        self::bootKernel(['environment' => 'test_grids_as_service']);

        $container = static::$container;

        $serviceGridProvider = $container->get('sylius.grid.service_grid_provider');

        $gridDefinition = $serviceGridProvider->get('app_bookmark');

        $this->assertEquals([], array_keys($gridDefinition->getSorting()));

        $this->assertEquals([
            'title',
            'author',
            'nationality',
        ], array_keys($gridDefinition->getFields()));

        $this->assertEquals([
            'title',
            'author',
            'nationality',
            'currencyCode',
            'state',
        ], array_keys($gridDefinition->getFilters()));

        $this->assertEquals([
            10,
            5,
            15,
        ], $gridDefinition->getLimits());
    }
}
