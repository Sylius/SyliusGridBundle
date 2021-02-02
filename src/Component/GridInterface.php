<?php

declare(strict_types=1);

namespace Sylius\Component\Grid;

use Sylius\Component\Grid\Definition\Grid;

interface GridInterface
{
    public static function getName(): string;

    public static function getResourceClass(): string;

    public function getDefinition(): Grid;
}
