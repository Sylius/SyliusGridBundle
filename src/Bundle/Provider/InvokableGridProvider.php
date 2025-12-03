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

namespace Sylius\Bundle\GridBundle\Provider;

use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Component\Grid\Definition\Grid as GridDefinition;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\InvokableGridCollectionInterface;
use Sylius\Component\Grid\Provider\GridProviderInterface;

final readonly class InvokableGridProvider implements GridProviderInterface
{
    public function __construct(
        private InvokableGridCollectionInterface $grids,
        private ArrayToDefinitionConverterInterface $converter,
    ) {
    }

    public function get(string $code): GridDefinition
    {
        if (!$this->grids->has($code)) {
            throw new UndefinedGridException($code);
        }

        $grid = $this->grids->get($code);
        $gridBuilder = GridBuilder::create($code);
        $grid($gridBuilder);

        return $this->converter->convert($code, $gridBuilder->toArray());
    }
}
