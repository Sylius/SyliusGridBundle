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

namespace Sylius\Bundle\GridBundle\Builder\ActionGroup;

use Sylius\Bundle\GridBundle\Builder\Action\ActionInterface;

final class MainActionGroup
{
    public static function create(ActionInterface ...$actions): ActionGroupInterface
    {
        return ActionGroup::create(ActionGroupInterface::MAIN_GROUP, ...$actions);
    }
}
