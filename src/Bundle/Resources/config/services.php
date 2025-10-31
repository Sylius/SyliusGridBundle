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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $container->import('services/field_types.php');
    $container->import('services/filters.php');

    $services->defaults()
        ->public();

    $services->set('sylius.grid.data_extractor.property_access', 'Sylius\Component\Grid\DataExtractor\PropertyAccessDataExtractor')
        ->args([service('property_accessor')]);

    $services->alias('Sylius\Component\Grid\DataExtractor\PropertyAccessDataExtractor', 'sylius.grid.data_extractor.property_access');

    $services->set('sylius.grid.array_to_definition_converter', 'Sylius\Component\Grid\Definition\ArrayToDefinitionConverter')
        ->args([service('event_dispatcher')]);

    $services->alias('Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface', 'sylius.grid.array_to_definition_converter');

    $services->set('sylius.grid.grid_registry', 'Sylius\Bundle\GridBundle\Registry\GridRegistry')
        ->args([tagged_locator('sylius.grid', indexAttribute: 'name', defaultIndexMethod: 'getName')]);

    $services->alias('Sylius\Bundle\GridBundle\Registry\GridRegistryInterface', 'sylius.grid.grid_registry');

    $services->set('sylius.grid.configuration_extender', 'Sylius\Component\Grid\Configuration\GridConfigurationExtender');

    $services->alias('Sylius\Component\Grid\Configuration\GridConfigurationExtenderInterface', 'sylius.grid.configuration_extender');

    $services->set('sylius.grid.configuration_removals_handler', 'Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandler');

    $services->alias('Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandlerInterface', 'sylius.grid.configuration_removals_handler');

    $services->set('sylius.grid.configuration_sorting_handler', 'Sylius\Component\Grid\Configuration\GridConfigurationSortingHandler');

    $services->alias('Sylius\Component\Grid\Configuration\GridConfigurationSortingHandlerInterface', 'sylius.grid.configuration_sorting_handler');

    $services->set('sylius.grid.array_grid_provider', 'Sylius\Component\Grid\Provider\ArrayGridProvider')
        ->args([
            service('sylius.grid.array_to_definition_converter'),
            '%sylius.grids_definitions%',
            service('sylius.grid.configuration_extender'),
            service('sylius.grid.configuration_removals_handler'),
            service('sylius.grid.configuration_sorting_handler'),
        ])
        ->tag('sylius.grid_provider', ['key' => 'array', 'priority' => -200]);

    $services->alias('Sylius\Component\Grid\Provider\ArrayGridProvider', 'sylius.grid.array_grid_provider');

    $services->set('sylius.grid.service_grid_provider', 'Sylius\Bundle\GridBundle\Provider\ServiceGridProvider')
        ->args([
            service('sylius.grid.array_to_definition_converter'),
            service('sylius.grid.grid_registry'),
            service('sylius.grid.configuration_extender'),
            service('sylius.grid.configuration_removals_handler'),
            service('sylius.grid.configuration_sorting_handler'),
        ])
        ->tag('sylius.grid_provider', ['key' => 'service', 'priority' => -100]);

    $services->alias('Sylius\Bundle\GridBundle\Provider\ServiceGridProvider', 'sylius.grid.service_grid_provider');

    $services->set('sylius.grid.chain_provider', 'Sylius\Component\Grid\Provider\ChainProvider')
        ->args([tagged_iterator('sylius.grid_provider')]);

    $services->alias('Sylius\Component\Grid\Provider\ChainProvider', 'sylius.grid.chain_provider');

    $services->alias('sylius.grid.provider', 'sylius.grid.chain_provider');

    $services->set('sylius.grid.view_factory', 'Sylius\Component\Grid\View\GridViewFactory')
        ->args([service('sylius.grid.data_provider')]);

    $services->alias('Sylius\Component\Grid\View\GridViewFactoryInterface', 'sylius.grid.view_factory');

    $services->set('sylius.grid.data_provider', 'Sylius\Component\Grid\Data\DataProvider')
        ->args([
            service('sylius.grid.data_source_provider'),
            service('sylius.grid.filters_applicator'),
            service('sylius.grid.sorter'),
        ]);

    $services->alias('Sylius\Component\Grid\Data\DataProviderInterface', 'sylius.grid.data_provider');

    $services->set('Sylius\Component\Grid\Data\Provider')
        ->decorate('sylius.grid.data_provider')
        ->args([
            tagged_locator('sylius.grid_data_provider'),
            service('.inner'),
        ]);

    $services->set('sylius.grid.filters_criteria_resolver', 'Sylius\Component\Grid\Filtering\FiltersCriteriaResolver');

    $services->alias('Sylius\Component\Grid\Filtering\FiltersCriteriaResolverInterface', 'sylius.grid.filters_criteria_resolver');

    $services->set('sylius.grid.filters_applicator', 'Sylius\Component\Grid\Filtering\FiltersApplicator')
        ->args([
            service('sylius.registry.grid_filter'),
            service('sylius.grid.filters_criteria_resolver'),
        ]);

    $services->alias('Sylius\Component\Grid\Filtering\FiltersApplicatorInterface', 'sylius.grid.filters_applicator');

    $services->set('sylius.grid.sorter.validator', 'Sylius\Component\Grid\Validation\SortingParametersValidator');

    $services->alias('Sylius\Component\Grid\Validation\SortingParametersValidatorInterface', 'sylius.grid.sorter.validator');

    $services->set('sylius.grid.field.validator', 'Sylius\Component\Grid\Validation\FieldValidator');

    $services->alias('Sylius\Component\Grid\Validation\FieldValidatorInterface', 'sylius.grid.field.validator');

    $services->set('sylius.grid.sorter', 'Sylius\Component\Grid\Sorting\Sorter')
        ->args([
            service('sylius.grid.sorter.validator'),
            service('sylius.grid.field.validator'),
        ]);

    $services->alias('Sylius\Component\Grid\Sorting\SorterInterface', 'sylius.grid.sorter');

    $services->set('Sylius\Component\Grid\Data\DataSourceProviderInterface', 'Sylius\Component\Grid\Data\DataSourceProvider')
        ->args([service('sylius.registry.grid_driver')]);

    $services->alias('sylius.grid.data_source_provider', 'Sylius\Component\Grid\Data\DataSourceProviderInterface');

    $services->set('sylius.registry.grid_driver', 'Sylius\Component\Registry\ServiceRegistry')
        ->args([
            'Sylius\Component\Grid\Data\DriverInterface',
            'grid driver',
        ]);

    $services->set('sylius.registry.grid_filter', 'Sylius\Component\Registry\ServiceRegistry')
        ->args([
            'Sylius\Component\Grid\Filtering\FilterInterface',
            'grid filter',
        ]);

    $services->set('sylius.registry.grid_field', 'Sylius\Component\Registry\ServiceRegistry')
        ->args([
            'Sylius\Component\Grid\FieldTypes\FieldTypeInterface',
            'grid field',
        ]);

    $services->set('sylius.grid.maker', 'Sylius\Bundle\GridBundle\Maker\MakeGrid')
        ->args([service('doctrine')->nullOnInvalid()])
        ->tag('maker.command');

    $services->alias('Sylius\Bundle\GridBundle\Maker\MakeGrid', 'sylius.grid.maker');

    $services->set('sylius.grid.console.command.grid_debug', 'Sylius\Bundle\GridBundle\Command\DebugGridCommand')
        ->args([
            service('sylius.grid.provider'),
            tagged_locator('sylius.grid'),
            '%sylius.grids_definitions%',
        ])
        ->tag('console.command');

    $services->set('sylius.grid.options_parser', 'Sylius\Bundle\GridBundle\Parser\OptionsParser')
        ->private();

    $services->alias('Sylius\Bundle\GridBundle\Parser\OptionsParserInterface', 'sylius.grid.options_parser')
        ->private();
};
