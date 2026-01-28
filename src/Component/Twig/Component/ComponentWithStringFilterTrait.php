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

    #[LiveProp(writable: true)]
    public string $type = 'contains';

    #[PostMount]
    public function postMount(): void
    {
        $this->value = $this->criteria[$this->filter->getName()]['value'] ?? '';
        $this->type = $this->criteria[$this->filter->getName()]['type'] ?? '';
    }

    protected function reset(): void
    {
        $this->value = '';
        $this->type = 'contains';
    }

    protected function toArray(): array
    {
        return [
            $this->filter->getName() => [
                'value' => $this->value,
                'type' => $this->type,
            ],
        ];
    }
}
