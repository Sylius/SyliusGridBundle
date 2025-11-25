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

use App\Driver\Foo;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\ValidateConfiguredGridDriversPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ValidateConfiguredGridDriversPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_does_nothing_when_no_grids_definitions_parameter_exists(): void
    {
        $this->compile();

        $this->expectNotToPerformAssertions();
    }

    /**
     * @test
     */
    public function it_validates_grid_drivers_successfully_when_all_drivers_are_registered(): void
    {
        $this->registerService('app.grid_driver.foo', Foo::class)
            ->addTag('sylius.grid_driver', ['alias' => 'foo']);

        $this->registerService('app.grid_driver.bar', Foo::class)
            ->addTag('sylius.grid_driver', ['alias' => 'bar']);

        $this->container->setParameter('sylius.grids_definitions', [
            'app_book' => [
                'driver' => [
                    'name' => 'foo',
                    'options' => [],
                ],
            ],
            'app_author' => [
                'driver' => [
                    'name' => 'bar',
                    'options' => [],
                ],
            ],
        ]);

        $this->compile();

        $this->expectNotToPerformAssertions();
    }

    /**
     * @test
     */
    public function it_throws_exception_when_grid_uses_non_existent_driver(): void
    {
        $this->registerService('app.grid_driver.foo', Foo::class)
            ->addTag('sylius.grid_driver', ['alias' => 'foo']);

        $this->container->setParameter('sylius.grids_definitions', [
            'app_book' => [
                'driver' => [
                    'name' => 'non_existent_driver',
                    'options' => [],
                ],
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Grid "app_book" uses driver "non_existent_driver" which is not registered. Available drivers are: foo');

        $this->compile();
    }

    /**
     * @test
     */
    public function it_provides_helpful_error_message_with_multiple_available_drivers(): void
    {
        $this->registerService('app.grid_driver.foo', Foo::class)
            ->addTag('sylius.grid_driver', ['alias' => 'foo']);

        $this->registerService('app.grid_driver.bar', Foo::class)
            ->addTag('sylius.grid_driver', ['alias' => 'bar']);

        $this->registerService('app.grid_driver.baz', Foo::class)
            ->addTag('sylius.grid_driver', ['alias' => 'baz']);

        $this->container->setParameter('sylius.grids_definitions', [
            'app_book' => [
                'driver' => [
                    'name' => 'invalid_driver',
                    'options' => [],
                ],
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Grid "app_book" uses driver "invalid_driver" which is not registered. Available drivers are: foo, bar, baz');

        $this->compile();
    }

    /**
     * @test
     */
    public function it_provides_helpful_error_message_when_no_drivers_are_available(): void
    {
        $this->container->setParameter('sylius.grids_definitions', [
            'app_book' => [
                'driver' => [
                    'name' => 'some_driver',
                    'options' => [],
                ],
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Grid "app_book" uses driver "some_driver" which is not registered. Available drivers are: none');

        $this->compile();
    }

    /**
     * @test
     */
    public function it_skips_grids_with_null_driver_name(): void
    {
        $this->container->setParameter('sylius.grids_definitions', [
            'app_book' => [
                'driver' => [
                    'options' => [],
                ],
            ],
        ]);

        $this->compile();

        $this->expectNotToPerformAssertions();
    }

    /**
     * @test
     */
    public function it_skips_grids_with_false_driver_name(): void
    {
        $this->container->setParameter('sylius.grids_definitions', [
            'app_book' => [
                'driver' => [
                    'name' => false,
                    'options' => [],
                ],
            ],
        ]);

        $this->compile();

        $this->expectNotToPerformAssertions();
    }

    /**
     * @test
     */
    public function it_validates_multiple_grids_and_reports_first_invalid_one(): void
    {
        $this->registerService('app.grid_driver.foo', Foo::class)
            ->addTag('sylius.grid_driver', ['alias' => 'foo']);

        $this->container->setParameter('sylius.grids_definitions', [
            'app_book' => [
                'driver' => [
                    'name' => 'foo',
                    'options' => [],
                ],
            ],
            'app_author' => [
                'driver' => [
                    'name' => 'invalid_driver',
                    'options' => [],
                ],
            ],
            'app_publisher' => [
                'driver' => [
                    'name' => 'another_invalid_driver',
                    'options' => [],
                ],
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Grid "app_author" uses driver "invalid_driver" which is not registered. Available drivers are: foo');

        $this->compile();
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ValidateConfiguredGridDriversPass());
    }
}
