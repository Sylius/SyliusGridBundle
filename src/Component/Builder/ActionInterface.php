<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Builder;

use Sylius\Component\Grid\Definition\Action as ActionDefinition;

interface ActionInterface
{
    public static function create(string $name, string $type): self;

    public function getDefinition(): ActionDefinition;

    public function setLabel(string $label): self;

    public function setEnabled(bool $enabled): self;

    public function setIcon(string $icon): self;

    public function setOptions(array $options): self;

    public function setPosition(int $position): self;
}
