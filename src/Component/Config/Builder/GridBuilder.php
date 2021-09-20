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

use Sylius\Component\Grid\Config\Builder\Action\ActionInterface;
use Sylius\Component\Grid\Config\Builder\ActionGroup\ActionGroup;
use Sylius\Component\Grid\Config\Builder\ActionGroup\ActionGroupInterface;
use Sylius\Component\Grid\Config\Builder\Field\FieldInterface;
use Sylius\Component\Grid\Config\Builder\Filter\FilterInterface;

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

    private function __construct(string $name, string $resourceClass)
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

    public function addActionGroup(ActionGroupInterface $actionGroup): self
    {
        $name = $actionGroup->getName();

        if (!isset($this->actionGroups[$name])) {
            $this->actionGroups[$name] = $actionGroup;
        }

        return $this;
    }

    public function addAction(ActionInterface $action, string $group): self
    {
        $actionGroup = ActionGroup::create($group);
        $actionGroup->addAction($action);
        $this->addActionGroup($actionGroup);

        return $this;
    }

    public function addMainAction(ActionInterface $action): self
    {
        $this->addAction($action, ActionGroupInterface::MAIN_GROUP);

        return $this;
    }

    public function addItemAction(ActionInterface $action): self
    {
        $this->addAction($action, ActionGroupInterface::ITEM_GROUP);

        return $this;
    }

    public function addSubItemAction(ActionInterface $action): self
    {
        $this->addAction($action, ActionGroupInterface::SUB_ITEM_GROUP);

        return $this;
    }

    public function addBulkAction(ActionInterface $action): self
    {
        $this->addAction($action, ActionGroupInterface::BULK_GROUP);

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
}
