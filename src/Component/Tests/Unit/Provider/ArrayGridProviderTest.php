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

namespace Sylius\Component\Grid\Tests\Unit\Provider;

use App\Entity\Book;
use App\Grid\Mutator\AddAuthorFieldBookGridMutator;
use App\Grid\Mutator\SortByTitleBookGridMutator;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Configuration\GridConfigurationExtender;
use Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandler;
use Sylius\Component\Grid\Configuration\GridConfigurationSortingHandler;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Mutator\GridMutatorCollection;
use Sylius\Component\Grid\Mutator\GridMutatorCollectionInterface;
use Sylius\Component\Grid\Mutator\GridMutatorInterface;
use Sylius\Component\Grid\Provider\ArrayGridProvider;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class ArrayGridProviderTest extends TestCase
{
    public function testImplementsGridProviderInterface(): void
    {
        $this->assertInstanceOf(GridProviderInterface::class, self::createProvider());
    }

    public function testProvidesAGridDefinition(): void
    {
        $provider = $this->createProvider([
            'app_book' => ['driver' => ['name' => 'doctrine/orm']],
        ]);

        $this->assertEquals(Grid::fromCodeAndDriverConfiguration('app_book', 'doctrine/orm', []), $provider->get('app_book'));
    }

    public function testSupportsGridInheritance(): void
    {
        $provider = $this->createProvider([
            'app_book' => ['driver' => ['name' => 'doctrine/orm'], 'extends' => 'app_parent_grid'],
            'app_parent_grid' => ['driver' => ['name' => 'doctrine/orm'], 'fields' => ['title' => ['type' => 'string']]],
        ]);

        $this->assertTrue($provider->get('app_book')->hasField('title'));
    }

    public function testThrowsAnExceptionIfGridDoesNotExist(): void
    {
        $this->expectException(UndefinedGridException::class);
        $this->expectExceptionMessage('sylius_admin_order_item');

        $provider = $this->createProvider();

        $provider->get('sylius_admin_order_item');
    }

    public function testThrowsAnInvalidArgumentExceptionWhenParentGridIsNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Grid with code "app_parent_grid" does not exists.');

        $provider = $this->createProvider([
            'app_book' => ['extends' => 'app_parent_grid'],
        ]);

        $provider->get('app_book');
    }

    public function testSupportsGridRemovals(): void
    {
        $provider = $this->createProvider([
            'app_book' => [
                'driver' => ['name' => 'doctrine/orm'],
                'fields' => ['title' => ['type' => 'string']],
                'removals' => ['fields' => ['title']],
            ],
        ]);

        $this->assertFalse($provider->get('app_book')->hasField('title'));
    }

    public function testMakesFieldsSortableIfSortingIsEnabledForIt(): void
    {
        $provider = $this->createProvider([
            'app_book' => [
                'driver' => ['name' => 'doctrine/orm'],
                'fields' => ['title' => ['type' => 'string']],
                'sorting' => ['title' => 'asc'],
            ],
        ]);

        self::assertTrue($provider->get('app_book')->getField('title')->isSortable());
    }

    public function testSupportsGridMutators(): void
    {
        $gridMutatorCollection = new GridMutatorCollection();
        $gridMutatorCollection->add('app_book', new SortByTitleBookGridMutator());
        $gridMutatorCollection->add('app_book', new AddAuthorFieldBookGridMutator());

        $provider = $this->createProvider([
            'app_book' => [
                'driver' => [
                    'name' => 'doctrine/orm',
                    'options' => [
                        'class' => Book::class,
                    ],
                ],
                'fields' => ['title' => ['type' => 'string']],
            ],
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

        $provider = $this->createProvider([
            'app_book' => [
                'driver' => ['name' => 'doctrine/orm'],
                'fields' => [
                    'title' => ['type' => 'string'],
                    'price' => ['type' => 'string'],
                    'author' => ['type' => 'string'],
                ],
                'removals' => ['fields' => ['price']],
            ],
        ], $gridMutatorCollection);

        $grid = $provider->get('app_book');

        $this->assertFalse($grid->hasField('author')); // OK — mutator removal works
        $this->assertFalse($grid->hasField('price'));  // FAILS — 'price' is back on the grid
    }

    public function testMutatorSortingReplacesExistingSorting(): void
    {
        $gridMutatorCollection = new GridMutatorCollection();
        $gridMutatorCollection->add('app_book', new SortByTitleBookGridMutator());

        $provider = $this->createProvider([
            'app_book' => [
                'driver' => ['name' => 'doctrine/orm'],
                'fields' => ['title' => ['type' => 'string']],
                'sorting' => ['createdAt' => 'desc'],
            ],
        ], $gridMutatorCollection);

        $this->assertSame(['title' => 'asc'], $provider->get('app_book')->getSorting());
    }

    /**
     * @param array<string, array<string, mixed>> $gridConfigurations
     */
    private function createProvider(
        array $gridConfigurations = [],
        ?GridMutatorCollectionInterface $gridMutatorCollection = null,
    ): ArrayGridProvider {
        return new ArrayGridProvider(
            new ArrayToDefinitionConverter(new EventDispatcher()),
            $gridConfigurations,
            new GridConfigurationExtender(),
            new GridConfigurationRemovalsHandler(),
            new GridConfigurationSortingHandler(),
            $gridMutatorCollection,
        );
    }
}
