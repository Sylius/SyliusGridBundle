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

namespace Sylius\Component\Grid\Renderer;

use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\View\GridViewInterface;

interface GridRendererInterface
{
    /**
     * @return string
     */
    public function render(GridViewInterface $gridView, ?string $template = null);

    /**
     * @param mixed $data
     *
     * @return string
     */
    public function renderField(GridViewInterface $gridView, Field $field, $data);

    /**
     * @param mixed|null $data
     *
     * @return string
     */
    public function renderAction(GridViewInterface $gridView, Action $action, $data = null);

    /**
     * @return string
     */
    public function renderFilter(GridViewInterface $gridView, Filter $filter);
}
