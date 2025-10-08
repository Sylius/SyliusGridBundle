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

use Sylius\Bundle\CurrencyBundle\SyliusCurrencyBundle;
use Sylius\Bundle\GridBundle\Grid\GridInterface;
use Sylius\Bundle\GridBundle\SyliusGridBundle;
use Sylius\Component\Grid\Annotation\AsGridFieldCallableService;
use Sylius\Component\Grid\Attribute\AsFilter;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Filtering\ConfigurableFilterInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Twig\Environment;

final class SyliusGridExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');

        $container->setParameter('sylius.grid.templates.action', $config['templates']['action']);
        $container->setParameter('sylius.grid.templates.bulk_action', $config['templates']['bulk_action']);
        $container->setParameter('sylius.grid.templates.filter', $config['templates']['filter']);
        $container->setParameter('sylius.grids_definitions', $config['grids']);

        $container->setAlias('sylius.grid.renderer', 'sylius.grid.renderer.twig');
        $container->setAlias('sylius.grid.bulk_action_renderer', 'sylius.grid.bulk_action_renderer.twig');
        $container->setAlias('sylius.grid.data_extractor', 'sylius.grid.data_extractor.property_access');

        if ($container::willBeAvailable('twig/twig', Environment::class, ['symfony/twig-bundle'])) {
            $loader->load('services/integrations/twig.xml');
        }

        $this->loadPersistence($config['drivers'], $config['grids'], $loader, $container);

        if (\class_exists(SyliusCurrencyBundle::class)) {
            $loader->load('services/integrations/sylius_currency_bundle.xml');
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

    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        $configuration = new Configuration();

        $container->addObjectResource($configuration);

        return $configuration;
    }

    private function loadPersistence(array $drivers, array $grids, LoaderInterface $loader, ContainerBuilder $container): void
    {
        $availableDrivers = $this->getAvailableDrivers($container);

        // Enable all available drivers if there is no configured drivers
        $drivers = [] !== $drivers ? $drivers : $availableDrivers;

        $gridDrivers = $this->resolveDriversUsedInGrids($grids);

        $this->assertDriversValid($drivers, $availableDrivers, $gridDrivers);

        foreach ($drivers as $enabledDriver) {
            if ($enabledDriver === SyliusGridBundle::DRIVER_DOCTRINE_PHPCR_ODM) {
                @trigger_error(sprintf(
                    'The "%s" driver is deprecated in Sylius 1.3. Doctrine PHPCR will no longer be supported in Sylius 2.0.',
                    SyliusGridBundle::DRIVER_DOCTRINE_PHPCR_ODM,
                ), \E_USER_DEPRECATED);
            }

            $loader->load(sprintf('services/integrations/%s.xml', $enabledDriver));
        }
    }

    /**
     * @param array<string, array{driver?: array{name: string}}> $grids
     *
     * @return array<string, string>
     */
    private function resolveDriversUsedInGrids(array $grids): array
    {
        $gridDrivers = array_map(function (array $grid): string|false {
            return $grid['driver']['name'] ?? false;
        }, $grids);

        // Remove grid with disabled driver
        return array_filter($gridDrivers, function (string|false $driver): bool {
            return false !== $driver;
        });
    }

    private function getAvailableDrivers(ContainerBuilder $container): array
    {
        $availableDrivers = [];

        if ($container::willBeAvailable(SyliusGridBundle::DRIVER_DOCTRINE_ORM, \Doctrine\ORM\EntityManagerInterface::class, ['doctrine/doctrine-bundle'])) {
            $availableDrivers[] = SyliusGridBundle::DRIVER_DOCTRINE_ORM;
        }

        if ($container::willBeAvailable(SyliusGridBundle::DRIVER_DOCTRINE_PHPCR_ODM, \Doctrine\ODM\PHPCR\Document\Resource::class, ['doctrine/doctrine-bundle'])) {
            $availableDrivers[] = SyliusGridBundle::DRIVER_DOCTRINE_PHPCR_ODM;
        }

        return $availableDrivers;
    }

    /**
     * @param string[] $configuredDrivers
     * @param string[] $availableDrivers
     * @param array<string, string> $gridDrivers
     */
    private function assertDriversValid(array $configuredDrivers, array $availableDrivers, array $gridDrivers): void
    {
        foreach ($configuredDrivers as $driver) {
            if (!in_array($driver, $availableDrivers, true)) {
                throw new InvalidArgumentException(sprintf(
                    'Driver "%s" is configured, but this driver is not available. Try running "composer require %s"',
                    $driver,
                    $driver,
                ));
            }
        }

        foreach ($gridDrivers as $grid => $driver) {
            if (!in_array($driver, $availableDrivers, true)) {
                throw new InvalidArgumentException(sprintf(
                    'Grid "%s" uses drivers "%s", but this driver is not available. Try running "composer require %s"',
                    $grid,
                    $driver,
                    $driver,
                ));
            }

            if (!in_array($driver, $configuredDrivers, true)) {
                throw new InvalidArgumentException(sprintf(
                    'Grid "%s" uses drivers "%s", but this driver is not enabled. Try adding "%s" in sylius_grid.drivers option',
                    $grid,
                    $driver,
                    $driver,
                ));
            }
        }
    }
}
