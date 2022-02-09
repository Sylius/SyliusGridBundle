<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\GridBundle\DependencyInjection\Compiler;

use Sylius\Bundle\GridBundle\Command\StubMakeGrid;
use Sylius\Bundle\GridBundle\Maker\MakeGrid;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RegisterMakersPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$this->isMakerEnabled($container)) {
            return;
        }

        $container->register('sylius.grid.maker')
            ->setClass(MakeGrid::class)
            ->addArgument('doctrine')
            ->addTag('maker.command')
        ;
    }

    private function isMakerEnabled(ContainerBuilder $container): bool
    {
        if (!class_exists(MakerBundle::class)) {
            return false;
        }

        /** @var array $bundles */
        $bundles = $container->getParameter('kernel.bundles');

        return in_array(MakerBundle::class, $bundles, true);
    }
}
