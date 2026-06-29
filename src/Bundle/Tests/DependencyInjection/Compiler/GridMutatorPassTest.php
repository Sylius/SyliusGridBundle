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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\GridMutatorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class GridMutatorPassTest extends AbstractCompilerPassTestCase
{
    public function testItDoesNothingWhenGridMutatorCollectionDoesNotExist(): void
    {
        $this->compile();

        $this->assertContainerBuilderNotHasService('sylius.grid.mutator_collection');
    }

    public function testItDoesNothingWhenOperationMutatorCollectionDoesNotExist(): void
    {
        $this->compile();

        $this->assertContainerBuilderNotHasService('sylius.grid.mutator_collection');
    }

    public function testItAddsResourceMutatorsToCollection(): void
    {
        $mutatorCollectionDefinition = new Definition();
        $this->setDefinition('sylius.grid.mutator_collection', $mutatorCollectionDefinition);

        $mutatorDefinition = new Definition();
        $mutatorDefinition->addTag('sylius.grid_mutator', ['grid' => 'app_book']);
        $this->setDefinition('app.grid_mutator.book', $mutatorDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.grid.mutator_collection',
            'add',
            [
                'app_book',
                new Reference('app.grid_mutator.book'),
            ],
        );
    }

    public function testItAddsMultipleGridMutatorsToCollection(): void
    {
        $mutatorCollectionDefinition = new Definition();
        $this->setDefinition('sylius.grid.mutator_collection', $mutatorCollectionDefinition);

        $productMutatorDefinition = new Definition();
        $productMutatorDefinition->addTag('sylius.grid_mutator', ['grid' => 'app_product']);
        $this->setDefinition('app.grid_mutator.product', $productMutatorDefinition);

        $customerMutatorDefinition = new Definition();
        $customerMutatorDefinition->addTag('sylius.grid_mutator', ['grid' => 'app_customer']);
        $this->setDefinition('app.grid_mutator.customer', $customerMutatorDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.grid.mutator_collection',
            'add',
            [
                'app_product',
                new Reference('app.grid_mutator.product'),
            ],
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.grid.mutator_collection',
            'add',
            [
                'app_customer',
                new Reference('app.grid_mutator.customer'),
            ],
        );
    }

    public function testItSortsMutatorsByTheirPriority(): void
    {
        $mutatorCollectionDefinition = new Definition();
        $this->setDefinition('sylius.grid.mutator_collection', $mutatorCollectionDefinition);

        $productMutatorDefinition = new Definition();
        $productMutatorDefinition->addTag('sylius.grid_mutator', ['grid' => 'app_product']);
        $this->setDefinition('app.grid_mutator.product', $productMutatorDefinition);

        $customerMutatorDefinition = new Definition();
        $customerMutatorDefinition->addTag('sylius.grid_mutator', ['grid' => 'app_customer', 'priority' => 10]);
        $this->setDefinition('app.grid_mutator.customer', $customerMutatorDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.grid.mutator_collection',
            'add',
            [
                'app_customer',
                new Reference('app.grid_mutator.customer'),
            ],
            index: 0, // It has been registered first
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.grid.mutator_collection',
            'add',
            [
                'app_product',
                new Reference('app.grid_mutator.product'),
            ],
            index: 1, // It has been registered after app.grid_mutator.customer
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new GridMutatorPass());
    }
}
