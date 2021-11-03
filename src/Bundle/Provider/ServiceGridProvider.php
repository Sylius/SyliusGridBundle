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

namespace Sylius\Bundle\GridBundle\Provider;

use Sylius\Bundle\GridBundle\Registry\GridRegistry;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\GridProviderInterface;

final class ServiceGridProvider implements GridProviderInterface
{
    private ArrayToDefinitionConverterInterface $converter;
    private GridRegistry $gridRegistry;

    public function __construct(ArrayToDefinitionConverterInterface $converter, GridRegistry $gridRegistry)
    {
        $this->converter = $converter;
        $this->gridRegistry = $gridRegistry;
    }

    public function get(string $code): Grid
    {
        $grid = $this->gridRegistry->getGrid($code);

        if (null === $grid) {
            throw new UndefinedGridException($code);
        }

        $gridConfiguration = $grid->toArray();

        return $this->converter->convert($code, $gridConfiguration);
    }
}
