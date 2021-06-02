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

final class Action implements ActionInterface
{
    private string $name;
    private string $type;
    private ?string $label = null;
    private ?bool $enabled = null;
    private ?string $icon = null;
    private array $options = [];
    private ?int $position = null;

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public static function create(string $name, string $type): ActionInterface
    {
        return new self($name, $type);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setLabel(string $label): ActionInterface
    {
        $this->label = $label;

        return $this;
    }

    public function setEnabled(bool $enabled): ActionInterface
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function setIcon(string $icon): ActionInterface
    {
        $this->icon = $icon;

        return $this;
    }

    public function setOptions(array $options): ActionInterface
    {
        $this->options = $options;

        return $this;
    }

    public function setPosition(int $position): ActionInterface
    {
        $this->position  = $position;

        return $this;
    }

    public function toArray(): array
    {
        $output = ['type' => $this->type];

        if (null !== $this->label) {
            $output['label'] = $this->label;
        }

        if (null !== $this->enabled) {
            $output['enabled'] = $this->enabled;
        }

        if (null !== $this->icon) {
            $output['icon'] = $this->icon;
        }

        if (count($this->options) > 0) {
            $output['options'] = $this->options;
        }

        if (null !== $this->position) {
            $output['position'] = $this->position;
        }

        return $output;
    }
}
