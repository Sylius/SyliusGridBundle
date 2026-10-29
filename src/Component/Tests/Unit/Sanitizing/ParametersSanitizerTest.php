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

namespace Sylius\Component\Grid\Tests\Unit\Sanitizing;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Pagination\MaxPerPageResolverInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Sanitizing\ParametersSanitizer;
use Sylius\Component\Grid\Sanitizing\ParametersSanitizerInterface;

final class ParametersSanitizerTest extends TestCase
{
    private ParametersSanitizer $sanitizer;

    private Grid $grid;

    protected function setUp(): void
    {
        $this->sanitizer = new ParametersSanitizer();

        $this->grid = Grid::fromCodeAndDriverConfiguration('app_book', 'doctrine/orm', []);
        $this->grid->setLimits([10, 25, 50]);

        $title = Field::fromNameAndType('title', 'string');
        $title->setSortable('title');
        $this->grid->addField($title);

        $author = Field::fromNameAndType('author', 'string');
        $author->setSortable('author.name');
        $this->grid->addField($author);

        $this->grid->addField(Field::fromNameAndType('description', 'string'));
    }

    public function testImplementsParametersSanitizerInterface(): void
    {
        $this->assertInstanceOf(ParametersSanitizerInterface::class, $this->sanitizer);
    }

    public function testKeepsValidParameters(): void
    {
        $parameters = new Parameters([
            'page' => 2,
            'limit' => 25,
            'sorting' => ['title' => 'desc', 'author' => 'asc'],
            'criteria' => ['title' => ['value' => 'Jurassic']],
        ]);

        $this->assertSame($parameters->all(), $this->sanitizer->sanitize($this->grid, $parameters)->all());
    }

    public function testKeepsMissingParametersMissing(): void
    {
        $this->assertSame([], $this->sanitizer->sanitize($this->grid, new Parameters())->all());
    }

    public function testKeepsOtherParametersAsIs(): void
    {
        $parameters = new Parameters(['criteria' => 'foo', 'custom' => ['bar']]);

        $this->assertSame($parameters->all(), $this->sanitizer->sanitize($this->grid, $parameters)->all());
    }

    #[DataProvider('pageDataProvider')]
    public function testSanitizesThePage(mixed $page, ?int $expectedPage): void
    {
        $parameters = $this->sanitizer->sanitize($this->grid, new Parameters(['page' => $page]));

        $this->assertSame(null !== $expectedPage, $parameters->has('page'));
        $this->assertSame($expectedPage, $parameters->get('page'));
    }

    public static function pageDataProvider(): iterable
    {
        yield 'valid page' => [3, 3];
        yield 'numeric string page' => ['3', 3];
        yield 'null page' => [null, null];
        yield 'zero page' => [0, null];
        yield 'negative page' => [-1, null];
        yield 'negative string page' => ['-1', null];
        yield 'non numeric page' => ['foo', null];
        yield 'float string page' => ['1.5', null];
        yield 'array page' => [[2], null];
    }

    #[DataProvider('limitDataProvider')]
    public function testSanitizesTheLimit(mixed $limit, ?int $expectedLimit): void
    {
        $parameters = $this->sanitizer->sanitize($this->grid, new Parameters(['limit' => $limit]));

        $this->assertSame(null !== $expectedLimit, $parameters->has('limit'));
        $this->assertSame($expectedLimit, $parameters->get('limit'));
    }

    public static function limitDataProvider(): iterable
    {
        yield 'grid limit' => [25, 25];
        yield 'numeric string limit' => ['50', 50];
        yield 'limit lower than the greatest grid limit' => [5, 5];
        yield 'limit greater than the greatest grid limit' => [10000, 50];
        yield 'null limit' => [null, null];
        yield 'zero limit' => [0, null];
        yield 'negative limit' => [-10, null];
        yield 'non numeric limit' => ['all', null];
    }

    public function testDelegatesTheLimitResolutionToTheMaxPerPageResolver(): void
    {
        $maxPerPageResolver = $this->createMock(MaxPerPageResolverInterface::class);
        $maxPerPageResolver
            ->expects($this->once())
            ->method('resolve')
            ->with(30, [10, 25, 50])
            ->willReturn(25)
        ;

        $parameters = (new ParametersSanitizer($maxPerPageResolver))->sanitize($this->grid, new Parameters(['limit' => '30']));

        $this->assertSame(25, $parameters->get('limit'));
    }

    /**
     * @param array<string, string>|null $expectedSorting
     */
    #[DataProvider('sortingDataProvider')]
    public function testSanitizesTheSorting(mixed $sorting, ?array $expectedSorting): void
    {
        $parameters = $this->sanitizer->sanitize($this->grid, new Parameters(['sorting' => $sorting]));

        $this->assertSame(null !== $expectedSorting, $parameters->has('sorting'));
        $this->assertSame($expectedSorting, $parameters->get('sorting'));
    }

    public static function sortingDataProvider(): iterable
    {
        yield 'valid sorting' => [['title' => 'asc'], ['title' => 'asc']];
        yield 'upper case direction' => [['title' => 'DESC'], ['title' => 'desc']];
        yield 'unknown field' => [['foo' => 'asc', 'title' => 'desc'], ['title' => 'desc']];
        yield 'non sortable field' => [['description' => 'asc', 'author' => 'desc'], ['author' => 'desc']];
        yield 'invalid direction' => [['title' => 'up', 'author' => 'asc'], ['author' => 'asc']];
        yield 'non string direction' => [['title' => ['asc']], null];
        yield 'list of fields' => [['title', 'author'], null];
        yield 'only invalid sortings' => [['foo' => 'asc', 'title' => 'sideways'], null];
        yield 'empty sorting' => [[], null];
        yield 'non array sorting' => ['title', null];
        yield 'null sorting' => [null, null];
    }
}
