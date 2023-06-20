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

namespace Sylius\Component\Grid\Data;

use Psr\Container\ContainerInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Webmozart\Assert\Assert;

final class Provider implements DataProviderInterface
{
    public function __construct(
        private ContainerInterface $locator,
        private DataProviderInterface $decorated,
    ) {
    }

    public function getData(Grid $grid, Parameters $parameters)
    {
        $provider = $grid->getProvider();

        if (null === $provider) {
            return $this->decorated->getData($grid, $parameters);
        }

        if (\is_callable($provider)) {
            return $provider($grid, $parameters);
        }

        if (!$this->locator->has($provider)) {
            throw new \RuntimeException(sprintf('Provider "%s" not found on grid "%s"', $provider, $grid->getCode()));
        }

        $providerInstance = $this->locator->get($provider);
        Assert::isInstanceOf($providerInstance, DataProviderInterface::class);

        return $providerInstance->getData($grid, $parameters);
    }
}
