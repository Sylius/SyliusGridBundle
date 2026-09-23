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

namespace App\Twig;

use Sylius\Component\Grid\Twig\Component\ComponentWithEntityFilterTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'entity_filter', template: 'shared/component/grid/filter/entity.html.twig')]
final class EntityFilterComponent
{
    use DefaultActionTrait;
    use ComponentWithEntityFilterTrait;
}
