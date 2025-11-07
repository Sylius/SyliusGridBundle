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

        /** @var string $driverName */
        $driverName = $configuration['driver']['name'];

        $grid = Grid::fromCodeAndDriverConfiguration(
            $code,
            $driverName,
            $driverConfiguration,
        );

        /** @var string|callable|null $provider */
        $provider = $configuration['provider'] ?? null;
        $grid->setProvider($provider);

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
            /** @var string $name */
            /** @var array<string, mixed> $fieldConfiguration */
            $grid->addField($this->convertField($name, $fieldConfiguration));
        }

        foreach ($configuration['filters'] ?? [] as $name => $filterConfiguration) {
            /** @var string $name */
            /** @var array<string, mixed> $filterConfiguration */
            $grid->addFilter($this->convertFilter($name, $filterConfiguration));
        }

        foreach ($configuration['actions'] ?? [] as $name => $actionGroupConfiguration) {
            /** @var string $name */
            /** @var array<string, mixed> $actionGroupConfiguration */
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
        /** @var string $type */
        $type = $configuration['type'];
        $field = Field::fromNameAndType($name, $type);

        if (array_key_exists('path', $configuration)) {
            /** @var string $path */
            $path = $configuration['path'];
            $field->setPath($path);
        }
        if (array_key_exists('label', $configuration)) {
            /** @var string $label */
            $label = $configuration['label'];
            $field->setLabel($label);
        }
        if (array_key_exists('enabled', $configuration)) {
            /** @var bool $enabled */
            $enabled = $configuration['enabled'];
            $field->setEnabled($enabled);
        }
        if (array_key_exists('sortable', $configuration)) {
            $sortable = $configuration['sortable'];

            if ($sortable === true || $sortable === null) {
                $sortable = $name;
            }

            if ($sortable === false) {
                $sortable = null;
            }

            /** @var string|null $sortable */
            $field->setSortable($sortable);
        }
        if (array_key_exists('position', $configuration)) {
            /** @var int $position */
            $position = $configuration['position'];
            $field->setPosition($position);
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
        /** @var string $type */
        $type = $configuration['type'];
        $filter = Filter::fromNameAndType($name, $type);

        if (array_key_exists('label', $configuration)) {
            /** @var string|bool|null $label */
            $label = $configuration['label'];
            $filter->setLabel($label);
        }
        if (array_key_exists('template', $configuration)) {
            /** @var string $template */
            $template = $configuration['template'];
            $filter->setTemplate($template);
        }
        if (array_key_exists('enabled', $configuration)) {
            /** @var bool $enabled */
            $enabled = $configuration['enabled'];
            $filter->setEnabled($enabled);
        }
        if (array_key_exists('position', $configuration)) {
            /** @var int $position */
            $position = $configuration['position'];
            $filter->setPosition($position);
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
            /** @var string $actionName */
            /** @var array<string, mixed> $actionConfiguration */
            $actionGroup->addAction($this->convertAction($actionName, $actionConfiguration));
        }

        return $actionGroup;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function convertAction(string $name, array $configuration): Action
    {
        /** @var string $type */
        $type = $configuration['type'];
        $action = Action::fromNameAndType($name, $type);

        if (array_key_exists('label', $configuration)) {
            /** @var string $label */
            $label = $configuration['label'];
            $action->setLabel($label);
        }
        if (array_key_exists('template', $configuration)) {
            /** @var string $template */
            $template = $configuration['template'];
            $action->setTemplate($template);
        }
        if (array_key_exists('icon', $configuration)) {
            /** @var string $icon */
            $icon = $configuration['icon'];
            $action->setIcon($icon);
        }
        if (array_key_exists('enabled', $configuration)) {
            /** @var bool $enabled */
            $enabled = $configuration['enabled'];
            $action->setEnabled($enabled);
        }
        if (array_key_exists('position', $configuration)) {
            /** @var int $position */
            $position = $configuration['position'];
            $action->setPosition($position);
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
