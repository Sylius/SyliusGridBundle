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

final class Field implements FieldInterface
{
    private string $name;
    private string $type;
    private ?string $path = null;
    private ?string $label = null;
    private ?bool $enabled = null;
    /** @var bool|string|null */
    private $sortable = null;
    private ?int $position = null;
    private array $options = [];

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public static function create(string $name, string $type): FieldInterface
    {
        return new self($name, $type);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setPath(string $path): FieldInterface
    {
        $this->path = $path;

        return $this;
    }

    public function setLabel(string $label): FieldInterface
    {
        $this->label = $label;

        return $this;
    }

    public function setEnabled(bool $enabled): FieldInterface
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function setSortable(bool $sortable, string $path = null): FieldInterface
    {
        if ($sortable) {
            $this->sortable = $path ?: true;
        } else {
            $this->sortable = null;
        }

        return $this;
    }

    public function setPosition(int $position): FieldInterface
    {
        $this->position = $position;

        return $this;
    }

    public function setOptions(array $options): FieldInterface
    {
        $this->options = $options;

        return $this;
    }

    public function toArray(): array
    {
        $output = ['type' => $this->type];

        if (null !== $this->label) {
            $output['label'] = $this->label;
        }

        if (null !== $this->path) {
            $output['path'] = $this->path;
        }

        if (null !== $this->enabled) {
            $output['enabled'] = $this->enabled;
        }

        if (null !== $this->sortable) {
            $output['sortable'] = $this->sortable;
        }

        if (null !== $this->position) {
            $output['position'] = $this->position;
        }

        if (count($this->options) > 0) {
            $output['options'] = $this->options;
        }

        return $output;
    }
}
