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
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;

class GridBodyGenerator
{
    use CommonConverterTrait;

    public function getGridBuilderBody(Variable $gridBuilder, array $gridConfiguration): ?MethodCall
    {
        if (array_key_exists('driver', $gridConfiguration)) {
            $gridBuilder = $this->handleDriver($gridBuilder, $gridConfiguration['driver']);
            unset($gridConfiguration['driver']);
        }

        // Handle extends
        if (array_key_exists('extends', $gridConfiguration)) {
            $gridBuilder = new MethodCall($gridBuilder, 'extends', [
                $this->convertValue($gridConfiguration['extends']),
            ]);
            unset($gridConfiguration['extends']);
        }

        // Handle the sorting
        if (array_key_exists('sorting', $gridConfiguration)) {
            foreach ($gridConfiguration['sorting'] as $field => $sorting) {
                $gridBuilder = new MethodCall($gridBuilder, 'addOrderBy', [
                    $this->convertValue($field),
                    $this->convertValue($sorting),
                ]);
            }
            unset($gridConfiguration['sorting']);
        }

        $this->convertToFunctionCall($gridBuilder, $gridConfiguration, 'limits');

        // Handle the fields
        if (array_key_exists('fields', $gridConfiguration)) {
            foreach ($gridConfiguration['fields'] as $fieldName => $fieldConfig) {
                $gridBuilder = $this->convertField($gridBuilder, $fieldName, $fieldConfig);
            }
            unset($gridConfiguration['fields']);
        }

        // Handle filters
        if (array_key_exists('filters', $gridConfiguration)) {
            foreach ($gridConfiguration['filters'] as $filterName => $filterConfig) {
                $gridBuilder = $this->convertFilter($gridBuilder, $filterName, $filterConfig);
            }
            unset($gridConfiguration['filters']);
        }

        // Handle removals
        if (array_key_exists('removals', $gridConfiguration)) {
            //trigger_error('Removals are not implemented', E_USER_WARNING);
            unset($gridConfiguration['removals']);
        }

        // Handle actions
        if (array_key_exists('actions', $gridConfiguration)) {
            foreach ($gridConfiguration['actions'] as $type => $configuredTypes) {
                $mappings = [
                    'main' => 'MainActionGroup',
                    'item' => 'ItemActionGroup',
                    'subitem' => 'SubItemActionGroup',
                    'bulk' => 'BulkActionGroup',
                ];

                $gridBuilder = new MethodCall(
                    $gridBuilder,
                    'addActionGroup',
                    [
                        new Arg(new Node\Expr\StaticCall(
                            new Name($mappings[$type]),
                            'create',
                            $this->convertActionsToFunctionParameters($configuredTypes),
                        )),
                    ],
                );
            }
            unset($gridConfiguration['actions']);
        }

        $this->checkUnconsumedConfiguration('.', $gridConfiguration);

        if ($gridBuilder instanceof Variable) {
            return null;
        }

        return $gridBuilder;
    }

    /** * @return array<Node\Expr> */
    public function convertActionsToFunctionParameters(array $actions): array
    {
        $handleCustomGrid = function (string $actionName, array $configuration): Node {
            $field = new Node\Expr\StaticCall(new Name('Action'), 'create', [
                $this->convertValue($actionName),
                $this->convertValue($configuration['type']),
            ]);
            $this->convertToFunctionCall($field, $configuration, 'label');
            $this->convertToFunctionCall($field, $configuration, 'icon');
            $this->convertToFunctionCall($field, $configuration, 'enabled');
            $this->convertToFunctionCall($field, $configuration, 'position');
            $this->convertToFunctionCall($field, $configuration, 'options');

            return $field;
        };

        $field = [];
        foreach ($actions as $actionName => $actionConfiguration) {
            switch ($actionConfiguration['type']) {
                case 'create':
                    $field[] = new Node\Expr\StaticCall(new Name('CreateAction'), 'create');

                    break;
                case 'show':
                    $field[] = new Node\Expr\StaticCall(new Name('ShowAction'), 'create');

                    break;
                case 'delete':
                    $field[] = new Node\Expr\StaticCall(new Name('DeleteAction'), 'create');

                    break;
                case 'update':
                    $field[] = new Node\Expr\StaticCall(new Name('UpdateAction'), 'create');

                    break;
                default:
                    $field[] = $handleCustomGrid($actionName, $actionConfiguration);
            }
        }

        return $field;
    }

