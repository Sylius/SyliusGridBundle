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

use Sylius\Bundle\GridBundle\Command\DebugGridCommand;
use Sylius\Bundle\GridBundle\Maker\MakeGrid;
use Sylius\Bundle\GridBundle\Parser\OptionsParser;
use Sylius\Bundle\GridBundle\Provider\ServiceGridProvider;
use Sylius\Bundle\GridBundle\Registry\GridRegistry;
use Sylius\Component\Grid\Configuration\GridConfigurationExtender;
use Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandler;
use Sylius\Component\Grid\Configuration\GridConfigurationSortingHandler;
use Sylius\Component\Grid\Data\DataProvider;
use Sylius\Component\Grid\Data\DataSourceProvider;
use Sylius\Component\Grid\DataExtractor\PropertyAccessDataExtractor;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverter;
use Sylius\Component\Grid\Filtering\FiltersApplicator;
use Sylius\Component\Grid\Filtering\FiltersCriteriaResolver;
use Sylius\Component\Grid\Provider\ArrayGridProvider;
use Sylius\Component\Grid\Provider\ChainProvider;
use Sylius\Component\Grid\Sorting\Sorter;
use Sylius\Component\Grid\Validation\FieldValidator;
use Sylius\Component\Grid\Validation\SortingParametersValidator;
use Sylius\Component\Grid\View\GridViewFactory;
use Sylius\Component\Registry\ServiceRegistry;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $container->import('services/field_types.php');
    $container->import('services/filters.php');

    $services->defaults()
        ->public();

    $services->set('sylius.grid.data_extractor.property_access', PropertyAccessDataExtractor::class)
        ->args([service('property_accessor')]);

    $services->alias(PropertyAccessDataExtractor::class, 'sylius.grid.data_extractor.property_access');

    $services->set('sylius.grid.array_to_definition_converter', ArrayToDefinitionConverter::class)
        ->args([service('event_dispatcher')]);

    $services->alias('Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface', 'sylius.grid.array_to_definition_converter');

    $services->set('sylius.grid.grid_registry', GridRegistry::class)
        ->args([tagged_locator('sylius.grid', indexAttribute: 'name', defaultIndexMethod: 'getName')]);

    $services->alias('Sylius\Bundle\GridBundle\Registry\GridRegistryInterface', 'sylius.grid.grid_registry');

    $services->set('sylius.grid.configuration_extender', GridConfigurationExtender::class);

    $services->alias('Sylius\Component\Grid\Configuration\GridConfigurationExtenderInterface', 'sylius.grid.configuration_extender');

    $services->set('sylius.grid.configuration_removals_handler', GridConfigurationRemovalsHandler::class);

    $services->alias('Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandlerInterface', 'sylius.grid.configuration_removals_handler');

    $services->set('sylius.grid.configuration_sorting_handler', GridConfigurationSortingHandler::class);

    $services->alias('Sylius\Component\Grid\Configuration\GridConfigurationSortingHandlerInterface', 'sylius.grid.configuration_sorting_handler');

    $services->set('sylius.grid.array_grid_provider', ArrayGridProvider::class)
        ->args([
            service('sylius.grid.array_to_definition_converter'),
            '%sylius.grids_definitions%',
            service('sylius.grid.configuration_extender'),
            service('sylius.grid.configuration_removals_handler'),
            service('sylius.grid.configuration_sorting_handler'),
        ])
        ->tag('sylius.grid_provider', ['key' => 'array', 'priority' => -200]);

    $services->alias('Sylius\Component\Grid\Provider\ArrayGridProvider', 'sylius.grid.array_grid_provider');

    $services->set('sylius.grid.service_grid_provider', ServiceGridProvider::class)
        ->args([
            service('sylius.grid.array_to_definition_converter'),
            service('sylius.grid.grid_registry'),
            service('sylius.grid.configuration_extender'),
            service('sylius.grid.configuration_removals_handler'),
            service('sylius.grid.configuration_sorting_handler'),
        ])
        ->tag('sylius.grid_provider', ['key' => 'service', 'priority' => -100]);

    $services->alias('Sylius\Bundle\GridBundle\Provider\ServiceGridProvider', 'sylius.grid.service_grid_provider');

    $services->set('sylius.grid.chain_provider', ChainProvider::class)
        ->args([tagged_iterator('sylius.grid_provider')]);

    $services->alias('Sylius\Component\Grid\Provider\ChainProvider', 'sylius.grid.chain_provider');

    $services->alias('sylius.grid.provider', 'sylius.grid.chain_provider');

    $services->set('sylius.grid.view_factory', GridViewFactory::class)
        ->args([service('sylius.grid.data_provider')]);

    $services->alias('Sylius\Component\Grid\View\GridViewFactoryInterface', 'sylius.grid.view_factory');

    $services->set('sylius.grid.data_provider', DataProvider::class)
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

    $services->set('sylius.grid.filters_criteria_resolver', FiltersCriteriaResolver::class);

    $services->alias('Sylius\Component\Grid\Filtering\FiltersCriteriaResolverInterface', 'sylius.grid.filters_criteria_resolver');

    $services->set('sylius.grid.filters_applicator', FiltersApplicator::class)
        ->args([
            service('sylius.registry.grid_filter'),
            service('sylius.grid.filters_criteria_resolver'),
        ]);

    $services->alias('Sylius\Component\Grid\Filtering\FiltersApplicatorInterface', 'sylius.grid.filters_applicator');

    $services->set('sylius.grid.sorter.validator', SortingParametersValidator::class);

    $services->alias('Sylius\Component\Grid\Validation\SortingParametersValidatorInterface', 'sylius.grid.sorter.validator');

    $services->set('sylius.grid.field.validator', FieldValidator::class);

    $services->alias('Sylius\Component\Grid\Validation\FieldValidatorInterface', 'sylius.grid.field.validator');

    $services->set('sylius.grid.sorter', Sorter::class)
        ->args([
            service('sylius.grid.sorter.validator'),
            service('sylius.grid.field.validator'),
        ]);

    $services->alias('Sylius\Component\Grid\Sorting\SorterInterface', 'sylius.grid.sorter');

    $services->set('Sylius\Component\Grid\Data\DataSourceProviderInterface', DataSourceProvider::class)
        ->args([service('sylius.registry.grid_driver')]);

    $services->alias('sylius.grid.data_source_provider', 'Sylius\Component\Grid\Data\DataSourceProviderInterface');

    $services->set('sylius.registry.grid_driver', ServiceRegistry::class)
        ->args([
            'Sylius\Component\Grid\Data\DriverInterface',
            'grid driver',
        ]);

    $services->set('sylius.registry.grid_filter', ServiceRegistry::class)
        ->args([
            'Sylius\Component\Grid\Filtering\FilterInterface',
            'grid filter',
        ]);

    $services->set('sylius.registry.grid_field', ServiceRegistry::class)
        ->args([
            'Sylius\Component\Grid\FieldTypes\FieldTypeInterface',
            'grid field',
        ]);

    $services->set('sylius.grid.maker', MakeGrid::class)
        ->args([service('doctrine')->nullOnInvalid()])
        ->tag('maker.command');

    $services->alias('Sylius\Bundle\GridBundle\Maker\MakeGrid', 'sylius.grid.maker');

    $services->set('sylius.grid.console.command.grid_debug', DebugGridCommand::class)
        ->args([
            service('sylius.grid.provider'),
            tagged_locator('sylius.grid'),
            '%sylius.grids_definitions%',
        ])
        ->tag('console.command');

    $services->set('sylius.grid.options_parser', OptionsParser::class)
        ->private();

    $services->alias('Sylius\Bundle\GridBundle\Parser\OptionsParserInterface', 'sylius.grid.options_parser')
        ->private();
};
