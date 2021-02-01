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

use App\Driver\Foo;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\RegisterDriversPass;
use Sylius\Component\Registry\ServiceRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterDriversPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_registers_a_grid_driver_for_every_driver_service_tag(): void
    {
        $tags = [
            ['alias' => 'foo'],
            ['alias' => 'bar'],
            ['alias' => 'baz'],
        ];
        $driverService = $this->registerService($driverServiceId = 'app.grid_driver.foo', Foo::class);
        foreach ($tags as $tag) {
            $driverService->addTag('sylius.grid_driver', $tag);
        }
        $this->registerService($driverRegistryServiceId = 'sylius.registry.grid_driver', ServiceRegistry::class);

        $this->compile();

        foreach ($tags as $tag) {
            $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
                $driverRegistryServiceId,
                'register',
                [
                    $tag['alias'],
                    new Reference($driverServiceId),
                ]
            );
        }
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterDriversPass());
    }
}
