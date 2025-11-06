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

use Sylius\Bundle\GridBundle\Form\Registry\FormTypeRegistry;
use Sylius\Bundle\GridBundle\Form\Type\Filter\BooleanFilterType;
use Sylius\Bundle\GridBundle\Form\Type\Filter\DateFilterType;
use Sylius\Bundle\GridBundle\Form\Type\Filter\EntityFilterType;
use Sylius\Bundle\GridBundle\Form\Type\Filter\EnumFilterType;
use Sylius\Bundle\GridBundle\Form\Type\Filter\ExistsFilterType;
use Sylius\Bundle\GridBundle\Form\Type\Filter\NumericRangeFilterType;
use Sylius\Bundle\GridBundle\Form\Type\Filter\SelectFilterType;
use Sylius\Bundle\GridBundle\Form\Type\Filter\StringFilterType;
use Sylius\Bundle\GridBundle\Storage\SessionFilterStorage;
use Sylius\Component\Grid\Filter\BooleanFilter;
use Sylius\Component\Grid\Filter\DateFilter;
use Sylius\Component\Grid\Filter\EntityFilter;
use Sylius\Component\Grid\Filter\ExistsFilter;
use Sylius\Component\Grid\Filter\NumericRangeFilter;
use Sylius\Component\Grid\Filter\SelectFilter;
use Sylius\Component\Grid\Filter\StringFilter;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius.grid.filter_storage.session', SessionFilterStorage::class)
        ->private()
        ->args([service('request_stack')]);

    $services->alias('sylius.grid.filter_storage', 'sylius.grid.filter_storage.session');

    $services->alias('Sylius\Bundle\GridBundle\Storage\FilterStorageInterface', 'sylius.grid.filter_storage');

    $services->set('sylius.form_registry.grid_filter', FormTypeRegistry::class)
        ->private();

    $services->set('sylius.grid_filter.string', StringFilter::class)
        ->tag('sylius.grid_filter', ['type' => 'string', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\StringFilterType']);

    $services->alias('Sylius\Component\Grid\Filter\StringFilter', 'sylius.grid_filter.string');

    $services->set(StringFilterType::class)
        ->tag('form.type');

    $services->alias('sylius.form.type.grid_filter.string', 'Sylius\Bundle\GridBundle\Form\Type\Filter\StringFilterType');

    $services->set('sylius.grid_filter.boolean', BooleanFilter::class)
        ->tag('sylius.grid_filter', ['type' => 'boolean', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\BooleanFilterType']);

    $services->alias('Sylius\Component\Grid\Filter\BooleanFilter', 'sylius.grid_filter.boolean');

    $services->set('sylius.form.type.grid_filter.boolean', BooleanFilterType::class)
        ->tag('form.type');

    $services->alias('Sylius\Bundle\GridBundle\Form\Type\Filter\BooleanFilterType', 'sylius.form.type.grid_filter.boolean');

    $services->set('sylius.grid_filter.date', DateFilter::class)
        ->tag('sylius.grid_filter', ['type' => 'date', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\DateFilterType']);

    $services->alias('Sylius\Component\Grid\Filter\DateFilter', 'sylius.grid_filter.date');

    $services->set('sylius.form.type.grid_filter.date', DateFilterType::class)
        ->tag('form.type');

    $services->alias('Sylius\Bundle\GridBundle\Form\Type\Filter\DateFilterType', 'sylius.form.type.grid_filter.date');

    $services->set('sylius.grid_filter.entity', EntityFilter::class)
        ->tag('sylius.grid_filter', ['type' => 'entity', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\EntityFilterType']);

    $services->alias('Sylius\Component\Grid\Filter\EntityFilter', 'sylius.grid_filter.entity');

    $services->set('sylius.form.type.grid_filter.entity', EntityFilterType::class)
        ->tag('form.type');

    $services->alias('Sylius\Bundle\GridBundle\Form\Type\Filter\EntityFilterType', 'sylius.form.type.grid_filter.entity');

    $services->set('sylius.grid_filter.exists', ExistsFilter::class)
        ->tag('sylius.grid_filter', ['type' => 'exists', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\ExistsFilterType']);

    $services->alias('Sylius\Component\Grid\Filter\ExistsFilter', 'sylius.grid_filter.exists');

    $services->set('sylius.form.type.grid_filter.exists', ExistsFilterType::class)
        ->tag('form.type');

    $services->alias('Sylius\Bundle\GridBundle\Form\Type\Filter\ExistsFilterType', 'sylius.form.type.grid_filter.exists');

    $services->set(NumericRangeFilter::class)
        ->tag('sylius.grid_filter', ['type' => 'numeric_range', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\NumericRangeFilterType']);

    $services->alias('sylius.grid_filter.numeric_range', 'Sylius\Component\Grid\Filter\NumericRangeFilter');

    $services->set('sylius.form.type.grid_filter.numeric_range', NumericRangeFilterType::class)
        ->tag('form.type');

    $services->alias('Sylius\Bundle\GridBundle\Form\Type\Filter\NumericRangeFilterType', 'sylius.form.type.grid_filter.numeric_range');

    $services->set('sylius.grid_filter.select', SelectFilter::class)
        ->tag('sylius.grid_filter', ['type' => 'select', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\SelectFilterType']);

    $services->alias('Sylius\Component\Grid\Filter\SelectFilter', 'sylius.grid_filter.select');

    $services->set('sylius.form.type.grid_filter.select', SelectFilterType::class)
        ->tag('form.type');

    $services->alias('Sylius\Bundle\GridBundle\Form\Type\Filter\SelectFilterType', 'sylius.form.type.grid_filter.select');

    $services->set('sylius.grid_filter.enum', SelectFilter::class)
        ->tag('sylius.grid_filter', ['type' => 'enum', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\EnumFilterType']);

    $services->set('sylius.form.type.grid_filter.enum', EnumFilterType::class)
        ->tag('form.type');

    $services->alias('Sylius\Bundle\GridBundle\Form\Type\Filter\EnumFilterType', 'sylius.form.type.grid_filter.enum');
};
