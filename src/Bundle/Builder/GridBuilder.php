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

namespace Sylius\Bundle\GridBundle\Builder;

use Sylius\Bundle\GridBundle\Builder\Action\ActionInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ActionGroupInterface;
use Sylius\Bundle\GridBundle\Builder\Field\FieldInterface;
use Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface;

final class GridBuilder implements GridBuilderInterface
{
    private const DEFAULT_DRIVER_NAME = 'doctrine/orm';

    private string $name;

    private string $driver;

    private array $driverConfiguration = [];

    /** @var FieldInterface[] */
    private array $fields = [];

    private array $sorting = [];

    private array $filters = [];

    /** @var ActionGroupInterface[] */
    private array $actionGroups = [];

    private array $limits = [];

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

    public function removeActionGroup(string $name): self
    {
        unset($this->actionGroups[$name]);

        return $this;
    }

    public function addAction(ActionInterface $action, string $group): self
    {
        $this->addActionGroup(ActionGroup::create($group));

        $this->actionGroups[$group]->addAction($action);

        return $this;
    }

    public function removeAction(string $name, string $group): self
    {
        $this->actionGroups[$group]->removeAction($name);

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
