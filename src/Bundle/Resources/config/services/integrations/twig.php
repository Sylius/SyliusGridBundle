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
    $container->import('twig/**');

    $services->defaults()
        ->public();

    $services->set('sylius.grid.renderer.twig', 'Sylius\Bundle\GridBundle\Renderer\TwigGridRenderer')
        ->args([
            service('twig'),
            service('sylius.registry.grid_field'),
            service('form.factory'),
            service('sylius.form_registry.grid_filter'),
            '@SyliusGrid/_grid.html.twig',
            '%sylius.grid.templates.action%',
            '%sylius.grid.templates.filter%',
            service('sylius.grid.options_parser'),
        ]);

    $services->alias('Sylius\Bundle\GridBundle\Renderer\TwigGridRenderer', 'sylius.grid.renderer.twig');

    $services->set('sylius.grid.bulk_action_renderer.twig', 'Sylius\Bundle\GridBundle\Renderer\TwigBulkActionGridRenderer')
        ->args([
            service('twig'),
            '%sylius.grid.templates.bulk_action%',
        ]);

    $services->alias('Sylius\Bundle\GridBundle\Renderer\TwigBulkActionGridRenderer', 'sylius.grid.bulk_action_renderer.twig');

    $services->set('sylius.twig.extension.grid', 'Sylius\Bundle\GridBundle\Twig\GridExtension')
        ->private()
        ->args([service('sylius.templating.helper.grid')])
        ->tag('twig.extension');

    $services->alias('Sylius\Bundle\GridBundle\Twig\GridExtension', 'sylius.twig.extension.grid')
        ->private();

    $services->set('sylius.twig.extension.bulk_action_grid', 'Sylius\Bundle\GridBundle\Twig\BulkActionGridExtension')
        ->private()
        ->args([service('sylius.templating.helper.bulk_action_grid')])
        ->tag('twig.extension');

    $services->alias('Sylius\Bundle\GridBundle\Twig\BulkActionGridExtension', 'sylius.twig.extension.bulk_action_grid')
        ->private();

    $services->set('sylius.grid_field.twig', 'Sylius\Bundle\GridBundle\FieldTypes\TwigFieldType')
        ->args([
            service('sylius.grid.data_extractor'),
            service('twig'),
        ])
        ->tag('sylius.grid_field', ['type' => 'twig']);

    $services->alias('Sylius\Bundle\GridBundle\FieldTypes\TwigFieldType', 'sylius.grid_field.twig');
};
