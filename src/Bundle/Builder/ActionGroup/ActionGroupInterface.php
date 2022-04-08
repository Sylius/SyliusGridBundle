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

namespace Sylius\Bundle\GridBundle\Builder\ActionGroup;

use Sylius\Bundle\GridBundle\Builder\Action\ActionInterface;

interface ActionGroupInterface
{
    public const MAIN_GROUP = 'main';

    public const ITEM_GROUP = 'item';

    public const SUB_ITEM_GROUP = 'subitem';

    public const BULK_GROUP = 'bulk';

    public static function create(string $name, ActionInterface ...$actions): self;

    public function getName(): string;

    public function addAction(ActionInterface $action): self;

    public function removeAction(string $name): self;

    public function toArray(): array;
}
