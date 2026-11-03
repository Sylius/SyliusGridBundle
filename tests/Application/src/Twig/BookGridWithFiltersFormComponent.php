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

use Sylius\Component\Grid\Twig\Component\ComponentWithGridFiltersFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(
    name: 'book_grid_with_filters_form',
    template: 'shared/component/grid.html.twig',
)]
final class BookGridWithFiltersFormComponent
{
    use DefaultActionTrait;
    use ComponentWithGridFiltersFormTrait;

    #[LiveProp(writable: true)]
    public bool $withFilters = false;

    protected function getGridName(): string
    {
        return 'app_book';
    }
}
