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

namespace Sylius\Component\Grid\Definition;

use Webmozart\Assert\Assert;

class Filter
{
    private const DEFAULT_POSITION = 100;

    private string $name;

    private string $type;

    /** @var string|bool|null */
    private $label;

    private bool $enabled = true;

    private ?string $template = null;

    /** @var array<string, mixed> */
    private array $options = [];

    /** @var array<string, mixed> */
    private array $formOptions = [];

    /** @var mixed */
    private $criteria;

    /**
     * Position equals to 100 to ensure that while sorting filters by position ASC
     * the filters positioned by default will be last
     */
    private int $position = self::DEFAULT_POSITION;

    private function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;

        $this->label = $name;
    }

    public static function fromNameAndType(string $name, string $type): self
    {
        return new self($name, $type);
    }

    /**
     * @param array{
     *     name: string,
     *     type: string,
     *     label: string|bool|null,
     *     enabled: ?bool,
     *     options: ?array<string, mixed>,
     *     formOptions: ?array<string, mixed>,
     *     position: ?int,
     *     criteria: ?array<string, mixed>
     * } $data
     */
    public static function fromArray(array $data): self
    {
        Assert::notNull($data['name']);
        Assert::notNull($data['type']);

        $filter = self::fromNameAndType(
            $data['name'],
            $data['type'],
        );

        $filter->setLabel($data['label'] ?? null);
        $filter->setEnabled($data['enabled'] ?? true);
        $filter->setOptions($data['options'] ?? []);
        $filter->setFormOptions($data['formOptions'] ?? []);
        $filter->setPosition($data['position'] ?? self::DEFAULT_POSITION);
        $filter->setCriteria($data['criteria'] ?? []);

        return $filter;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string|bool|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string|bool|null $label
     */
    public function setLabel($label): void
    {
        $this->label = $label;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return array<string, mixed>
     */
    public function getFormOptions(): array
    {
        return $this->formOptions;
    }

    /**
     * @param array<string, mixed> $formOptions
     */
    public function setFormOptions(array $formOptions): void
    {
        $this->formOptions = $formOptions;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param mixed $criteria
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'type' => $this->getType(),
            'label' => $this->getLabel(),
            'enabled' => $this->isEnabled(),
            'options' => $this->getOptions(),
            'formOptions' => $this->getFormOptions(),
            'position' => $this->getPosition(),
            'criteria' => $this->getCriteria(),
        ];
    }
}
