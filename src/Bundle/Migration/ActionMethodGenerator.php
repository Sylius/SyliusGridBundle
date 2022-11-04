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

namespace Sylius\Bundle\GridBundle\Migration;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\SubItemActionGroup;
use Sylius\Component\Grid\Definition\Action;

class ActionMethodGenerator
{
    use CommonConverterTrait;

    public function __construct(
        private CodeGenerator $codeGenerator,
    ) {
    }

    public function convertActions($gridBuilder, array $actionConfiguration)
    {
        foreach ($actionConfiguration as $type => $configuredTypes) {
            $mappings = [
                'main' => MainActionGroup::class,
                'item' => ItemActionGroup::class,
                'subitem' => SubItemActionGroup::class,
                'bulk' => BulkActionGroup::class,
            ];

            $gridBuilder = new MethodCall(
                $gridBuilder,
                'addActionGroup',
                [
                    new Arg(new Node\Expr\StaticCall(
                        $this->codeGenerator->getRelativeClassName($mappings[$type]),
                        'create',
                        $this->convertActionsToFunctionParameters($configuredTypes),
                    )),
                ],
            );
        }

        return $gridBuilder;
    }

    /** * @return array<Node\Expr> */
    public function convertActionsToFunctionParameters(array $actions): array
    {
        $handleCustomGrid = function (string $actionName, array $configuration): Node {
            $this->codeGenerator->addUseStatement(Action::class);
            $field = new Node\Expr\StaticCall(
                $this->codeGenerator->getRelativeClassName(Action::class),
                'create',
                [
                    $this->convertValue($actionName),
                    $this->convertValue($configuration['type']),
                ],
            );
            $this->convertToFunctionCall($field, $configuration, 'label');
            $this->convertToFunctionCall($field, $configuration, 'icon');
            $this->convertToFunctionCall($field, $configuration, 'enabled');
            $this->convertToFunctionCall($field, $configuration, 'position');
            $this->convertToFunctionCall($field, $configuration, 'options');

            return $field;
        };

        $field = [];
        foreach ($actions as $actionName => $actionConfiguration) {
            $mappings = [
                'create' => CreateAction::class,
                'show' => ShowAction::class,
                'delete' => DeleteAction::class,
                'update' => UpdateAction::class,
            ];

            // Handle custom grid actions
            $actionType = $actionConfiguration['type'];
            if (!array_key_exists($actionType, $mappings)) {
                $field[] = $handleCustomGrid($actionName, $actionConfiguration);

                continue;
            }

            $field[] = new Node\Expr\StaticCall(
                $this->codeGenerator->getRelativeClassName($mappings[$actionType]),
                'create',
            );
        }

        return $field;
    }
}
