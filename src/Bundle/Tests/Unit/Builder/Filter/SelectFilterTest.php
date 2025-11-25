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
use Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface;
use Sylius\Bundle\GridBundle\Builder\Filter\SelectFilter;

final class SelectFilterTest extends TestCase
{
    public function testCreatesSelectFilters(): void
    {
        $filter = SelectFilter::create('search', ['sylius.ui.new' => 'new', 'sylius.ui.published' => 'published']);

        $this->assertInstanceOf(FilterInterface::class, $filter);
        $this->assertEquals([
            'type' => 'select',
            'form_options' => [
                'choices' => ['sylius.ui.new' => 'new', 'sylius.ui.published' => 'published'],
            ],
        ], $filter->toArray());
    }

    public function testCreatesSelectFiltersWithMultipleOption(): void
    {
        $filter = SelectFilter::create('search', ['sylius.ui.new' => 'new', 'sylius.ui.published' => 'published'], true);

        $this->assertInstanceOf(FilterInterface::class, $filter);
        $this->assertEquals([
            'type' => 'select',
            'form_options' => [
                'choices' => ['sylius.ui.new' => 'new', 'sylius.ui.published' => 'published'],
                'multiple' => true,
            ],
        ], $filter->toArray());
    }
}
