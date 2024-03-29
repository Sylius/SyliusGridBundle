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

namespace Sylius\Bundle\GridBundle\Templating\Helper;

use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Renderer\BulkActionGridRendererInterface;
use Sylius\Component\Grid\View\GridView;

/**
 * @final
 */
class BulkActionGridHelper
{
    private BulkActionGridRendererInterface $bulkActionGridRenderer;

    public function __construct(BulkActionGridRendererInterface $bulkActionGridRenderer)
    {
        $this->bulkActionGridRenderer = $bulkActionGridRenderer;
    }

    /**
     * @param mixed|null $data
     */
    public function renderBulkAction(GridView $gridView, Action $bulkAction, $data = null): string
    {
        return $this->bulkActionGridRenderer->renderBulkAction($gridView, $bulkAction, $data);
    }
}
