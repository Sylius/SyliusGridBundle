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

namespace Sylius\Component\Grid\Builder;

use Sylius\Component\Grid\Definition\Field as FieldDefinition;

interface FieldInterface
{
    public static function create(string $name, string $type): self;

    public function getDefinition(): FieldDefinition;

    public function setPath(string $path): self;

    public function setLabel(string $label): self;

    public function setEnabled(bool $enabled): self;

    public function setSortable(bool $sortable, string $path = null): self;

    public function setPosition(int $position): self;

    public function setOptions(array $options): self;
}
