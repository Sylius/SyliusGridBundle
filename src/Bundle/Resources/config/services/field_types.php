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

    $services->set('sylius.grid_field.callable', 'Sylius\Component\Grid\FieldTypes\CallableFieldType')
        ->args([
            service('sylius.grid.data_extractor'),
            tagged_locator('sylius.grid_field_callable_service'),
        ])
        ->tag('sylius.grid_field', ['type' => 'callable']);

    $services->alias('Sylius\Component\Grid\FieldTypes\CallableFieldType', 'sylius.grid_field.callable');

    $services->set('sylius.grid_field.datetime', 'Sylius\Component\Grid\FieldTypes\DatetimeFieldType')
        ->args([
            service('sylius.grid.data_extractor'),
            '%sylius_grid.timezone%',
        ])
        ->tag('sylius.grid_field', ['type' => \datetime::class]);

    $services->alias('Sylius\Component\Grid\FieldTypes\DatetimeFieldType', 'sylius.grid_field.datetime');

    $services->set('sylius.grid_field.string', 'Sylius\Component\Grid\FieldTypes\StringFieldType')
        ->args([service('sylius.grid.data_extractor')])
        ->tag('sylius.grid_field', ['type' => 'string']);

    $services->alias('Sylius\Component\Grid\FieldTypes\StringFieldType', 'sylius.grid_field.string');

    $services->set('sylius.grid_field.enum', 'Sylius\Component\Grid\FieldTypes\EnumFieldType')
        ->args([
            service('sylius.grid.data_extractor'),
            service('translator')->nullOnInvalid(),
        ])
        ->tag('sylius.grid_field', ['type' => 'enum']);

    $services->alias('Sylius\Component\Grid\FieldTypes\EnumFieldType', 'sylius.grid_field.enum');
};
