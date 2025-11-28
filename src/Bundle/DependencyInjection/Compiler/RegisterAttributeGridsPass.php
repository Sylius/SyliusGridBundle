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

use Sylius\Bundle\GridBundle\Grid\GridInterface;
use Sylius\Component\Grid\Attribute\AsGrid;
use Sylius\Component\Grid\Metadata\Grid\InvokableGrid;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterAttributeGridsPass implements CompilerPassInterface
{
    private const TAG = 'sylius.attribute_grid';

    public static function autoconfigureFromGridAttribute(ChildDefinition $definition, AsGrid $attribute, \ReflectionClass $reflector): void
    {
        if ($reflector->implementsInterface(GridInterface::class)) {
            return;
        }

        $definition->addTag(self::TAG, [
            'resource_class' => $attribute->resourceClass,
            'name' => $attribute->name ?? $reflector->getName(),
            'build_method' => $attribute->buildMethod,
            'provider' => $attribute->provider,
        ]);
    }

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds(self::TAG, true) as $serviceId => $tags) {
            foreach ($tags as $id => $attribute) {
                if (!isset($attribute['name'])) {
                    throw new \InvalidArgumentException('Tagged grids need to have a `name` attribute.');
                }

                $container->register('.sylius.grid.' . $serviceId . $id, InvokableGrid::class)
                    ->setArguments([
                        $attribute['name'],
                        new Reference($serviceId),
                        $attribute['resource_class'] ?? null,
                    ])
                    ->addTag('sylius.invokable_grid');
            }
        }
    }
}
