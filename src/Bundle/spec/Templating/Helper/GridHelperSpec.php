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
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridView;

final class GridHelperSpec extends ObjectBehavior
{
    function let(GridRendererInterface $gridRenderer): void
    {
        $this->beConstructedWith($gridRenderer);
    }

    function it_uses_grid_renderer_to_render_grid(GridRendererInterface $gridRenderer, GridView $gridView): void
    {
        $gridRenderer->render($gridView, null)->willReturn('<html>Grid!</html>');
        $this->renderGrid($gridView, null)->shouldReturn('<html>Grid!</html>');
    }

    function it_uses_grid_renderer_to_render_field(GridRendererInterface $gridRenderer, GridView $gridView, Field $field): void
    {
        $gridRenderer->renderField($gridView, $field, 'foo')->willReturn('Value');
        $this->renderField($gridView, $field, 'foo')->shouldReturn('Value');
    }

    function it_uses_grid_renderer_to_render_action(GridRendererInterface $gridRenderer, GridView $gridView, Action $action): void
    {
        $gridRenderer->renderAction($gridView, $action, null)->willReturn('<a href="#">Go go Gadget arms!</a>');
        $this->renderAction($gridView, $action)->shouldReturn('<a href="#">Go go Gadget arms!</a>');
    }
}
