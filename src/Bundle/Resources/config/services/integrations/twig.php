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

use Sylius\Bundle\GridBundle\FieldTypes\TwigFieldType;
use Sylius\Bundle\GridBundle\Renderer\TwigBulkActionGridRenderer;
use Sylius\Bundle\GridBundle\Renderer\TwigGridRenderer;
use Sylius\Bundle\GridBundle\Twig\BulkActionGridExtension;
use Sylius\Bundle\GridBundle\Twig\GridExtension;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $container->import('twig/**');

    $services->defaults()
        ->public();

    $services->set('sylius.grid.renderer.twig', TwigGridRenderer::class)
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

    $services->alias(TwigGridRenderer::class, 'sylius.grid.renderer.twig');

    $services->set('sylius.grid.bulk_action_renderer.twig', TwigBulkActionGridRenderer::class)
        ->args([
            service('twig'),
            '%sylius.grid.templates.bulk_action%',
        ]);

    $services->alias(TwigBulkActionGridRenderer::class, 'sylius.grid.bulk_action_renderer.twig');

    $services->set('sylius.twig.extension.grid', GridExtension::class)
        ->private()
        ->args([service('sylius.templating.helper.grid')])
        ->tag('twig.extension');

    $services->alias(GridExtension::class, 'sylius.twig.extension.grid')
        ->private();

    $services->set('sylius.twig.extension.bulk_action_grid', BulkActionGridExtension::class)
        ->private()
        ->args([service('sylius.templating.helper.bulk_action_grid')])
        ->tag('twig.extension');

    $services->alias(BulkActionGridExtension::class, 'sylius.twig.extension.bulk_action_grid')
        ->private();

    $services->set('sylius.grid_field.twig', TwigFieldType::class)
        ->args([
            service('sylius.grid.data_extractor'),
            service('twig'),
        ])
        ->tag('sylius.grid_field', ['type' => 'twig']);

    $services->alias(TwigFieldType::class, 'sylius.grid_field.twig');
};
