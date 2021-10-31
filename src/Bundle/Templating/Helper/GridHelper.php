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

namespace Sylius\Bundle\GridBundle\Templating\Helper;

use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridView;

class GridHelper
{
    private GridRendererInterface $gridRenderer;

    public function __construct(GridRendererInterface $gridRenderer)
    {
        $this->gridRenderer = $gridRenderer;
    }

    /**
     * @return string
     */
    public function renderGrid(GridView $gridView, ?string $template = null)
    {
        return $this->gridRenderer->render($gridView, $template);
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    public function renderField(GridView $gridView, Field $field, $data)
    {
        return $this->gridRenderer->renderField($gridView, $field, $data);
    }

    /**
     * @param mixed|null $data
     *
     * @return string
     */
    public function renderAction(GridView $gridView, Action $action, $data = null)
    {
        return $this->gridRenderer->renderAction($gridView, $action, $data);
    }

    /**
     * @return string
     */
    public function renderFilter(GridView $gridView, Filter $filter)
    {
        return $this->gridRenderer->renderFilter($gridView, $filter);
    }
}
