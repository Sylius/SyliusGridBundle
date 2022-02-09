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

namespace Sylius\Bundle\GridBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\GridBundle\Command\StubMakeGrid;
use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\RegisterMakersPass;
use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\RegisterStubCommandsPass;
use Sylius\Bundle\GridBundle\Maker\MakeGrid;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RegisterMakersPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_does_not_register_grid_maker_when_marker_is_not_available(): void
    {
        $this->compile();

        $this->assertContainerBuilderNotHasService('sylius.grid.maker');
    }

    /** @test */
    public function it_does_not_register_stub_commands_when_marker_is_registered(): void
    {
        $this->setParameter('kernel.bundles', [MakerBundle::class]);

        $this->compile();

        $this->assertContainerBuilderHasService('sylius.grid.maker', MakeGrid::class);
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $this->setParameter('kernel.bundles', []);

        $container->addCompilerPass(new RegisterMakersPass());
    }
}
