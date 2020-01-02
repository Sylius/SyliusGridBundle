<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\GridBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterDriversPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('sylius.registry.grid_driver')) {
            return;
        }

        $registry = $container->findDefinition('sylius.registry.grid_driver');

        foreach ($container->findTaggedServiceIds('sylius.grid_driver') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['alias'])) {
                    throw new \InvalidArgumentException('Tagged grid drivers needs to have `alias` attribute.');
                }

                $registry->addMethodCall('register', [$attribute['alias'], new Reference($id)]);
            }
        }
    }
}
