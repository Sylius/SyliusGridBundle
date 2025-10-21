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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Builder\Filter;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Builder\Filter\BooleanFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface;

final class BooleanFilterTest extends TestCase
{
    public function testCreatesBooleanFilters(): void
    {
        $filter = BooleanFilter::create('enabled');

        $this->assertInstanceOf(FilterInterface::class, $filter);
        $this->assertEquals([
            'type' => 'boolean',
        ], $filter->toArray());
    }
}
