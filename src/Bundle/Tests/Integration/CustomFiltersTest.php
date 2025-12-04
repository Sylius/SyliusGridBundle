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

namespace Integration;

use App\Field\FirstCustomField;
use Sylius\Component\Registry\ServiceRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CustomFiltersTest extends KernelTestCase
{
    public function testItRegistersCustomFilters(): void
    {
        $container = static::getContainer();

        /** @var ServiceRegistry $fieldRegistry */
        $fieldRegistry = $container->get('sylius.registry.grid_field');

        self::assertTrue($fieldRegistry->has(FirstCustomField::class));
        self::assertTrue($fieldRegistry->has('custom_two'));
    }
}
