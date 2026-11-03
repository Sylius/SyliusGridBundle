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

use Sylius\Component\Grid\Twig\Component\ComponentWithFilterTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

/**
 * A filter rendered as its own live component, whose value is the one of its form (e.g. a select or an entity filter).
 */
#[AsLiveComponent(name: 'filter', template: 'shared/component/grid/filter/filter.html.twig')]
final class FilterComponent
{
    use DefaultActionTrait;
    use ComponentWithFilterTrait;

    #[LiveProp(writable: true)]
    public mixed $value = null;

    /**
     * Higher priority than ComponentWithFormTrait::initializeForm() so the form is built with the mounted value.
     */
    #[PostMount(priority: 10)]
    public function initializeFilterValue(): void
    {
        $this->value = $this->getFilterCriteria();
    }

    protected function reset(): void
    {
        $this->value = null;
    }

    protected function toArray(): array
    {
        return [
            $this->filter->getName() => $this->value,
        ];
    }
}
