<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Builder;

use Sylius\Component\Grid\Definition\Filter as FilterDefinition;

interface FilterInterface
{
    public static function create(string $name, string $type): self;

    public function getDefinition(): FilterDefinition;

    public function setLabel(?string $label): self;

    public function setEnabled(bool $enabled): self;

    public function setTemplate(string $template): self;

    public function setOptions(array $options): self;

    public function setFormOptions(array $formOptions): self;

    public function setCriteria(array $criteria): self;
}
