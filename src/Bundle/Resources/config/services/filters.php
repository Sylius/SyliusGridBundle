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

    $services->defaults()
        ->public();

    $services->set('sylius.grid.filter_storage.session', 'Sylius\Bundle\GridBundle\Storage\SessionFilterStorage')
        ->private()
        ->args([service('request_stack')]);

    $services->alias('sylius.grid.filter_storage', 'sylius.grid.filter_storage.session');

    $services->alias('Sylius\Bundle\GridBundle\Storage\FilterStorageInterface', 'sylius.grid.filter_storage');

    $services->set('sylius.form_registry.grid_filter', 'Sylius\Bundle\GridBundle\Form\Registry\FormTypeRegistry')
        ->private();

    $services->set('sylius.grid_filter.string', 'Sylius\Component\Grid\Filter\StringFilter')
        ->tag('sylius.grid_filter', ['type' => 'string', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\StringFilterType']);

    $services->alias('Sylius\Component\Grid\Filter\StringFilter', 'sylius.grid_filter.string');

    $services->set('Sylius\Bundle\GridBundle\Form\Type\Filter\StringFilterType')
        ->tag('form.type');

    $services->alias('sylius.form.type.grid_filter.string', 'Sylius\Bundle\GridBundle\Form\Type\Filter\StringFilterType');

    $services->set('sylius.grid_filter.boolean', 'Sylius\Component\Grid\Filter\BooleanFilter')
        ->tag('sylius.grid_filter', ['type' => 'boolean', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\BooleanFilterType']);

    $services->alias('Sylius\Component\Grid\Filter\BooleanFilter', 'sylius.grid_filter.boolean');

    $services->set('sylius.form.type.grid_filter.boolean', 'Sylius\Bundle\GridBundle\Form\Type\Filter\BooleanFilterType')
        ->tag('form.type');

    $services->alias('Sylius\Bundle\GridBundle\Form\Type\Filter\BooleanFilterType', 'sylius.form.type.grid_filter.boolean');

    $services->set('sylius.grid_filter.date', 'Sylius\Component\Grid\Filter\DateFilter')
        ->tag('sylius.grid_filter', ['type' => 'date', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\DateFilterType']);

    $services->alias('Sylius\Component\Grid\Filter\DateFilter', 'sylius.grid_filter.date');

    $services->set('sylius.form.type.grid_filter.date', 'Sylius\Bundle\GridBundle\Form\Type\Filter\DateFilterType')
        ->tag('form.type');

    $services->alias('Sylius\Bundle\GridBundle\Form\Type\Filter\DateFilterType', 'sylius.form.type.grid_filter.date');

    $services->set('sylius.grid_filter.entity', 'Sylius\Component\Grid\Filter\EntityFilter')
        ->tag('sylius.grid_filter', ['type' => 'entity', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\EntityFilterType']);

    $services->alias('Sylius\Component\Grid\Filter\EntityFilter', 'sylius.grid_filter.entity');

    $services->set('sylius.form.type.grid_filter.entity', 'Sylius\Bundle\GridBundle\Form\Type\Filter\EntityFilterType')
        ->tag('form.type');

    $services->alias('Sylius\Bundle\GridBundle\Form\Type\Filter\EntityFilterType', 'sylius.form.type.grid_filter.entity');

    $services->set('sylius.grid_filter.exists', 'Sylius\Component\Grid\Filter\ExistsFilter')
        ->tag('sylius.grid_filter', ['type' => 'exists', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\ExistsFilterType']);

    $services->alias('Sylius\Component\Grid\Filter\ExistsFilter', 'sylius.grid_filter.exists');

    $services->set('sylius.form.type.grid_filter.exists', 'Sylius\Bundle\GridBundle\Form\Type\Filter\ExistsFilterType')
        ->tag('form.type');

    $services->alias('Sylius\Bundle\GridBundle\Form\Type\Filter\ExistsFilterType', 'sylius.form.type.grid_filter.exists');

    $services->set('Sylius\Component\Grid\Filter\NumericRangeFilter')
        ->tag('sylius.grid_filter', ['type' => 'numeric_range', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\NumericRangeFilterType']);

    $services->alias('sylius.grid_filter.numeric_range', 'Sylius\Component\Grid\Filter\NumericRangeFilter');

    $services->set('sylius.form.type.grid_filter.numeric_range', 'Sylius\Bundle\GridBundle\Form\Type\Filter\NumericRangeFilterType')
        ->tag('form.type');

    $services->alias('Sylius\Bundle\GridBundle\Form\Type\Filter\NumericRangeFilterType', 'sylius.form.type.grid_filter.numeric_range');

    $services->set('sylius.grid_filter.select', 'Sylius\Component\Grid\Filter\SelectFilter')
        ->tag('sylius.grid_filter', ['type' => 'select', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\SelectFilterType']);

    $services->alias('Sylius\Component\Grid\Filter\SelectFilter', 'sylius.grid_filter.select');

    $services->set('sylius.form.type.grid_filter.select', 'Sylius\Bundle\GridBundle\Form\Type\Filter\SelectFilterType')
        ->tag('form.type');

    $services->alias('Sylius\Bundle\GridBundle\Form\Type\Filter\SelectFilterType', 'sylius.form.type.grid_filter.select');

    $services->set('sylius.grid_filter.enum', 'Sylius\Component\Grid\Filter\SelectFilter')
        ->tag('sylius.grid_filter', ['type' => 'enum', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\EnumFilterType']);

    $services->set('sylius.form.type.grid_filter.enum', 'Sylius\Bundle\GridBundle\Form\Type\Filter\EnumFilterType')
        ->tag('form.type');

    $services->alias('Sylius\Bundle\GridBundle\Form\Type\Filter\EnumFilterType', 'sylius.form.type.grid_filter.enum');
};
