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

namespace Sylius\Component\Grid\Maker\Helper;

interface ResourceHelperInterface
{
    public function getResourcesAliases(): array;

    public function isResourceAliasExist(string $resourceAlias): bool;

    public function getResourceModelFromAlias(string $resourceAlias): string;

    public function getResourceNameFromAlias(string $resourceAlias): string;

    public function splitResourceAlias(string $resourceAlias): array;
}
