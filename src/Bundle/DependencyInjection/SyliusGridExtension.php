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

namespace Sylius\Bundle\GridBundle\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\PHPCRBundle\DoctrinePHPCRBundle;
use Sylius\Bundle\CurrencyBundle\SyliusCurrencyBundle;
use Sylius\Bundle\GridBundle\Grid\GridInterface;
use Sylius\Bundle\GridBundle\SyliusGridBundle;
use Sylius\Component\Grid\Annotation\AsGridFieldCallableService;
use Sylius\Component\Grid\Attribute\AsFilter;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Filtering\ConfigurableFilterInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Twig\Environment;

final class SyliusGridExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.php');

        /** @var array<string, string> $actionTemplates */
        $actionTemplates = $config['templates']['action'];
        /** @var array<string, string> $bulkActionTemplates */
        $bulkActionTemplates = $config['templates']['bulk_action'];
        /** @var array<string, string> $filterTemplates */
        $filterTemplates = $config['templates']['filter'];
        /** @var array<string, mixed> $gridsDefinitions */
        $gridsDefinitions = $config['grids'];

        $container->setParameter('sylius.grid.templates.action', $actionTemplates);
        $container->setParameter('sylius.grid.templates.bulk_action', $bulkActionTemplates);
        $container->setParameter('sylius.grid.templates.filter', $filterTemplates);
        $container->setParameter('sylius.grids_definitions', $gridsDefinitions);

        $container->setAlias('sylius.grid.renderer', 'sylius.grid.renderer.twig');
        $container->setAlias('sylius.grid.bulk_action_renderer', 'sylius.grid.bulk_action_renderer.twig');
        $container->setAlias('sylius.grid.data_extractor', 'sylius.grid.data_extractor.property_access');

        if ($container::willBeAvailable('twig/twig', Environment::class, ['symfony/twig-bundle'])) {
            $loader->load('services/integrations/twig.php');
        }

        if (\class_exists(SyliusCurrencyBundle::class)) {
            $loader->load('services/integrations/sylius_currency_bundle.php');
        }

        if (\class_exists(DoctrineBundle::class)) {
            $loader->load('services/integrations/doctrine/orm.php');
        }

        if (\class_exists(DoctrinePHPCRBundle::class)) {
            @trigger_error(sprintf(
                'The "%s" driver is deprecated in Sylius 1.3. Doctrine PHPCR will no longer be supported in Sylius 2.0.',
                SyliusGridBundle::DRIVER_DOCTRINE_PHPCR_ODM,
            ), \E_USER_DEPRECATED);
            $loader->load('services/integrations/doctrine/phpcr-odm.php');
        }

        $container->registerForAutoconfiguration(GridInterface::class)
            ->addTag('sylius.grid')
        ;

        $container->registerAttributeForAutoconfiguration(
            AsFilter::class,
            static function (ChildDefinition $definition, AsFilter $attribute, \Reflector $reflector): void {
                // Helps to avoid issues with psalm
                if (!$reflector instanceof \ReflectionClass) {
                    return;
                }

                $definition->addTag(AsFilter::SERVICE_TAG, [
                    'type' => $attribute->type ?? $reflector->getName(),
                    'form_type' => $attribute->formType,
                    'template' => $attribute->template,
                ]);
            },
        );

        $container->registerForAutoconfiguration(ConfigurableFilterInterface::class)
            ->addTag(AsFilter::SERVICE_TAG)
        ;

        $container->registerForAutoconfiguration(DataProviderInterface::class)
            ->addTag('sylius.grid_data_provider')
        ;

        $container->registerAttributeForAutoconfiguration(
            AsGridFieldCallableService::class,
            static function (ChildDefinition $definition, AsGridFieldCallableService $attribute, \Reflector $reflector): void {
                $definition->addTag('sylius.grid_field_callable_service');
            },
        );
    }

    /**
     * @param array<int, mixed> $config
     */
    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        $configuration = new Configuration();

        $container->addObjectResource($configuration);

        return $configuration;
    }
}
