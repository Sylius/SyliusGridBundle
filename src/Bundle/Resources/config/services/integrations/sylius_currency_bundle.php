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

    $services->set('sylius.grid_filter.money', 'Sylius\Component\Grid\Filter\MoneyFilter')
        ->tag('sylius.grid_filter', ['type' => 'money', 'form_type' => 'Sylius\Bundle\GridBundle\Form\Type\Filter\MoneyFilterType']);

    $services->alias('Sylius\Component\Grid\Filter\MoneyFilter', 'sylius.grid_filter.money');

    $services->set('sylius.form.type.grid_filter.money', 'Sylius\Bundle\GridBundle\Form\Type\Filter\MoneyFilterType')
        ->tag('form.type');

    $services->alias('Sylius\Bundle\GridBundle\Form\Type\Filter\MoneyFilterType', 'sylius.form.type.grid_filter.money');
};
