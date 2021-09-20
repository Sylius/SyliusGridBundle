<?php

/*
 * This file is part of the SyliusGridBundle project.
 *
 * (c) Mobizel
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Grid\Config\Builder\Action;

final class UpdateAction
{
    public static function create(array $options = []): ActionInterface
    {
        $action = Action::create('update', 'update');
        $action->setLabel('sylius.ui.update');
        $action->setOptions($options);

        return $action;
    }
}
