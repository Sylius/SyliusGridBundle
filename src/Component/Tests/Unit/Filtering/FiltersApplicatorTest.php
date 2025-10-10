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

namespace Sylius\Component\Grid\Tests\Unit\Filtering;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Sylius\Component\Grid\Filtering\FiltersApplicator;
use Sylius\Component\Grid\Filtering\FiltersApplicatorInterface;
use Sylius\Component\Grid\Filtering\FiltersCriteriaResolverInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class FiltersApplicatorTest extends TestCase
{
    private FiltersApplicator $filtersApplicator;

    private ServiceRegistryInterface $filtersRegistry;

    private FiltersCriteriaResolverInterface $criteriaResolver;

    protected function setUp(): void
    {
        $this->filtersRegistry = $this->createMock(ServiceRegistryInterface::class);
        $this->criteriaResolver = $this->createMock(FiltersCriteriaResolverInterface::class);

        $this->filtersApplicator = new FiltersApplicator($this->filtersRegistry, $this->criteriaResolver);
    }

    public function testImplementsFiltersApplicatorInterface(): void
    {
        $this->assertInstanceOf(FiltersApplicatorInterface::class, $this->filtersApplicator);
    }

    public function testDoesNothingWhenThereAreNoFilteringCriteria(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $grid = $this->createMock(Grid::class);
        $filter = $this->createMock(Filter::class);
        $stringFilter = $this->createMock(FilterInterface::class);
        $parameters = new Parameters();

        $grid->method('getFilters')->willReturn(['keywords' => $filter]);
        $this->criteriaResolver->method('hasCriteria')->with($grid, $parameters)->willReturn(false);

        $stringFilter->expects($this->never())->method('apply');

        $this->filtersApplicator->apply($dataSource, $grid, $parameters);
    }

    public function testFiltersDataSourceBasedOnFiltersDefaultCriteria(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $grid = $this->createMock(Grid::class);
        $filter = $this->createMock(Filter::class);
        $stringFilter = $this->createMock(FilterInterface::class);
        $parameters = new Parameters();

        $grid->method('getFilters')->willReturn(['keywords' => $filter]);
        $grid->method('hasFilter')->willReturnMap([
            ['keywords', true],
        ]);
        $grid->method('getFilter')->with('keywords')->willReturn($filter);

        $filter->method('getType')->willReturn('string');
        $filter->method('getOptions')->willReturn(['fields' => ['firstName', 'lastName']]);

        $this->criteriaResolver->method('hasCriteria')->with($grid, $parameters)->willReturn(true);
        $this->criteriaResolver->method('getCriteria')->with($grid, $parameters)->willReturn(['keywords' => 'Banana']);

        $this->filtersRegistry->method('get')->with('string')->willReturn($stringFilter);

        $stringFilter
            ->expects($this->once())
            ->method('apply')
            ->with($dataSource, 'keywords', 'Banana', ['fields' => ['firstName', 'lastName']]);

        $this->filtersApplicator->apply($dataSource, $grid, $parameters);
    }

    public function testFiltersDataSourceBasedOnCriteriaParameter(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $grid = $this->createMock(Grid::class);
        $filter = $this->createMock(Filter::class);
        $stringFilter = $this->createMock(FilterInterface::class);
        $parameters = new Parameters(['criteria' => ['keywords' => 'Banana', 'enabled' => true]]);

        $grid->method('getFilters')->willReturn(['keywords' => $filter]);
        $grid->method('hasFilter')->willReturnMap([
            ['keywords', true],
            ['enabled', false],
        ]);
        $grid->method('getFilter')->with('keywords')->willReturn($filter);

        $filter->method('getType')->willReturn('string');
        $filter->method('getOptions')->willReturn(['fields' => ['firstName', 'lastName']]);

        $this->criteriaResolver->method('hasCriteria')->with($grid, $parameters)->willReturn(true);
        $this->criteriaResolver->method('getCriteria')->with($grid, $parameters)->willReturn(['keywords' => 'Banana', 'enabled' => true]);

        $this->filtersRegistry->method('get')->with('string')->willReturn($stringFilter);

        $stringFilter
            ->expects($this->once())
            ->method('apply')
            ->with($dataSource, 'keywords', 'Banana', ['fields' => ['firstName', 'lastName']]);

        $this->filtersApplicator->apply($dataSource, $grid, $parameters);
    }
}
