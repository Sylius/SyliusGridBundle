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

namespace Sylius\Component\Grid\Builder\Filter;

/**
 * @method mixed getDefaultValue()
 * @method self setDefaultValue(mixed $defaultValue)
 * @method array<string, mixed> getCriteria()
 */
interface FilterInterface
{
    public static function create(string $name, string $type): self;

    public function getName(): string;

    public function getLabel(): string|bool|null;

    public function setLabel(string|bool|null $label): self;

    public function isEnabled(): bool;

    public function setEnabled(bool $enabled): self;

    public function getTemplate(): ?string;

    public function setTemplate(?string $template): self;

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array;

    /**
     * @param array<string, mixed> $options
     */
    public function setOptions(array $options): self;

    /**
     * @param mixed $value
     */
    public function addOption(string $option, $value): self;

    public function removeOption(string $option): self;

    /**
     * @return array<string, mixed>
     */
    public function getFormOptions(): array;

    /**
     * @param array<string, mixed> $formOptions
     */
    public function setFormOptions(array $formOptions): self;

    /**
     * @param mixed $value
     */
    public function addFormOption(string $option, $value): self;

    public function removeFormOption(string $option): self;

    /**
     * @param array<string, mixed> $criteria
     */
    public function setCriteria(array $criteria): self;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}

if (!interface_exists(\Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface::class, false)) {
    class_alias(FilterInterface::class, \Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface::class);
}