    private function handleDriver(Expr $gridBuilder, array $driverConfiguration): Expr
    {
        if (array_key_exists('name', $driverConfiguration)) {
            $gridBuilder = new MethodCall($gridBuilder, 'setDriver', [$this->convertValue($driverConfiguration['name'])]);
            unset($driverConfiguration['name']);
        }

        if (array_key_exists('repository', $driverConfiguration['options'] ?? [])) {
            $gridBuilder = $this->handleRepositoryConfiguration($gridBuilder, $driverConfiguration['options']['repository']);
            unset($driverConfiguration['options']['repository']);
        }

        if (array_key_exists('options', $driverConfiguration)) {
            foreach ($driverConfiguration['options'] as $option => $optionValue) {
                $gridBuilder = new MethodCall($gridBuilder, 'setDriverOption', [
                    $this->convertValue($option),
                    $this->convertValue($optionValue),
                ]);
            }
            unset($driverConfiguration['options']);
        }

        $this->checkUnconsumedConfiguration('driver', $driverConfiguration);

        return $gridBuilder;
    }

    public function handleRepositoryConfiguration(Expr $gridBuilder, array $configuration): Expr
    {
        $setRepositoryMethodArguments = [
            $this->convertValue($configuration['method']),
        ];
        unset($configuration['method']);

        if (array_key_exists('arguments', $configuration)) {
            $setRepositoryMethodArguments[] = $this->convertValue($configuration['arguments']);
            unset($configuration['arguments']);
        }

        $this->checkUnconsumedConfiguration('driver.repository', $configuration);

        return new MethodCall($gridBuilder, 'setRepositoryMethod', $setRepositoryMethodArguments);
    }

    public function convertFilter(Expr $gridBuilder, string $filterName, array $configuration): Expr
    {
        $filter = new StaticCall(new Name('Filter'), 'create', [
            $this->convertValue($filterName),
            $this->convertValue($configuration['type']),
        ]);
        unset($configuration['type']);

        $this->convertToFunctionCall($filter, $configuration, 'enabled');
        $this->convertToFunctionCall($filter, $configuration, 'label');
        $this->convertToFunctionCall($filter, $configuration, 'options');
        $this->convertToFunctionCall($filter, $configuration, 'form_options');

        $this->checkUnconsumedConfiguration('filter', $configuration);

        return new MethodCall($gridBuilder, 'addFilter', [new Arg($filter)]);
    }

    public function convertField(Expr $gridBuilder, string $fieldName, array $fieldConfig): Expr
    {
        $field = $this->createField($fieldConfig, $fieldName);
        unset($fieldConfig['type']);

        $this->convertToFunctionCall($field, $fieldConfig, 'enabled');
        $this->convertToFunctionCall($field, $fieldConfig, 'label');
        $this->convertToFunctionCall($field, $fieldConfig, 'position');
        $this->convertToFunctionCall($field, $fieldConfig, 'path');

        /*
         * Handling of the sortable attribute is a little complicated because:
         * sortable: ~
         * means the field is sortable with the default configuration
         */
        if (array_key_exists('sortable', $fieldConfig)) {
            $path = $fieldConfig['sortable'];

            if ($path === false) {
                $arguments = [
                    new ConstFetch(new Name('false')),
                ];
                $field = new MethodCall($field, 'setSortable', $arguments);
            } elseif ($path !== null) {
                $arguments = [
                    new ConstFetch(new Name('true')),
                ];
                if (is_string($path)) {
                    $actions[] = $this->convertValue((string) $path);
                }
                $field = new MethodCall($field, 'setSortable', $arguments);
            }

            unset($fieldConfig['sortable']);
        }

        // Only add the options if the value is not empty. This can happen for the twig field for example. The template is now
        // part of the create call and not an option anymore
        if (isset($fieldConfig['options'])) {
            if (count($fieldConfig['options']) > 0) {
                $field = new MethodCall($field, 'setOptions', [$this->convertValue($fieldConfig['options'])]);
            }
            unset($fieldConfig['options']);
        }

        $this->checkUnconsumedConfiguration('fields', $fieldConfig);

        return new MethodCall($gridBuilder, 'addField', [new Arg($field)]);
    }

    private function createField(array $fieldConfig, string $fieldName): Expr
    {
        switch ($fieldConfig['type']) {
            case 'datetime':
                $field = new StaticCall(new Name('DateTimeField'), 'create', [
                    $this->convertValue($fieldName),
                ]);

                break;
            case 'string':
                $field = new StaticCall(new Name('StringField'), 'create', [
                    $this->convertValue($fieldName),
                ]);

                break;
            case 'twig':
                $field = new StaticCall(new Name('TwigField'), 'create', [
                    $this->convertValue($fieldName),
                    $this->convertValue($fieldConfig['options']['template']),
                ]);

                break;
            default:
                $field = new StaticCall(new Name('Field'), 'create', [
                    $this->convertValue($fieldName),
                    $this->convertValue($fieldConfig['type']),
                ]);
        }

        return $field;
    }
}
