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

namespace Sylius\Bundle\GridBundle\Tests\DependencyInjection\Compiler;

use App\Filter\Foo;
use App\Filter\NationalityFilter;
use App\Grid\Type\NationalityFilterType;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\RegisterFiltersPass;
use Sylius\Component\Registry\ServiceRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterFiltersPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_registers_a_grid_filter_and_form_type_for_every_service_filter_tag(): void
    {
        $tags = [
            ['type' => 'foo', 'form_type' => 'foo_type'],
            ['type' => 'bar', 'form_type' => 'bar_type'],
            ['type' => 'baz', 'form_type' => 'baz_type'],
        ];
        $filterService = $this->registerService($filterServiceId = 'app.grid_filter.foo', Foo::class);
        foreach ($tags as $tag) {
            $filterService->addTag('sylius.grid_filter', $tag);
        }
        $this->registerService($filterRegistryServiceId = 'sylius.registry.grid_filter', ServiceRegistry::class);
        $this->registerService(
            $filterFormTypeRegistryServiceId = 'sylius.form_registry.grid_filter',
            ServiceRegistry::class,
        );

        $this->compile();

        foreach ($tags as $tag) {
            // Filter
            $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
                $filterRegistryServiceId,
                'register',
                [
                    $tag['type'],
                    new Reference($filterServiceId),
                ],
            );

            // Form Type
            $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
                $filterFormTypeRegistryServiceId,
                'add',
                [
                    $tag['type'],
                    'default',
                    $tag['form_type'],
                ],
            );
        }
    }

    /**
     * @test
     */
    public function it_autoconfigures_a_grid_filter(): void
    {
        $this->registerService(NationalityFilter::class, NationalityFilter::class)
            ->addTag('sylius.grid_filter')
        ;

        $this->registerService($filterRegistryServiceId = 'sylius.registry.grid_filter', ServiceRegistry::class);
        $this->registerService(
            $filterFormTypeRegistryServiceId = 'sylius.form_registry.grid_filter',
            ServiceRegistry::class,
        );

        $this->compile();

        // Filter
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $filterRegistryServiceId,
            'register',
            [
                'nationality',
                new Reference(NationalityFilter::class),
            ],
        );

        // Form Type
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $filterFormTypeRegistryServiceId,
            'add',
            [
                'nationality',
                'default',
                NationalityFilterType::class,
            ],
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterFiltersPass());
    }
}
