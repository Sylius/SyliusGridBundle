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

interface ActionInterface
{
    public static function create(string $name, string $type): self;

    public function getName(): string;

    public function setLabel(string $label): self;

    public function setEnabled(bool $enabled): self;

    public function setIcon(string $icon): self;

    public function setOptions(array $options): self;

    public function setPosition(int $position): self;

    public function toArray(): array;
}
