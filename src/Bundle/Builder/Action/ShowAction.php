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

namespace Sylius\Bundle\GridBundle\Builder\Action;

final class ShowAction
{
    public static function create(array $options = []): ActionInterface
    {
        $action = Action::create('show', 'show');
        $action->setLabel('sylius.ui.show');
        $action->setOptions($options);

        return $action;
    }
}
