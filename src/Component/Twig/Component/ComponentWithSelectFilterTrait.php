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

namespace Sylius\Component\Grid\Twig\Component;

use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\TwigComponent\Attribute\PostMount;

/**
 * @experimental
 */
trait ComponentWithSelectFilterTrait
{
    use ComponentWithFilterTrait;

    #[LiveProp(writable: true)]
    public mixed $value = null;

    /**
     * Higher priority than ComponentWithFormTrait::initializeForm() so the form is built with the mounted value.
     */
    #[PostMount(priority: 10)]
    public function initializeFilterValue(): void
    {
        $this->value = $this->criteria[$this->filter->getName()] ?? null;
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
