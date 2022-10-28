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

namespace Sylius\Bundle\GridBundle\Builder\Filter;

final class Filter implements FilterInterface
{
    private string $name;

    private string $type;

    private string|bool|null $label = null;

    private ?bool $enabled = null;

    private ?string $template = null;

    private array $options = [];

    private array $formOptions = [];

    private mixed $criteria = [];

    private function __construct(string $name, string $type)
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

    public function getLabel(): string|bool|null
    {
        return $this->label;
    }

    public function setLabel(string|bool|null $label): FilterInterface
    {
        $this->label = $label;

        return $this;
    }

    public function setEnabled(bool $enabled): FilterInterface
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled ?? true;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): FilterInterface
    {
        $this->template = $template;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): FilterInterface
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function addOption(string $option, $value): FilterInterface
    {
        $this->options[$option] = $value;

        return $this;
    }

    public function removeOption(string $option): FilterInterface
    {
        unset($this->options[$option]);

        return $this;
    }

    public function getFormOptions(): array
    {
        return $this->formOptions;
    }

    public function setFormOptions(array $formOptions): FilterInterface
    {
        $this->formOptions = $formOptions;

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function addFormOption(string $option, $value): FilterInterface
    {
        $this->formOptions[$option] = $value;

        return $this;
    }

    public function removeFormOption(string $option): FilterInterface
    {
        unset($this->formOptions[$option]);

        return $this;
    }

    public function getCriteria(): mixed
    {
        return $this->criteria;
    }

    public function setCriteria(mixed $criteria): FilterInterface
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

        if ($this->criteria !== []) {
            $output['criteria'] = $this->criteria;
        }

        return $output;
    }
}
