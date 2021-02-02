<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Builder;

use Sylius\Component\Grid\Definition\ActionGroup;
use Sylius\Component\Grid\Definition\Grid;

interface GridBuilderInterface
{
    public static function create(string $code, string $resourceClass): self;

    public function setRepositoryMethod(string $method, array $arguments = []): self;

    public function addField(FieldInterface $field): self;

    public function removeField(string $name): self;

    public function orderBy(string $name, $direction = 'asc'): self;

    public function addFilter(FilterInterface $filter): self;

    public function removeFilter(string $name): self;

    public function addActionGroup(string $name): self;

    public function addMainAction(ActionInterface $action): self;

    public function addItemAction(ActionInterface $action): self;

    public function addCreateAction(array $options = []): self;

    public function addUpdateAction(array $options = []): self;

    public function addDeleteAction(array $options = []): self;

    public function getDefinition(): Grid;
}
