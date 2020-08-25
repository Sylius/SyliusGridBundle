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

use Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterFieldTypesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('sylius.registry.grid_field')) {
            return;
        }

        $hasCurrentBundle = class_exists(CurrencyChoiceType::class);

        $registry = $container->getDefinition('sylius.registry.grid_field');

        foreach ($container->findTaggedServiceIds('sylius.grid_field') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['type'])) {
                    throw new \InvalidArgumentException('Tagged grid fields needs to have `type` attribute.');
                }

                if ('money' === $attribute['type'] && !$hasCurrentBundle) {
                    $container->removeDefinition('sylius.grid_filter.money');
                    $container->removeDefinition('sylius.form.type.grid_filter.money');

                    continue;
                }

                $registry->addMethodCall('register', [$attribute['type'], new Reference($id)]);
            }
        }
    }
}
