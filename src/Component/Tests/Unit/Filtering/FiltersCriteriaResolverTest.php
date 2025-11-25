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
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filtering\FiltersCriteriaResolver;
use Sylius\Component\Grid\Filtering\FiltersCriteriaResolverInterface;
use Sylius\Component\Grid\Parameters;

final class FiltersCriteriaResolverTest extends TestCase
{
    private FiltersCriteriaResolver $filtersCriteriaResolver;

    protected function setUp(): void
    {
        $this->filtersCriteriaResolver = new FiltersCriteriaResolver();
    }

    public function testItImplementsFiltersCriteriaResolverInterface(): void
    {
        $this->assertInstanceOf(FiltersCriteriaResolverInterface::class, $this->filtersCriteriaResolver);
    }

    /**
     * @dataProvider criteriaDataProvider
     */
    public function testChecksWhetherAnyCriteriaAreAvailable(array $filtersCriteria, Parameters $parameters, bool $expected): void
    {
        $grid = $this->createMock(Grid::class);

        $filters = [];
        foreach ($filtersCriteria as $criteria) {
            $filter = $this->createMock(Filter::class);
            $filter->method('getCriteria')->willReturn($criteria);
            $filters[] = $filter;
        }

        $grid->method('getFilters')->willReturn($filters);

        self::assertSame($expected, $this->filtersCriteriaResolver->hasCriteria($grid, $parameters));
    }

    public function testGetsDefaultCriteriaFromGridFilters(): void
    {
        $grid = $this->createMock(Grid::class);
        $firstFilter = $this->createMock(Filter::class);
        $secondFilter = $this->createMock(Filter::class);

        $startDate = new \DateTime();
        $endDate = new \DateTime();

        $firstFilter->method('getCriteria')->willReturn('Pug');
        $secondFilter->method('getCriteria')->willReturn(['start' => $startDate, 'end' => $endDate]);

        $grid->method('getFilters')->willReturn([
            'favourite' => $firstFilter,
            'date' => $secondFilter,
        ]);

        $result = $this->filtersCriteriaResolver->getCriteria($grid, new Parameters());

        $this->assertSame([
            'favourite' => 'Pug',
            'date' => ['start' => $startDate, 'end' => $endDate],
        ], $result);
    }

    public function testGetsCriteriaFromParameters(): void
    {
        $grid = $this->createMock(Grid::class);
        $firstFilter = $this->createMock(Filter::class);
        $secondFilter = $this->createMock(Filter::class);

        $startDate = new \DateTime();
        $endDate = new \DateTime();

        $firstFilter->method('getCriteria')->willReturn(null);
        $secondFilter->method('getCriteria')->willReturn(null);

        $grid->method('getFilters')->willReturn([
            'favourite' => $firstFilter,
            'date' => $secondFilter,
        ]);

        $parameters = new Parameters([
            'criteria' => [
                'favourite' => 'Pug',
                'date' => ['start' => $startDate, 'end' => $endDate],
            ],
        ]);

        $result = $this->filtersCriteriaResolver->getCriteria($grid, $parameters);

        $this->assertSame([
            'favourite' => 'Pug',
            'date' => ['start' => $startDate, 'end' => $endDate],
        ], $result);
    }

    public function testPrioritizesParametersCriteriaOverFiltersDefault(): void
    {
        $grid = $this->createMock(Grid::class);
        $firstFilter = $this->createMock(Filter::class);
        $secondFilter = $this->createMock(Filter::class);

        $parametersDate = new \DateTime();

        $firstFilter->method('getCriteria')->willReturn('Rum');
        $secondFilter->method('getCriteria')->willReturn(null);

        $grid->method('getFilters')->willReturn([
            'favourite' => $firstFilter,
            'date' => $secondFilter,
        ]);

        $parameters = new Parameters([
            'criteria' => [
                'favourite' => 'Pug',
                'date' => ['now' => $parametersDate],
            ],
        ]);

        $result = $this->filtersCriteriaResolver->getCriteria($grid, $parameters);

        $this->assertSame([
            'favourite' => 'Pug',
            'date' => ['now' => $parametersDate],
        ], $result);
    }

    public static function criteriaDataProvider(): iterable
    {
        $emptyParameters = new Parameters();
        $criteriaParameters = new Parameters(['criteria' => ['czapla']]);

        return [
            'no filters, no criteria' => [
                [],                               // filtersCriteria
                $emptyParameters,                 // parameters
                false,                            // expected
            ],
            'no filters, but criteria provided' => [
                [],
                $criteriaParameters,
                true,
            ],
            'one filter, no criteria' => [
                [null],
                $emptyParameters,
                false,
            ],
            'one filter, parameters have criteria' => [
                [null],
                $criteriaParameters,
                true,
            ],
            'filter has default criteria' => [
                ['czapla'],
                $emptyParameters,
                true,
            ],
            'filter and parameters both have criteria' => [
                ['czapla'],
                $criteriaParameters,
                true,
            ],
        ];
    }
}
