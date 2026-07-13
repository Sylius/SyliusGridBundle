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

namespace Sylius\Component\Grid\Builder;

use Sylius\Component\Grid\Builder\Action\ActionInterface;
use Sylius\Component\Grid\Builder\ActionGroup\ActionGroup;
use Sylius\Component\Grid\Builder\ActionGroup\ActionGroupInterface;
use Sylius\Component\Grid\Builder\Field\FieldInterface;
use Sylius\Component\Grid\Builder\Filter\FilterInterface;

final class GridBuilder implements GridBuilderInterface
{
    private const DEFAULT_DRIVER_NAME = 'doctrine/orm';

    private string $name;

    private string $driver;

    /** @var array<string, mixed> */
    private array $driverConfiguration = [];

    /** @var string|callable|null */
    private $provider;

    /** @var array<string, FieldInterface> */
    private array $fields = [];

    /** @var array<string, string> */
    private array $sorting = [];

    /** @var array<string, FilterInterface> */
    private array $filters = [];

    /** @var array<string, ActionGroupInterface> */
    private array $actionGroups = [];

    /** @var int[] */
    private array $limits = [];

    private ?string $extends = null;

    /**
     * @var array{
     *     fields?: string[],
     *     filters?: string[],
     *     actions?: mixed,
     * }
     */
    private array $removals = [];

    private function __construct(string $name, ?string $resourceClass = null)
    {
        $this->name = $name;
        $this->driver = self::DEFAULT_DRIVER_NAME;

        if (null !== $resourceClass) {
            $this->driverConfiguration['class'] = $resourceClass;
        }
    }

    public static function create(string $name, ?string $resourceClass = null): GridBuilderInterface
    {
        return new self($name, $resourceClass);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDriver(string $driver): GridBuilderInterface
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function setDriverOption(string $option, $value): GridBuilderInterface
    {
        $this->driverConfiguration[$option] = $value;

        return $this;
    }

    public function setRepositoryMethod($method, array $arguments = []): GridBuilderInterface
    {
        return $this->setDriverOption('repository', [
            'method' => $method,
            'arguments' => $arguments,
        ]);
    }

    public function getProvider(): callable|string|null
    {
        return $this->provider;
    }

    public function setProvider(callable|string|null $provider): GridBuilderInterface
    {
        $this->provider = $provider;

        return $this;
    }

    public function addField(FieldInterface $field): self
    {
        $this->fields[$field->getName()] = $field;

        return $this;
    }

    public function withFields(FieldInterface ...$fields): GridBuilderInterface
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    public function removeField(string $name): GridBuilderInterface
    {
        unset($this->fields[$name]);
        $this->removals['fields'][] = $name;

        return $this;
    }

    public function orderBy(string $name, string $direction = 'asc'): self
    {
        $this->sorting = [$name => $direction];

        return $this;
    }

    public function addOrderBy(string $name, string $direction = 'asc'): self
    {
        $this->sorting[$name] = $direction;

        return $this;
    }

    public function addFilter(FilterInterface $filter): self
    {
        $this->filters[$filter->getName()] = $filter;

        return $this;
    }

    public function withFilters(FilterInterface ...$filters): GridBuilderInterface
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }

        return $this;
    }

    public function removeFilter(string $name): GridBuilderInterface
    {
        unset($this->filters[$name]);
        $this->removals['filters'][] = $name;

        return $this;
    }

    public function addActionGroup(ActionGroupInterface $actionGroup): self
    {
        $name = $actionGroup->getName();

        if (!isset($this->actionGroups[$name])) {
            $this->actionGroups[$name] = $actionGroup;
        }

        return $this;
    }

    public function removeActionGroup(string $name): self
    {
        unset($this->actionGroups[$name]);
        $this->removals['actions'][] = $name;

        return $this;
    }

    public function addAction(ActionInterface $action, string $group): self
    {
        $this->addActionGroup(ActionGroup::create($group));

        $this->actionGroups[$group]->addAction($action);

        return $this;
    }

    public function withActions(string $group, ActionInterface ...$actions): self
    {
        foreach ($actions as $action) {
            $this->addAction($action, $group);
        }

        return $this;
    }

    public function withMainActions(ActionInterface ...$actions): self
    {
        return $this->withActions(ActionGroupInterface::MAIN_GROUP, ...$actions);
    }

    public function withItemActions(ActionInterface ...$actions): self
    {
        return $this->withActions(ActionGroupInterface::ITEM_GROUP, ...$actions);
    }

    public function withSubItemActions(ActionInterface ...$actions): self
    {
        return $this->withActions(ActionGroupInterface::SUB_ITEM_GROUP, ...$actions);
    }

    public function withBulkActions(ActionInterface ...$actions): self
    {
        return $this->withActions(ActionGroupInterface::BULK_GROUP, ...$actions);
    }

    public function removeAction(string $name, string $group): self
    {
        $actionGroup = $this->actionGroups[$group] ?? null;
        if ($actionGroup !== null) {
            $actionGroup->removeAction($name);
        }

        if (!isset($this->removals['actions'])) {
            $this->removals['actions'] = [];
        }

        if (!is_array($this->removals['actions'])) {
            $this->removals['actions'] = [];
        }

        if (!isset($this->removals['actions'][$group])) {
            $this->removals['actions'][$group] = [];
        }

        $this->removals['actions'][$group][] = $name;

        return $this;
    }

    public function setLimits(array $limits): GridBuilderInterface
    {
        $this->limits = $limits;

        return $this;
    }

    public function extends(string $gridName): GridBuilderInterface
    {
        $this->extends = $gridName;

        return $this;
    }

    public function toArray(): array
    {
        $output = [
            'driver' => [
                'name' => $this->driver,
            ],
            'removals' => $this->removals,
        ];

        if (null !== $this->provider) {
            $output['provider'] = $this->provider;
        }

        if (count($this->driverConfiguration) > 0) {
            $output['driver']['options'] = $this->driverConfiguration;
        }

        if (count($this->fields) > 0) {
            $output['fields'] = array_map(function (FieldInterface $field) { return $field->toArray(); }, $this->fields);
        }

        if (count($this->sorting) > 0) {
            $output['sorting'] = $this->sorting;
        }

        foreach ($this->filters as $name => $filter) {
            $output['filters'][$name] = $filter->toArray();
        }

        foreach ($this->actionGroups as $name => $actionGroup) {
            $output['actions'][$name] = $actionGroup->toArray();
        }

        if (count($this->limits) > 0) {
            $output['limits'] = $this->limits;
        }

        if (null !== $this->extends) {
            $output['extends'] = $this->extends;
        }

        return $output;
    }
}

if (!class_exists(\Sylius\Bundle\GridBundle\Builder\GridBuilder::class, false)) {
    class_alias(GridBuilder::class, \Sylius\Bundle\GridBundle\Builder\GridBuilder::class);
}
