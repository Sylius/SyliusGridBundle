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

use Sylius\Component\Grid\FieldTypes\CallableFieldType;
use Sylius\Component\Grid\FieldTypes\DatetimeFieldType;
use Sylius\Component\Grid\FieldTypes\EnumFieldType;
use Sylius\Component\Grid\FieldTypes\StringFieldType;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius.grid_field.callable', CallableFieldType::class)
        ->args([
            service('sylius.grid.data_extractor'),
            tagged_locator('sylius.grid_field_callable_service'),
        ])
        ->tag('sylius.grid_field', ['type' => 'callable']);

    $services->alias('Sylius\Component\Grid\FieldTypes\CallableFieldType', 'sylius.grid_field.callable');

    $services->set('sylius.grid_field.datetime', DatetimeFieldType::class)
        ->args([
            service('sylius.grid.data_extractor'),
            '%sylius_grid.timezone%',
        ])
        ->tag('sylius.grid_field', ['type' => 'datetime']);

    $services->alias('Sylius\Component\Grid\FieldTypes\DatetimeFieldType', 'sylius.grid_field.datetime');

    $services->set('sylius.grid_field.string', StringFieldType::class)
        ->args([service('sylius.grid.data_extractor')])
        ->tag('sylius.grid_field', ['type' => 'string']);

    $services->alias('Sylius\Component\Grid\FieldTypes\StringFieldType', 'sylius.grid_field.string');

    $services->set('sylius.grid_field.enum', EnumFieldType::class)
        ->args([
            service('sylius.grid.data_extractor'),
            service('translator')->nullOnInvalid(),
        ])
        ->tag('sylius.grid_field', ['type' => 'enum']);

    $services->alias('Sylius\Component\Grid\FieldTypes\EnumFieldType', 'sylius.grid_field.enum');
};
