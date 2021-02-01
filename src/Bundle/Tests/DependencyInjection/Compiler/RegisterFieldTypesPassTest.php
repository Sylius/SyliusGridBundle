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

use App\FieldTypes\FooType;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\RegisterFieldTypesPass;
use Sylius\Component\Registry\ServiceRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterFieldTypesPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_registers_a_grid_field_for_every_field_service_tag(): void
    {
        $tags = [
            ['type' => 'foo'],
            ['type' => 'bar'],
            ['type' => 'baz'],
        ];
        $fieldService = $this->registerService($fieldServiceId = 'app.grid_field.foo', FooType::class);
        foreach ($tags as $tag) {
            $fieldService->addTag('sylius.grid_field', $tag);
        }
        $this->registerService($fieldRegistryServiceId = 'sylius.registry.grid_field', ServiceRegistry::class);

        $this->compile();

        foreach ($tags as $tag) {
            $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
                $fieldRegistryServiceId,
                'register',
                [
                    $tag['type'],
                    new Reference($fieldServiceId),
                ]
            );
        }
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterFieldTypesPass());
    }
}
