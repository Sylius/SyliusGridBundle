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

namespace Sylius\Bundle\GridBundle\DependencyInjection\Compiler;

use Sylius\Component\Grid\Exception\LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class GridMutatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('sylius.grid.mutator_collection')) {
            return;
        }

        $definition = $container->getDefinition('sylius.grid.mutator_collection');

        /** @var list<array{id: string, grid: string, priority: int}> $mutators */
        $mutators = [];

        foreach ($container->findTaggedServiceIds('sylius.grid_mutator') as $id => $attributes) {
            /**
             * @var array{grid?: ?string, priority?: ?int} $attribute
             */
            foreach ($attributes as $attribute) {
                $grid = $attribute['grid'] ?? throw new LogicException('The "sylius.grid_mutator" tag should have a "grid" attribute');

                $mutators[] = ['id' => $id, 'grid' => $grid, 'priority' => $attribute['priority'] ?? 0];
            }
        }

        uasort($mutators, static fn (array $a, array $b): int => $b['priority'] <=> $a['priority']);

        foreach ($mutators as $mutator) {
            $definition->addMethodCall('add', [
                $mutator['grid'],
                new Reference($mutator['id']),
            ]);
        }
    }
}
