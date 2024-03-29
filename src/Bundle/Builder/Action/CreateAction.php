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

namespace Sylius\Bundle\GridBundle\Builder\Action;

final class CreateAction
{
    public static function create(array $options = []): ActionInterface
    {
        $action = Action::create('create', 'create');
        $action->setLabel('sylius.ui.create');
        $action->setOptions($options);

        return $action;
    }
}
