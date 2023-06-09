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
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ActionGroupInterface;
use Sylius\Bundle\GridBundle\Builder\Field\FieldInterface;
use Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface;

/**
 * @method string|callable|null getProvider
 * @method GridBuilderInterface setProvider(string|callable|null $provider)
 *
 * @psalm-method string|callable|null getProvider
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
     * @param string|array $method
     */
    public function setRepositoryMethod($method, array $arguments = []): self;

    public function addField(FieldInterface $field): self;

    public function removeField(string $name): self;

    public function orderBy(string $name, string $direction): self;

    public function addOrderBy(string $name, string $direction = 'asc'): self;

    public function setLimits(array $limits): self;

    public function addFilter(FilterInterface $filter): self;

    public function removeFilter(string $name): self;

    public function addActionGroup(ActionGroupInterface $actionGroup): self;

    public function removeActionGroup(string $name): self;

    public function addAction(ActionInterface $action, string $group): self;

    public function removeAction(string $name, string $group): self;

    public function extends(string $gridName): self;

    public function toArray(): array;
}
