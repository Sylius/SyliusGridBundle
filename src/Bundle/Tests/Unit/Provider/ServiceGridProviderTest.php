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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Provider;

use App\Grid\BookGrid;
use App\Grid\Mutator\AddAuthorFieldBookGridMutator;
use App\Grid\Mutator\SortByTitleBookGridMutator;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\GridInterface as LegacyGridInterface;
use Sylius\Bundle\GridBundle\Grid\InvokableGrid;
use Sylius\Bundle\GridBundle\Provider\ServiceGridProvider;
use Sylius\Bundle\GridBundle\Registry\GridRegistry;
use Sylius\Component\Grid\Configuration\GridConfigurationExtender;
use Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandler;
use Sylius\Component\Grid\Configuration\GridConfigurationSortingHandler;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\GridInterface;
use Sylius\Component\Grid\Mutator\GridMutatorCollection;
use Sylius\Component\Grid\Mutator\GridMutatorCollectionInterface;
use Sylius\Component\Grid\Mutator\GridMutatorInterface;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class ServiceGridProviderTest extends TestCase
{
    public function testIsAGridProvider(): void
    {
        $this->assertInstanceOf(GridProviderInterface::class, $this->createProvider());
    }

    public function testGetsGridDefinitionByCode(): void
    {
        $provider = $this->createProvider([
            new BookGrid(),
        ]);

        $grid = $provider->get('app_book');

        $this->assertSame('app_book', $grid->getCode());
    }

    public function testGetsGridDefinitionByClassName(): void
    {
        $provider = $this->createProvider([
            new BookGrid(),
        ]);

        $grid = $provider->get(BookGrid::class);

        $this->assertSame('app_book', $grid->getCode());
    }

    public function testSupportsGridInheritance(): void
    {
        $provider = $this->createProvider([
            new InvokableGrid(
                function (GridBuilderInterface $gridBuilder): void {
                    $gridBuilder->extends('app_parent_grid');
                },
                'app_book',
            ),
            new InvokableGrid(
                function (GridBuilderInterface $gridBuilder): void {
                    $gridBuilder->addField(StringField::create('title'));
                },
                'app_parent_grid',
            ),
        ]);

        $this->assertTrue($provider->get('app_book')->hasField('title'));
    }

    public function testSupportsInvokableGrid(): void
    {
        $provider = $this->createProvider([
            new InvokableGrid(
                function (GridBuilderInterface $gridBuilder): void {},
                'app_book',
            ),
        ]);

        $gridDefinition = $provider->get('app_book');

        $this->assertInstanceOf(Grid::class, $gridDefinition);
    }

    public function testThrowsUndefinedGridExceptionWhenGridIsNotFound(): void
    {
        $provider = $this->createProvider();

        $this->expectException(UndefinedGridException::class);

        $provider->get('app_book');
    }

    public function testThrowsInvalidArgumentExceptionWhenParentGridIsNotFound(): void
    {
        $provider = $this->createProvider([
            new InvokableGrid(function (GridBuilderInterface $gridBuilder): void {
                $gridBuilder
                    ->extends('app_parent_grid')
                ;
            }, 'app_book'),
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Parent grid with code "app_parent_grid" does not exists.');

        $provider->get('app_book');
    }

    public function testSupportsGridRemovals(): void
    {
        $provider = $this->createProvider([
            new InvokableGrid(function (GridBuilderInterface $gridBuilder): void {
                $gridBuilder
                    ->addField(StringField::create('title'))
                    ->removeField('title')
                ;
            }, 'app_book'),
        ]);

        $this->assertFalse($provider->get('app_book')->hasField('title'));
    }

    public function testMakesFieldsSortableIfSortingIsEnabled(): void
    {
        $provider = $this->createProvider([
            new InvokableGrid(function (GridBuilderInterface $gridBuilder): void {
                $gridBuilder
                    ->addField(StringField::create('title'))
                    ->orderBy('title', 'asc')
                ;
            }, 'app_book'),
        ]);

        self::assertTrue($provider->get('app_book')->getField('title')->isSortable());
    }

    public function testSupportsGridMutatorsWithInvokableGrids(): void
    {
        $gridMutatorCollection = new GridMutatorCollection();
        $gridMutatorCollection->add('app_book', new SortByTitleBookGridMutator());
        $gridMutatorCollection->add('app_book', new AddAuthorFieldBookGridMutator());

        $provider = $this->createProvider([
            new InvokableGrid(function (GridBuilderInterface $gridBuilder): void {
                $gridBuilder
                    ->addField(StringField::create('title'))
                ;
            }, 'app_book'),
        ], $gridMutatorCollection);

        $this->assertTrue($provider->get('app_book')->hasField('title'));
        $this->assertTrue($provider->get('app_book')->hasField('author'));
        $this->assertSame(['title' => 'asc'], $provider->get('app_book')->getSorting());
    }

    public function testSupportsGridMutatorsWithLegacyGrids(): void
    {
        $gridMutatorCollection = new GridMutatorCollection();
        $gridMutatorCollection->add('app_book', new SortByTitleBookGridMutator());
        $gridMutatorCollection->add('app_book', new AddAuthorFieldBookGridMutator());

        $provider = $this->createProvider([
            new BookGrid(),
        ], $gridMutatorCollection);

        $grid = $provider->get('app_book');

        $this->assertTrue($grid->hasField('title'));
        $this->assertTrue($grid->hasField('author'));
        $this->assertSame(['title' => 'asc'], $grid->getSorting());
    }

    public function testMutatorRemovalsDoNotOverwriteGridRemovals(): void
    {
        $gridMutatorCollection = new GridMutatorCollection();
        $gridMutatorCollection->add('app_book', new class() implements GridMutatorInterface {
            public function __invoke(GridBuilderInterface $gridBuilder): void
            {
                $gridBuilder->removeField('author');
            }
        });

        $legacyGrid = new class() implements LegacyGridInterface {
            public static function getName(): string
            {
                return 'app_book';
            }

            public function toArray(): array
            {
                return [
                    'driver' => ['name' => 'doctrine/orm'],
                    'fields' => [
                        'title' => ['type' => 'string'],
                        'price' => ['type' => 'string'],
                        'author' => ['type' => 'string'],
                    ],
                    'removals' => ['fields' => ['price']],
                ];
            }

            public function buildGrid(GridBuilderInterface $gridBuilder): void
            {
            }
        };

        $provider = $this->createProvider([$legacyGrid], $gridMutatorCollection);

        $grid = $provider->get('app_book');

        $this->assertFalse($grid->hasField('author'));
        $this->assertFalse($grid->hasField('price'));
    }

    public function testMutatorSortingReplacesExistingSorting(): void
    {
        $gridMutatorCollection = new GridMutatorCollection();
        $gridMutatorCollection->add('app_book', new SortByTitleBookGridMutator());

        $legacyGrid = new class() implements LegacyGridInterface {
            public static function getName(): string
            {
                return 'app_book';
            }

            public function toArray(): array
            {
                return [
                    'driver' => ['name' => 'doctrine/orm'],
                    'fields' => ['title' => ['type' => 'string']],
                    'sorting' => ['createdAt' => 'desc'],
                ];
            }

            public function buildGrid(GridBuilderInterface $gridBuilder): void
            {
            }
        };

        $provider = $this->createProvider([$legacyGrid], $gridMutatorCollection);

        $this->assertSame(['title' => 'asc'], $provider->get('app_book')->getSorting());
    }

    /**
     * @param list<LegacyGridInterface|GridInterface> $grids
     */
    private function createProvider(
        array $grids = [],
        ?GridMutatorCollectionInterface $gridMutatorCollection = null,
    ): ServiceGridProvider {
        /** @var array<string, callable> $locatedGrids */
        $locatedGrids = [];
        foreach ($grids as $grid) {
            $locatedGrids[$grid->getName()] = fn () => $grid;
        }

        return new ServiceGridProvider(
            new ArrayToDefinitionConverter(new EventDispatcher()),
            new GridRegistry(new ServiceLocator($locatedGrids)),
            new GridConfigurationExtender(),
            new GridConfigurationRemovalsHandler(),
            new GridConfigurationSortingHandler(),
            $gridMutatorCollection,
        );
    }
}
