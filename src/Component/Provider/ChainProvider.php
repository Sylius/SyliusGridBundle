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

namespace Sylius\Component\Grid\Provider;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\GridProviderInterface;

final class ChainProvider implements GridProviderInterface
{
    /** @var array<int, GridProviderInterface> */
    private array $providers;

    public function __construct(\Traversable $providers)
    {
        $this->providers = iterator_to_array($providers);
    }

    public function get(string $code): Grid
    {
        foreach ($this->providers as $provider) {
            try {
                return $provider->get($code);
            } catch (UndefinedGridException $exception) {
            }
        }

        throw new UndefinedGridException($code);
    }
}
