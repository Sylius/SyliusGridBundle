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

namespace DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\RegisterTimezoneParameterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RegisterTimezoneParameterPassTest extends AbstractCompilerPassTestCase
{
    #[Test]
    public function it_registers_null_timezone_parameter_if_it_does_not_exist(): void
    {
        $this->compile();

        $this->assertContainerBuilderHasParameter('sylius_grid.timezone', null);
    }

    #[Test]
    public function it_does_nothing_if_timezone_parameter_already_exists(): void
    {
        $this->container->setParameter('sylius_grid.timezone', 'UTC');

        $this->compile();

        $this->assertContainerBuilderHasParameter('sylius_grid.timezone', 'UTC');
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterTimezoneParameterPass());
    }
}
