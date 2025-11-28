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

namespace Sylius\Component\Grid\Provider;

use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Metadata\Grid\GridCollectionInterface;
use Sylius\Component\Grid\Metadata\Grid\InvokableGrid;

final readonly class InvokableGridProvider implements GridProviderInterface
{
    public function __construct(
        private GridCollectionInterface $grids,
        private ArrayToDefinitionConverterInterface $converter,
    ) {
    }

    public function get(string $code): Grid
    {
        if (!$this->grids->has($code)) {
            throw new UndefinedGridException($code);
        }

        $grid = $this->grids->get($code);

        if (!$grid instanceof InvokableGrid) {
            // TODO: it should throw another exception
            throw new UndefinedGridException($code);
        }

        $gridBuilder = GridBuilder::create($code, $grid->getResourceClass());
        $grid($gridBuilder);

        return $this->converter->convert($code, $gridBuilder->toArray());
    }
}
