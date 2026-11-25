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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\RegisterMakerCommandAliasesPass;
use Sylius\Bundle\GridBundle\Maker\MakeGrid;
use Symfony\Bundle\MakerBundle\Command\MakerCommand;
use Symfony\Bundle\MakerBundle\Maker\MakeController;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterMakerCommandAliasesPassTest extends AbstractCompilerPassTestCase
{
    #[Test]
    public function it_adds_command_aliases_of_alias_aware_makers(): void
    {
        $this->registerMakerCommand('maker.auto_command.sylius_make_grid', 'sylius.grid.maker', MakeGrid::class, 'sylius:make:grid');

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'maker.auto_command.sylius_make_grid',
            'console.command',
            ['command' => 'make:grid'],
        );
    }

    #[Test]
    public function it_does_not_add_command_aliases_twice(): void
    {
        $this->registerMakerCommand('maker.auto_command.sylius_make_grid', 'sylius.grid.maker', MakeGrid::class, 'sylius:make:grid')
            ->addTag('console.command', ['command' => 'make:grid'])
        ;

        $this->compile();

        $commands = array_column(
            $this->container->getDefinition('maker.auto_command.sylius_make_grid')->getTag('console.command'),
            'command',
        );

        $this->assertSame(['sylius:make:grid', 'make:grid'], $commands);
    }

    #[Test]
    public function it_does_not_add_command_aliases_to_makers_not_aware_of_aliases(): void
    {
        $this->registerMakerCommand('maker.auto_command.make_controller', 'maker.maker.make_controller', MakeController::class, 'make:controller');

        $this->compile();

        $this->assertCount(1, $this->container->getDefinition('maker.auto_command.make_controller')->getTag('console.command'));
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->register('maker.auto_command.abstract', MakerCommand::class)->setAbstract(true)->setArguments([null]);

        $container->addCompilerPass(new RegisterMakerCommandAliasesPass());
    }

    private function registerMakerCommand(string $commandId, string $makerId, string $makerClass, string $commandName): ChildDefinition
    {
        $this->registerService($makerId, $makerClass);

        $commandDefinition = (new ChildDefinition('maker.auto_command.abstract'))
            ->setClass(MakerCommand::class)
            ->replaceArgument(0, new Reference($makerId))
            ->addTag('console.command', ['command' => $commandName])
        ;
        $this->setDefinition($commandId, $commandDefinition);

        return $commandDefinition;
    }
}
