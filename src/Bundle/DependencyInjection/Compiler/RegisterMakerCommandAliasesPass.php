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

namespace Sylius\Bundle\GridBundle\DependencyInjection\Compiler;

use Sylius\Bundle\GridBundle\Maker\CommandAliasesAwareInterface;
use Symfony\Bundle\MakerBundle\Command\MakerCommand;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Must run after MakerBundle's MakeCommandRegistrationPass (TYPE_BEFORE_OPTIMIZATION, priority 10),
 * which creates the maker command definitions, and before AddConsoleCommandPass, which reads their tags.
 */
final class RegisterMakerCommandAliasesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('console.command') as $id => $tags) {
            $definition = $container->getDefinition($id);
            if (MakerCommand::class !== $definition->getClass()) {
                continue;
            }

            // MakeCommandRegistrationPass passes the maker as the first argument of a child definition
            $arguments = $definition->getArguments();
            $makerClass = $this->getMakerClass($container, $arguments['index_0'] ?? $arguments[0] ?? null);
            if (null === $makerClass || !is_a($makerClass, CommandAliasesAwareInterface::class, true)) {
                continue;
            }

            $tagAttributes = [];
            if (method_exists($makerClass, 'getCommandDescription')) {
                $tagAttributes['description'] = $makerClass::getCommandDescription();
            }

            $registeredCommands = array_column($tags, 'command');
            foreach ($makerClass::aliases() as $alias) {
                if (in_array($alias, $registeredCommands, true)) {
                    continue;
                }

                $definition->addTag('console.command', ['command' => $alias] + $tagAttributes);
                $registeredCommands[] = $alias;
            }
        }
    }

    private function getMakerClass(ContainerBuilder $container, mixed $makerReference): ?string
    {
        if (!$makerReference instanceof Reference || !$container->has((string) $makerReference)) {
            return null;
        }

        $makerId = (string) $makerReference;
        $makerClass = $container->getParameterBag()->resolveValue(
            $container->findDefinition($makerId)->getClass() ?? $makerId,
        );

        return is_string($makerClass) ? $makerClass : null;
    }
}
