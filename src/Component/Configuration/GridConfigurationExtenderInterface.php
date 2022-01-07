<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Configuration;

interface GridConfigurationExtenderInterface
{
    public function extends(array $gridConfiguration, array $parentGridConfiguration): array;
}
