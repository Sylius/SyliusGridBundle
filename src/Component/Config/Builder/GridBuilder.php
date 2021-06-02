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

namespace Sylius\Component\Grid\Config\Builder;

final class GridBuilder implements GridBuilderInterface
{
    private const DEFAULT_DRIVER_NAME = 'doctrine/orm';

    private string $name;
    private string $driver;
    private array $driverConfiguration = [];
    private array $fields = [];
    private array $sorting = [];
    private array $filters = [];
    private array $actionGroups = [];
    private array $limits = [];

    public function __construct(string $name, string $resourceClass)
    {
        $this->name = $name;
        $this->driver = self::DEFAULT_DRIVER_NAME;
        $this->driverConfiguration['class'] = $resourceClass;
    }

    public static function create(string $name, string $resourceClass): GridBuilderInterface
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

    public function setRepositoryMethod($method, array $arguments = []): GridBuilderInterface
    {
        $this->driverConfiguration['repository'] = [
            'method' => $method,
            'arguments' => $arguments,
        ];

        return $this;
    }

    public function addField(FieldInterface $field): self
    {
        $this->fields[$field->getName()] = $field;

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

    public function addActionGroup(string $name): self
    {
        if (!isset($this->actionGroups[$name])) {
            $this->actionGroups[$name] = ActionGroup::create($name);
        }

        return $this;
    }

    public function addMainAction(ActionInterface $action): self
    {
        $this->addActionGroup('main');
        $this->actionGroups['main']->addAction($action);

        return $this;
    }

    public function addCreateAction(array $options = []): self
    {
        $action = Action::create('create', 'create');
        $action->setOptions($options);
        $this->addMainAction($action);

        return $this;
    }

    public function addItemAction(ActionInterface $action): self
    {
        if (!isset($this->actionGroups['item'])) {
            $this->actionGroups['item'] = ActionGroup::create('item');
        }

        $this->actionGroups['item']->addAction($action);

        return $this;
    }

    public function addUpdateAction(array $options = []): self
    {
        $action = Action::create('update', 'update');
        $action->setOptions($options);
        $this->addItemAction($action);

        return $this;
    }

    public function addDeleteAction(array $options = []): self
    {
        $action = Action::create('delete', 'delete');
        $action->setOptions($options);
        $this->addItemAction($action);

        return $this;
    }

    public function removeField(string $name): GridBuilderInterface
    {
        unset($this->fields[$name]);

        return $this;
    }

    public function setLimits(array $limits): GridBuilderInterface
    {
        $this->limits = $limits;

        return $this;
    }

    public function removeFilter(string $name): GridBuilderInterface
    {
        unset($this->filters[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $output = ['driver' => [
            'name' => $this->driver,
            'options' => $this->driverConfiguration,
        ]];

        if (count($this->fields) > 0) {
            $output['fields'] = array_map(function (FieldInterface $field) { return $field->toArray(); }, $this->fields);
        }

        if (count($this->sorting) > 0) {
            $output['sorting'] = $this->sorting;
        }

        if (count($this->filters) > 0) {
            $output['filters'] = array_map(function (FilterInterface $filter): array { return $filter->toArray(); }, $this->filters);
        }

        if (count($this->actionGroups) > 0) {
            $output['actions'] = array_map(function (ActionGroupInterface $actionGroup): array { return $actionGroup->toArray(); }, $this->actionGroups);
        }

        if (count($this->limits) > 0) {
            $output['limits'] = $this->limits;
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAlias(): string
    {
        return 'sylius_resource';
    }
}
