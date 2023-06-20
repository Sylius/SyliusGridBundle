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

    public function addOptions(array $options): self;

    /**
     * @param mixed $value
     */
    public function setOption(string $option, $value): self;

    public function removeOption(string $option): self;

    public function toArray(): array;
}
