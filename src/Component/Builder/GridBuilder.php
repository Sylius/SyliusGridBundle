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

namespace Sylius\Component\Grid\Builder;

use Sylius\Component\Grid\Definition\ActionGroup;
use Sylius\Component\Grid\Definition\Grid;

final class GridBuilder implements GridBuilderInterface
{
    private const DEFAULT_DRIVER_NAME = 'doctrine/orm';

    private Grid $gridDefinition;

    /** @var array<string, ActionGroup> */
    private array $actionGroups = [];

    public function __construct(string $code, string $resourceClass)
    {
        $this->gridDefinition = Grid::fromCodeAndDriverConfiguration(
            $code,
            self::DEFAULT_DRIVER_NAME,
            ['class' => $resourceClass]
        );
    }

    public static function create(string $code, string $resourceClass): GridBuilderInterface
    {
        return new self($code, $resourceClass);
    }

    public function getDefinition(): Grid
    {
        return $this->gridDefinition;
    }

    public function setDriver(string $driver): GridBuilderInterface
    {
        $this->gridDefinition->setDriver($driver);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRepositoryMethod($method, array $arguments = []): GridBuilderInterface
    {
        $driverConfiguration = $this->gridDefinition->getDriverConfiguration();
        $driverConfiguration['repository'] = [
            'method' => $method,
            'arguments' => $arguments,
        ];

        $this->gridDefinition->setDriverConfiguration($driverConfiguration);

        return $this;
    }

    public function addField(FieldInterface $field): GridBuilderInterface
    {
        $this->gridDefinition->addField($field->getDefinition());

        return $this;
    }

    public function removeField(string $name): GridBuilderInterface
    {
        $this->gridDefinition->removeField($name);

        return $this;
    }

    public function orderBy(string $name, string $direction = 'asc'): GridBuilderInterface
    {
        $this->gridDefinition->setSorting([$name => $direction]);

        return $this;
    }

    public function addOrderBy(string $name, string $direction = 'asc'): GridBuilderInterface
    {
        $sorting = $this->gridDefinition->getSorting();
        $sorting[$name] = $direction;
        $this->gridDefinition->setSorting($sorting);

        return $this;
    }

    public function setLimits(array $limits): GridBuilderInterface
    {
        $this->gridDefinition->setLimits($limits);

        return $this;
    }

    public function addFilter(FilterInterface $filter): GridBuilderInterface
    {
        $this->gridDefinition->addFilter($filter->getDefinition());

        return $this;
    }

    public function removeFilter(string $name): GridBuilderInterface
    {
        $this->gridDefinition->removeFilter($name);

        return $this;
    }

    public function addActionGroup(string $name): GridBuilderInterface
    {
        if (!isset($this->actionGroups[$name])) {
            $this->actionGroups[$name] = ActionGroup::named($name);
            $this->gridDefinition->addActionGroup($this->actionGroups[$name]);
        }

        return $this;
    }

    public function addMainAction(ActionInterface $action): GridBuilderInterface
    {
        $this->addActionGroup('main');
        $this->actionGroups['main']->addAction($action->getDefinition());

        return $this;
    }

    public function addItemAction(ActionInterface $action): GridBuilderInterface
    {
        if (!isset($this->actionGroups['item'])) {
            $this->actionGroups['item'] = ActionGroup::named('item');
            $this->gridDefinition->addActionGroup($this->actionGroups['item']);
        }

        $this->actionGroups['item']->addAction($action->getDefinition());

        return $this;
    }

    public function addCreateAction(array $options = []): GridBuilderInterface
    {
        $action = Action::create('create', 'create');
        $action->setOptions($options);
        $this->addMainAction($action);

        return $this;
    }

    public function addUpdateAction(array $options = []): GridBuilderInterface
    {
        $action = Action::create('update', 'update');
        $action->setOptions($options);
        $this->addItemAction($action);

        return $this;
    }

    public function addDeleteAction(array $options = []): GridBuilderInterface
    {
        $action = Action::create('delete', 'delete');
        $action->setOptions($options);
        $this->addItemAction($action);

        return $this;
    }
}
