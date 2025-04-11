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

use Sylius\Component\Grid\Attribute\AsFilter;
use Sylius\Component\Grid\Filtering\FormTypeAwareFilterInterface;
use Sylius\Component\Grid\Filtering\TypeAwareFilterInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterFiltersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('sylius.registry.grid_filter') || !$container->hasDefinition('sylius.form_registry.grid_filter')) {
            return;
        }

        $filterRegistry = $container->getDefinition('sylius.registry.grid_filter');
        $formTypeRegistry = $container->getDefinition('sylius.form_registry.grid_filter');

        foreach ($container->findTaggedServiceIds(AsFilter::SERVICE_TAG) as $id => $attributes) {
            $definition = $container->getDefinition($id);
            $class = $definition->getClass();

            $type = null;
            $formType = null;

            if ($class !== null && is_a($class, TypeAwareFilterInterface::class, true)) {
                $type = $class::getType();
            }

            if ($class !== null && is_a($class, FormTypeAwareFilterInterface::class, true)) {
                $formType = $class::getFormType();
            }

            $this->registerFilter($container, $filterRegistry, $formTypeRegistry, $id, $attributes, $type, $formType);
        }
    }

    private function registerFilter(
        ContainerBuilder $container,
        Definition $filterRegistry,
        Definition $formTypeRegistry,
        string $id,
        array $attributes,
        ?string $type = null,
        ?string $formType = null,
    ): void {
        foreach ($attributes as $attribute) {
            $template = $attribute['template'] ?? null;

            $filterType = $type ?? $attribute['type'] ?? null;
            $filterFormType = $formType ?? $attribute['form_type'] ?? null;

            if (null === $filterType) {
                throw new \InvalidArgumentException(sprintf('Tagged grid filters needs to have "type" attribute or implements "%s".', TypeAwareFilterInterface::class));
            }

            if (null === $filterFormType) {
                throw new \InvalidArgumentException(sprintf('Tagged grid filters needs to have "form_type" attribute or implements "%s".', FormTypeAwareFilterInterface::class));
            }

            $filterRegistry->addMethodCall('register', [$filterType, new Reference($id)]);
            $formTypeRegistry->addMethodCall('add', [$filterType, 'default', $filterFormType]);

            if (null !== $template) {
                $this->registerFilterTemplate($container, $filterType, $template);
            }
        }
    }

    private function registerFilterTemplate(ContainerBuilder $container, string $filterType, string $template): void
    {
        /** @var array<string, string> $filtersConfig */
        $filtersConfig = $container->hasParameter('sylius.grid.templates.filter') ? $container->getParameter('sylius.grid.templates.filter') : [];

        $filtersConfig[$filterType] = $template;
        $container->setParameter('sylius.grid.templates.filter', $filtersConfig);
    }
}
