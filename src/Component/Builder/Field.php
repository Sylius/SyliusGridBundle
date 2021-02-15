<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Builder;

use Sylius\Component\Grid\Definition\Field as FieldDefinition;

final class Field implements FieldInterface
{
    private FieldDefinition $fieldDefinition;

    public function __construct(string $name, string $type)
    {
        $this->fieldDefinition = FieldDefinition::fromNameAndType($name, $type);
    }

    public static function create(string $name, string $type): FieldInterface
    {
        return new self($name, $type);
    }

    public function getDefinition(): FieldDefinition
    {
        return $this->fieldDefinition;
    }

    public function setPath(string $path): FieldInterface
    {
        $this->fieldDefinition->setPath($path);

        return $this;
    }

    public function setLabel(string $label): FieldInterface
    {
        $this->fieldDefinition->setLabel($label);

        return $this;
    }

    public function setEnabled(bool $enabled): FieldInterface
    {
        $this->fieldDefinition->setEnabled($enabled);

        return $this;
    }

    public function setSortable(bool $sortable, string $path = null): FieldInterface
    {
        if ($sortable) {
            $this->fieldDefinition->setSortable($path ?: $this->fieldDefinition->getName());
        } else {
            $this->fieldDefinition->setSortable(null);
        }

        return $this;
    }

    public function setPosition(int $position): FieldInterface
    {
        $this->fieldDefinition->setPosition($position);

        return $this;
    }

    public function setOptions(array $options): FieldInterface
    {
        $this->fieldDefinition->setOptions($options);

        return $this;
    }
}
