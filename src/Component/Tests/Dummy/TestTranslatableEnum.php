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

namespace Sylius\Component\Grid\Tests\Dummy;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum TestTranslatableEnum: string implements TranslatableInterface
{
    case A = 'a';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $this->value . '.translation';
    }
}
