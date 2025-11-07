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

namespace Sylius\Component\Grid\Definition;

use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ArrayToDefinitionConverter implements ArrayToDefinitionConverterInterface
{
    public const EVENT_NAME = 'sylius.grid.%s';

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function convert(string $code, array $configuration): Grid
    {
        /** @var array<string, mixed> $driverConfiguration */
        $driverConfiguration = $configuration['driver']['options'] ?? [];

        $grid = Grid::fromCodeAndDriverConfiguration(
            $code,
            $configuration['driver']['name'],
            $driverConfiguration,
        );

        $grid->setProvider($configuration['provider'] ?? null);

        if (array_key_exists('sorting', $configuration)) {
            /** @var array<string, string> $sorting */
            $sorting = $configuration['sorting'];
            $grid->setSorting($sorting);
        }

        if (array_key_exists('limits', $configuration)) {
            /** @var int[] $limits */
            $limits = $configuration['limits'];
            $grid->setLimits($limits);
        }

        foreach ($configuration['fields'] ?? [] as $name => $fieldConfiguration) {
            $grid->addField($this->convertField($name, $fieldConfiguration));
        }

        foreach ($configuration['filters'] ?? [] as $name => $filterConfiguration) {
            $grid->addFilter($this->convertFilter($name, $filterConfiguration));
        }

        foreach ($configuration['actions'] ?? [] as $name => $actionGroupConfiguration) {
            $grid->addActionGroup($this->convertActionGroup($name, $actionGroupConfiguration));
        }

        $this->eventDispatcher->dispatch(new GridDefinitionConverterEvent($grid), $this->getEventName($code));

        return $grid;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function convertField(string $name, array $configuration): Field
    {
        $field = Field::fromNameAndType($name, $configuration['type']);

        if (array_key_exists('path', $configuration)) {
            $field->setPath($configuration['path']);
        }
        if (array_key_exists('label', $configuration)) {
            $field->setLabel($configuration['label']);
        }
        if (array_key_exists('enabled', $configuration)) {
            $field->setEnabled($configuration['enabled']);
        }
        if (array_key_exists('sortable', $configuration)) {
            $sortable = $configuration['sortable'];

            if ($sortable === true || $sortable === null) {
                $sortable = $name;
            }

            if ($sortable === false) {
                $sortable = null;
            }

            $field->setSortable($sortable);
        }
        if (array_key_exists('position', $configuration)) {
            $field->setPosition($configuration['position']);
        }
        if (array_key_exists('options', $configuration)) {
            /** @var array<string, mixed> $options */
            $options = $configuration['options'];
            $field->setOptions($options);
        }

        return $field;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function convertFilter(string $name, array $configuration): Filter
    {
        $filter = Filter::fromNameAndType($name, $configuration['type']);

        if (array_key_exists('label', $configuration)) {
            $filter->setLabel($configuration['label']);
        }
        if (array_key_exists('template', $configuration)) {
            $filter->setTemplate($configuration['template']);
        }
        if (array_key_exists('enabled', $configuration)) {
            $filter->setEnabled($configuration['enabled']);
        }
        if (array_key_exists('position', $configuration)) {
            $filter->setPosition($configuration['position']);
        }
        if (array_key_exists('options', $configuration)) {
            /** @var array<string, mixed> $options */
            $options = $configuration['options'];
            $filter->setOptions($options);
        }
        if (array_key_exists('form_options', $configuration)) {
            /** @var array<string, mixed> $formOptions */
            $formOptions = $configuration['form_options'];
            $filter->setFormOptions($formOptions);
        }
        if (array_key_exists('default_value', $configuration)) {
            $filter->setCriteria($configuration['default_value']);
        }

        return $filter;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function convertActionGroup(string $name, array $configuration): ActionGroup
    {
        $actionGroup = ActionGroup::named($name);

        foreach ($configuration as $actionName => $actionConfiguration) {
            $actionGroup->addAction($this->convertAction($actionName, $actionConfiguration));
        }

        return $actionGroup;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function convertAction(string $name, array $configuration): Action
    {
        $action = Action::fromNameAndType($name, $configuration['type']);

        if (array_key_exists('label', $configuration)) {
            $action->setLabel($configuration['label']);
        }
        if (array_key_exists('template', $configuration)) {
            $action->setTemplate($configuration['template']);
        }
        if (array_key_exists('icon', $configuration)) {
            $action->setIcon($configuration['icon']);
        }
        if (array_key_exists('enabled', $configuration)) {
            $action->setEnabled($configuration['enabled']);
        }
        if (array_key_exists('position', $configuration)) {
            $action->setPosition($configuration['position']);
        }
        if (array_key_exists('options', $configuration)) {
            /** @var array<string, mixed> $options */
            $options = $configuration['options'];
            $action->setOptions($options);
        }

        return $action;
    }

    private function getEventName(string $code): string
    {
        return sprintf(self::EVENT_NAME, str_replace('sylius_', '', $code));
    }
}
