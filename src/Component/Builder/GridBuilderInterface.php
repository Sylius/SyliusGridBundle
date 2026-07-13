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
use Sylius\Component\Grid\Builder\ActionGroup\ActionGroupInterface;
use Sylius\Component\Grid\Builder\Field\FieldInterface;
use Sylius\Component\Grid\Builder\Filter\FilterInterface;

/**
 * @method string|callable|null getProvider()
 * @method GridBuilderInterface setProvider(string|callable|null $provider)
 * @method GridBuilderInterface withFields(FieldInterface ...$fields)
 * @method GridBuilderInterface withFilters(FilterInterface ...$filters)
 * @method GridBuilderInterface withActions(string $group, ActionInterface ...$actions)
 * @method GridBuilderInterface withMainActions(ActionInterface ...$actions)
 * @method GridBuilderInterface withItemActions(ActionInterface ...$actions)
 * @method GridBuilderInterface withSubItemActions(ActionInterface ...$actions)
 * @method GridBuilderInterface withBulkActions(ActionInterface ...$actions)
 *
 * @psalm-method string|callable|null getProvider()
 * @psalm-method GridBuilderInterface setProvider(string|callable|null $provider)
 */
interface GridBuilderInterface
{
    public static function create(string $name, ?string $resourceClass = null): self;

    public function getName(): string;

    public function setDriver(string $driver): self;

    /**
     * @param mixed $value
     */
    public function setDriverOption(string $option, $value): self;

    /**
     * @param string|callable|mixed[] $method
     * @param mixed[] $arguments
     */
    public function setRepositoryMethod($method, array $arguments = []): self;

    public function addField(FieldInterface $field): self;

    public function removeField(string $name): self;

    public function orderBy(string $name, string $direction): self;

    public function addOrderBy(string $name, string $direction = 'asc'): self;

    /**
     * @param int[] $limits
     */
    public function setLimits(array $limits): self;

    public function addFilter(FilterInterface $filter): self;

    public function removeFilter(string $name): self;

    public function addActionGroup(ActionGroupInterface $actionGroup): self;

    public function removeActionGroup(string $name): self;

    public function addAction(ActionInterface $action, string $group): self;

    public function removeAction(string $name, string $group): self;

    public function extends(string $gridName): self;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}

if (!interface_exists(\Sylius\Bundle\GridBundle\Builder\GridBuilderInterface::class, false)) {
    class_alias(GridBuilderInterface::class, \Sylius\Bundle\GridBundle\Builder\GridBuilderInterface::class);
}
