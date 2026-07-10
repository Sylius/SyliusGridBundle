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

use Sylius\Component\Grid\Builder\Action\ActionInterface;

final class UpdateAction
{
    /**
     * @param array<string, mixed> $options
     */
    public static function create(array $options = []): ActionInterface
    {
        $action = Action::create('update', 'update');
        $action->setLabel('sylius.ui.edit');
        $action->setOptions($options);

        return $action;
    }
}
