<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Dummy;

use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;

final class NoResourceGrid extends AbstractGrid
{
    public static function getName(): string
    {
        return 'app_no_resource';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
    }
}
