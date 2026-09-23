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

use Sylius\Component\Grid\Twig\Component\ComponentWithStringFilterTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'string_filter', template: 'shared/component/grid/filter/string.html.twig')]
final class StringFilterComponent
{
    use DefaultActionTrait;
    use ComponentWithStringFilterTrait;
}
