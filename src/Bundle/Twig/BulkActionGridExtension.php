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

namespace Sylius\Bundle\GridBundle\Twig;

use Sylius\Bundle\GridBundle\Templating\Helper\BulkActionGridHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class BulkActionGridExtension extends AbstractExtension
{
    private BulkActionGridHelper $bulkActionGridHelper;

    public function __construct(BulkActionGridHelper $bulkActionGridHelper)
    {
        $this->bulkActionGridHelper = $bulkActionGridHelper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'sylius_grid_render_bulk_action',
                [$this->bulkActionGridHelper, 'renderBulkAction'],
                ['is_safe' => ['html']],
            ),
        ];
    }
}
