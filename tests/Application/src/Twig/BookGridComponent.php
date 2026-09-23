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

use Sylius\Component\Grid\Twig\Component\ComponentWithGridTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(
    name: 'book_grid',
    template: 'shared/component/grid.html.twig',
)]
final class BookGridComponent
{
    use DefaultActionTrait;
    use ComponentWithGridTrait;

    #[LiveProp(writable: true)]
    public bool $withFilters = true;

    protected function getGridName(): string
    {
        return 'app_book';
    }
}
