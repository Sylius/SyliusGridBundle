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

namespace Sylius\Bundle\GridBundle\Builder\Field;

interface FieldInterface
{
    public static function create(string $name, string $type): self;

    public function getName(): string;

    public function getPath(): ?string;

    public function setPath(?string $path): self;

    public function getLabel(): ?string;

    public function setLabel(?string $label): self;

    public function isEnabled(): bool;

    public function setEnabled(bool $enabled): self;

    public function isSortable(): bool;

    public function setSortable(bool $sortable, string $path = null): self;

    public function getPosition(): ?int;

    public function setPosition(?int $position): self;

    public function getOptions(): array;

    public function setOptions(array $options): self;

    /**
     * @param mixed $value
     */
    public function addOption(string $option, $value): FieldInterface;

    public function removeOption(string $option): FieldInterface;

    public function toArray(): array;
}
