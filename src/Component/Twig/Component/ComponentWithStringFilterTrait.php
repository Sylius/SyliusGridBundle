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
trait ComponentWithStringFilterTrait
{
    use ComponentWithFilterTrait;

    #[LiveProp(writable: true)]
    public string $value = '';

    /**
     * Null means "not chosen": the StringFilter then falls back on the "type" option or on "contains".
     */
    #[LiveProp(writable: true)]
    public ?string $type = null;

    /**
     * Higher priority than ComponentWithFormTrait::initializeForm() so the form is built with the mounted value.
     */
    #[PostMount(priority: 10)]
    public function initializeFilterValue(): void
    {
        /** @var array{value?: string|null, type?: string|null} $criteria */
        $criteria = $this->criteria[$this->filter->getName()] ?? [];

        $this->value = (string) ($criteria['value'] ?? '');
        $this->type = $criteria['type'] ?? null;
    }

    protected function reset(): void
    {
        $this->value = '';
        $this->type = null;
    }

    protected function toArray(): array
    {
        $criteria = ['value' => $this->value];

        if (null !== $this->type && '' !== $this->type) {
            $criteria['type'] = $this->type;
        }

        return [
            $this->filter->getName() => $criteria,
        ];
    }
}
