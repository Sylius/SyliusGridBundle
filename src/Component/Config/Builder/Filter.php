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

class Filter implements FilterInterface
{
    private string $name;
    private string $type;
    private ?string $label = null;
    private ?bool $enabled = null;
    private ?string $template = null;
    private array $options = [];
    private array $formOptions = [];
    private array $criteria = [];

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public static function create(string $name, string $type): FilterInterface
    {
        return new self($name, $type);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setLabel(?string $label): FilterInterface
    {
        $this->label = $label;

        return $this;
    }

    public function setEnabled(bool $enabled): FilterInterface
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function setTemplate(string $template): FilterInterface
    {
        $this->template = $template;

        return $this;
    }

    public function setOptions(array $options): FilterInterface
    {
        $this->options = $options;

        return $this;
    }

    public function setFormOptions(array $formOptions): FilterInterface
    {
        $this->formOptions = $formOptions;

        return $this;
    }

    public function setCriteria(array $criteria): FilterInterface
    {
        $this->criteria = $criteria;

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

        if (null !== $this->template) {
            $output['template'] = $this->template;
        }

        if (count($this->options) > 0) {
            $output['options'] = $this->options;
        }

        if (count($this->formOptions) > 0) {
            $output['form_options'] = $this->formOptions;
        }

        if (count($this->criteria) > 0) {
            $output['criteria'] = $this->criteria;
        }

        return $output;
    }
}
