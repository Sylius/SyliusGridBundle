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

namespace Sylius\Bundle\GridBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\InvokableGridPass;
use Sylius\Bundle\GridBundle\Grid\InvokableGrid;
use Sylius\Component\Grid\Attribute\AsGrid;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class InvokableGridPassTest extends TestCase
{
    public function test_it_adds_attribute_grid_tag_when_autoconfiguring(): void
    {
        $definition = new ChildDefinition('base');

        $attribute = new AsGrid(
            resourceClass: 'App\Entity\Product',
            name: 'admin_product',
            buildMethod: 'buildGrid',
            provider: 'app.provider',
        );

        $reflector = new \ReflectionClass(DummyGrid::class);

        InvokableGridPass::autoconfigureFromAttribute(
            $definition,
            $attribute,
            $reflector,
        );

        $this->assertSame([
            [
                'name' => 'admin_product',
                'resourceClass' => 'App\Entity\Product',
                'buildMethod' => 'buildGrid',
                'provider' => 'app.provider',
            ],
        ], $definition->getTag('sylius.invokable_grid'));
    }

    public function test_it_registers_invokable_grid_services(): void
    {
        $container = new ContainerBuilder();

        $container
            ->register('app.grid', DummyGrid::class)
            ->addTag('sylius.invokable_grid', [
                'name' => 'admin_product',
                'resourceClass' => 'App\Entity\Product',
                'buildMethod' => 'buildGrid',
                'provider' => 'app.provider',
            ]);

        $pass = new InvokableGridPass();

        $pass->process($container);

        $definition = $container->getDefinition('.sylius.grid.app.grid');

        $this->assertSame(
            InvokableGrid::class,
            $definition->getClass(),
        );

        $this->assertEquals([
            new Reference('app.grid'),
            'admin_product',
            DummyGrid::class,
            'App\Entity\Product',
            'buildGrid',
            'app.provider',
        ], $definition->getArguments());

        $this->assertSame([
            ['name' => 'admin_product'],
            ['name' => DummyGrid::class],
        ], $definition->getTag('sylius.grid'));
    }

    public function test_it_reads_metadata_from_as_grid_attribute_when_tag_has_no_name(): void
    {
        $container = new ContainerBuilder();

        $container
            ->register('app.grid', AttributedDummyGrid::class)
            ->addTag('sylius.invokable_grid');

        $pass = new InvokableGridPass();

        $pass->process($container);

        $definition = $container->getDefinition('.sylius.grid.app.grid');

        $this->assertEquals([
            new Reference('app.grid'),
            'app_book',
            AttributedDummyGrid::class,
            'App\Entity\Book',
            'build',
            'app.book_provider',
        ], $definition->getArguments());

        $this->assertSame([
            ['name' => 'app_book'],
            ['name' => AttributedDummyGrid::class],
        ], $definition->getTag('sylius.grid'));
    }

    public function test_tag_attributes_override_the_as_grid_attribute(): void
    {
        $container = new ContainerBuilder();

        $container
            ->register('app.grid', AttributedDummyGrid::class)
            ->addTag('sylius.invokable_grid', ['name' => 'overridden']);

        $pass = new InvokableGridPass();

        $pass->process($container);

        $definition = $container->getDefinition('.sylius.grid.app.grid');

        $this->assertEquals([
            new Reference('app.grid'),
            'overridden',
            AttributedDummyGrid::class,
            'App\Entity\Book',
            'build',
            'app.book_provider',
        ], $definition->getArguments());

        $this->assertSame([
            ['name' => 'overridden'],
            ['name' => AttributedDummyGrid::class],
        ], $definition->getTag('sylius.grid'));
    }
}

final class DummyGrid
{
}

#[AsGrid(resourceClass: 'App\Entity\Book', name: 'app_book', buildMethod: 'build', provider: 'app.book_provider')]
final class AttributedDummyGrid
{
}
