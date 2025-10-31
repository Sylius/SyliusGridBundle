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

    $services->set('sylius.grid_driver.doctrine.orm', 'Sylius\Bundle\GridBundle\Doctrine\ORM\Driver')
        ->args([service('doctrine')->nullOnInvalid()])
        ->tag('sylius.grid_driver', ['alias' => 'doctrine/orm']);

    $services->alias('Sylius\Bundle\GridBundle\Doctrine\ORM\Driver', 'sylius.grid_driver.doctrine.orm');

    $services->set('sylius.grid_driver.doctrine.dbal', 'Sylius\Bundle\GridBundle\Doctrine\DBAL\Driver')
        ->args([service('doctrine.dbal.default_connection')])
        ->tag('sylius.grid_driver', ['alias' => 'doctrine/dbal']);

    $services->alias('Sylius\Bundle\GridBundle\Doctrine\DBAL\Driver', 'sylius.grid_driver.doctrine.dbal');
};
