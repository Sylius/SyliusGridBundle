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

use Sylius\Bundle\GridBundle\Templating\Helper\BulkActionGridHelper;
use Sylius\Bundle\GridBundle\Templating\Helper\GridHelper;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius.templating.helper.grid', GridHelper::class)
        ->lazy()
        ->args([service('sylius.grid.renderer')]);

    $services->alias('Sylius\Bundle\GridBundle\Templating\Helper\GridHelper', 'sylius.templating.helper.grid');

    $services->set('sylius.templating.helper.bulk_action_grid', BulkActionGridHelper::class)
        ->lazy()
        ->args([service('sylius.grid.bulk_action_renderer')]);

    $services->alias('Sylius\Bundle\GridBundle\Templating\Helper\BulkActionGridHelper', 'sylius.templating.helper.bulk_action_grid');
};
