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
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register an instance of InvokableGrid for each service using the
 * PHP attributes to declare Grids, or using the "sylius.invokable_grid" tag.
 *
 * @internal
 */
final class InvokableGridPass implements CompilerPassInterface
{
    private const TAG = 'sylius.invokable_grid';

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
            'name' => $attribute->name ?? $class,
            'resourceClass' => $attribute->resourceClass,
            'buildMethod' => $attribute->buildMethod,
            'provider' => $attribute->provider,
        ]);
    }

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds(self::TAG, true) as $id => $tags) {
            $class = $container->findDefinition($id)->getClass();
            $asGrid = $this->resolveAsGrid($class);

            /** @var array<string, string|null> $attribute */
            foreach ($tags as $attribute) {
                $name = $attribute['name'] ?? $asGrid->name ?? $class;
                $resourceClass = $attribute['resourceClass'] ?? $asGrid->resourceClass;
                $buildMethod = $attribute['buildMethod'] ?? $asGrid->buildMethod;
                $provider = $attribute['provider'] ?? $asGrid->provider;

                $container->register('.sylius.grid.' . $id, InvokableGrid::class)
                    ->setArguments([new Reference($id), $name, $class, $resourceClass, $buildMethod, $provider])
                    ->addTag('sylius.grid', ['name' => $name])
                    ->addTag('sylius.grid', ['name' => $class])
                ;
            }
        }
    }

    private function resolveAsGrid(?string $class): AsGrid
    {
        // getClass() may be null or a "%parameter%" placeholder; only reflect real classes.
        if (null === $class || !class_exists($class)) {
            return new AsGrid();
        }

        $attributes = (new \ReflectionClass($class))->getAttributes(AsGrid::class);

        return ($attributes[0] ?? null)?->newInstance() ?? new AsGrid();
    }
}
