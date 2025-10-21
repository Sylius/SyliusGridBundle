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

use App\Entity\Author;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Builder\Filter\EntityFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface;

final class EntityFilterTest extends TestCase
{
    public function testCreatesEntityFilters(): void
    {
        $filter = EntityFilter::create('author', Author::class);

        $this->assertInstanceOf(FilterInterface::class, $filter);
        $this->assertEquals([
            'type' => 'entity',
            'form_options' => [
                'class' => Author::class,
            ],
        ], $filter->toArray());
    }

    public function testCreatesEntityFiltersWithMultipleOption(): void
    {
        $filter = EntityFilter::create('author', Author::class, true);

        $this->assertInstanceOf(FilterInterface::class, $filter);
        $this->assertEquals([
            'type' => 'entity',
            'form_options' => [
                'class' => Author::class,
                'multiple' => true,
            ],
        ], $filter->toArray());
    }
}
