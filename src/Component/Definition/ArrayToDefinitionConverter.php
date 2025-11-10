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

/**
 * @template TDriver of array{
 *     name: string,
 *     options?: array<string, mixed>,
 * }
 * @template TField of array{
 *         type: string,
 *         label?: string,
 *         path?: string,
 *         enabled?: bool,
 *         sortable?: bool|string,
 *         position?: int,
 *         options?: array<string, mixed>,
 *  }
 * @template TFilter of array{
 *         type: string,
 *         label?: string,
 *         template?: string,
 *         enabled?: bool,
 *         position?: int,
 *         options?: array<string, mixed>,
 *         form_options?: array<string, mixed>,
 *         default_value?: mixed,
 *  }
 * @template TActionGroup of array<string, TAction>
 * @template TAction of array{
 *         type: string,
 *         label?: string,
 *         template?: string,
 *         icon?: string,
 *         enabled?: bool,
 *         position?: int,
 *         options?: array<string, mixed>,
 *  }
 */
final class ArrayToDefinitionConverter implements ArrayToDefinitionConverterInterface
{
    public const EVENT_NAME = 'sylius.grid.%s';

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array{
     *        driver: TDriver,
     *        provider?: string|callable,
     *        sorting?: array<string, string>,
     *        limits?: int[],
     *        fields?: array<string, TField>,
     *        filters?: array<string, TFilter>,
     *        actions?: array<string, TActionGroup>,
     * } $configuration
     */
    public function convert(string $code, array $configuration): Grid
    {
        $grid = Grid::fromCodeAndDriverConfiguration(
            $code,
            $configuration['driver']['name'],
            $configuration['driver']['options'] ?? [],
        );

        $grid->setProvider($configuration['provider'] ?? null);

        if (array_key_exists('sorting', $configuration)) {
            $grid->setSorting($configuration['sorting']);
        }

        if (array_key_exists('limits', $configuration)) {
            $grid->setLimits($configuration['limits']);
        }

        /** @var TField $fieldConfiguration */
        foreach ($configuration['fields'] ?? [] as $name => $fieldConfiguration) {
            $grid->addField($this->convertField($name, $fieldConfiguration));
        }

        /** @var TFilter $filterConfiguration */
        foreach ($configuration['filters'] ?? [] as $name => $filterConfiguration) {
            $grid->addFilter($this->convertFilter($name, $filterConfiguration));
        }

        /** @var TActionGroup $actionGroupConfiguration */
        foreach ($configuration['actions'] ?? [] as $name => $actionGroupConfiguration) {
            $grid->addActionGroup($this->convertActionGroup($name, $actionGroupConfiguration));
        }

        $this->eventDispatcher->dispatch(new GridDefinitionConverterEvent($grid), $this->getEventName($code));

        return $grid;
    }

    /**
     * @param TField $configuration
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
            $field->setOptions($configuration['options']);
        }

        return $field;
    }

    /**
     * @param TFilter $configuration
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
            $filter->setOptions($configuration['options']);
        }
        if (array_key_exists('form_options', $configuration)) {
            $filter->setFormOptions($configuration['form_options']);
        }
        if (array_key_exists('default_value', $configuration)) {
            $filter->setCriteria($configuration['default_value']);
        }

        return $filter;
    }

    /**
     * @param TActionGroup $configuration
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
     * @param TAction $configuration
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
            $action->setOptions($configuration['options']);
        }

        return $action;
    }

    private function getEventName(string $code): string
    {
        return sprintf(self::EVENT_NAME, str_replace('sylius_', '', $code));
    }
}
