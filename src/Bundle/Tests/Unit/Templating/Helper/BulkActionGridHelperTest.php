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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Templating\Helper;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Templating\Helper\BulkActionGridHelper;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Renderer\BulkActionGridRendererInterface;
use Sylius\Component\Grid\View\GridView;

final class BulkActionGridHelperTest extends TestCase
{
    private BulkActionGridRendererInterface $bulkActionGridRenderer;

    private BulkActionGridHelper $helper;

    protected function setUp(): void
    {
        $this->bulkActionGridRenderer = $this->createMock(BulkActionGridRendererInterface::class);
        $this->helper = new BulkActionGridHelper($this->bulkActionGridRenderer);
    }

    public function testUsesGridRendererToRenderBulkAction(): void
    {
        $gridView = $this->createMock(GridView::class);
        $bulkAction = $this->createMock(Action::class);

        $this->bulkActionGridRenderer
            ->expects($this->once())
            ->method('renderBulkAction')
            ->with($gridView, $bulkAction, null)
            ->willReturn('<a href="#">Delete</a>')
        ;

        $result = $this->helper->renderBulkAction($gridView, $bulkAction);

        $this->assertSame('<a href="#">Delete</a>', $result);
    }
}
