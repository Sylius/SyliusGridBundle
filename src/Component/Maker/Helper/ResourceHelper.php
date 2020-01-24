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

class ResourceHelper implements ResourceHelperInterface
{
    /** @var array */
    private $syliusResources;

    public function __construct(array $syliusResources)
    {
        $this->syliusResources = $syliusResources;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourcesAliases(): array
    {
        return array_keys($this->syliusResources);
    }

    /**
     * {@inheritdoc}
     */
    public function isResourceAliasExist(string $resourceAlias): bool
    {
        return in_array($resourceAlias, $this->getResourcesAliases());
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceModelFromAlias(string $resourceAlias): string
    {
        [$appName, $resourceName] = $this->splitResourceAlias($resourceAlias);

        return sprintf('%s.model.%s.class', $appName, $resourceName);
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceNameFromAlias(string $resourceAlias): string
    {
        return $this->splitResourceAlias($resourceAlias)[1];
    }

    /**
     * {@inheritdoc}
     */
    public function splitResourceAlias(string $resourceAlias): array
    {
        return explode('.', $resourceAlias);
    }
}
