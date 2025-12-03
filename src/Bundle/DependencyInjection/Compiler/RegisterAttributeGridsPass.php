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

use Sylius\Bundle\GridBundle\Grid\InvokableGrid;
use Sylius\Component\Grid\Attribute\AsGrid;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterAttributeGridsPass implements CompilerPassInterface
{
    private const TAG = 'sylius.attribute_grid';

    /**
     * @param \ReflectionClass<object> $reflector
     */
    public static function autoconfigureFromGridAttribute(ChildDefinition $definition, AsGrid $attribute, \ReflectionClass $reflector): void
    {
        if (
            !$reflector->hasMethod('__invoke')
        ) {
            return;
        }

        $definition->addTag(self::TAG);
    }

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds(self::TAG, true) as $serviceId => $tags) {
            /**
             * @var int $id
             * @var array{string, mixed} $attribute
             */
            foreach ($tags as $id => $attribute) {
                $container->register('.sylius.grid.' . $serviceId . $id, InvokableGrid::class)
                    ->setArguments([
                        new Reference($serviceId),
                    ])
                    ->addTag('sylius.invokable_grid');
            }
        }
    }
}
