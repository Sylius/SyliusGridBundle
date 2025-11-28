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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterGridCollectionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('sylius.grid.grid_collection')) {
            return;
        }

        $definition = $container->findDefinition('sylius.grid.grid_collection');

        $grids = $container->findTaggedServiceIds('sylius.invokable_grid');

        foreach ($grids as $id => $tags) {
            $definition->addMethodCall('add', [
                new Reference($id),
            ]);
        }
    }
}
