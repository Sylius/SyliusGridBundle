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

namespace spec\Sylius\Bundle\GridBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Renderer\BulkActionGridRendererInterface;
use Sylius\Component\Grid\View\GridView;

final class BulkActionGridHelperSpec extends ObjectBehavior
{
    function let(BulkActionGridRendererInterface $bulkActionGridRenderer): void
    {
        $this->beConstructedWith($bulkActionGridRenderer);
    }

    function it_uses_a_grid_renderer_to_render_a_bulk_action(
        BulkActionGridRendererInterface $bulkActionGridRenderer,
        GridView $gridView,
        Action $bulkAction
    ): void {
        $bulkActionGridRenderer->renderBulkAction($gridView, $bulkAction, null)->willReturn('<a href="#">Delete</a>');
        $this->renderBulkAction($gridView, $bulkAction)->shouldReturn('<a href="#">Delete</a>');
    }
}
