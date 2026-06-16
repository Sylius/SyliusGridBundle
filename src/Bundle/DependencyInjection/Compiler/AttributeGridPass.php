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
use Sylius\Bundle\GridBundle\Grid\InvokableGrid;
use Sylius\Component\Grid\Attribute\AsGrid;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register an instance of AttributeGrid for each service using the
 * PHP attributes to declare Grids.
 *
 * @internal
 */
final class AttributeGridPass implements CompilerPassInterface
{
    private const TAG = 'sylius.attribute_grid';

    /**
     * @param \ReflectionClass<AsGrid> $reflector
     */
    public static function autoconfigureFromAttribute(ChildDefinition $definition, AsGrid $attribute, \ReflectionClass $reflector): void
    {
        $class = $reflector->name;
        if (is_a($class, GridInterface::class, true)) {
            return;
        }

        $definition->addTag(self::TAG, [
            'class' => $class,
            'name' => $attribute->name ?? $class,
            'resourceClass' => $attribute->resourceClass,
            'buildMethod' => $attribute->buildMethod,
            'provider' => $attribute->provider,
        ]);
    }

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds(self::TAG, true) as $id => $tags) {
            /** @var array<string, string|null> $attribute */
            foreach ($tags as $attribute) {
                /** @var string $class */
                $class = $attribute['class'] ?? throw new LogicException(sprintf('"class" attribute not found on "sylius.grid" tag for "%s" service.', $id));

                /** @var string $name */
                $name = $attribute['name'] ?? $class;

                /** @var string|null $resourceClass */
                $resourceClass = $attribute['resourceClass'] ?? null;

                /** @var string|null $buildMethod */
                $buildMethod = $attribute['buildMethod'] ?? null;

                /** @var string|null $provider */
                $provider = $attribute['provider'] ?? null;

                $container->register('.sylius.grid.' . $id, InvokableGrid::class)
                    ->setArguments([new Reference($id), $name, $class, $resourceClass, $buildMethod, $provider])
                    ->addTag('sylius.grid', ['name' => $name])
                    ->addTag('sylius.grid', ['name' => $class])
                ;
            }
        }
    }
}
