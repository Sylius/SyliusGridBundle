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

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterFiltersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('sylius.registry.grid_filter') || !$container->hasDefinition('sylius.form_registry.grid_filter')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.grid_filter');
        $formTypeRegistry = $container->getDefinition('sylius.form_registry.grid_filter');

        foreach ($container->findTaggedServiceIds('sylius.grid_filter') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['type'], $attribute['form_type'])) {
                    throw new InvalidArgumentException('Tagged grid filters needs to have `type` and `form_type` attributes.');
                }

                $registry->addMethodCall('register', [$attribute['type'], new Reference($id)]);
                $formTypeRegistry->addMethodCall('add', [$attribute['type'], 'default', $attribute['form_type']]);
            }
        }
    }
}
