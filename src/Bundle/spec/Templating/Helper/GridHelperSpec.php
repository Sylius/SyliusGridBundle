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
use Sylius\Bundle\GridBundle\Templating\Helper\GridHelper;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridView;

final class GridHelperTest extends TestCase
{
    private GridRendererInterface $gridRenderer;

    private GridHelper $helper;

    protected function setUp(): void
    {
        $this->gridRenderer = $this->createMock(GridRendererInterface::class);
        $this->helper = new GridHelper($this->gridRenderer);
    }

    public function testUsesGridRendererToRenderGrid(): void
    {
        $gridView = $this->createMock(GridView::class);

        $this->gridRenderer
            ->expects($this->once())
            ->method('render')
            ->with($gridView, null)
            ->willReturn('<html>Grid!</html>')
        ;

        $result = $this->helper->renderGrid($gridView, null);

        $this->assertSame('<html>Grid!</html>', $result);
    }

    public function testUsesGridRendererToRenderField(): void
    {
        $gridView = $this->createMock(GridView::class);
        $field = $this->createMock(Field::class);

        $this->gridRenderer
            ->expects($this->once())
            ->method('renderField')
            ->with($gridView, $field, 'foo')
            ->willReturn('Value')
        ;

        $result = $this->helper->renderField($gridView, $field, 'foo');

        $this->assertSame('Value', $result);
    }

    public function testUsesGridRendererToRenderAction(): void
    {
        $gridView = $this->createMock(GridView::class);
        $action = $this->createMock(Action::class);

        $this->gridRenderer
            ->expects($this->once())
            ->method('renderAction')
            ->with($gridView, $action, null)
            ->willReturn('<a href="#">Go go Gadget arms!</a>')
        ;

        $result = $this->helper->renderAction($gridView, $action);

        $this->assertSame('<a href="#">Go go Gadget arms!</a>', $result);
    }
}
