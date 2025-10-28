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

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum AdminUserStatusEnum: string implements TranslatableInterface
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Banned = 'banned';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::Active => $translator->trans('enum.status.active', locale: $locale),
            self::Inactive => $translator->trans('enum.status.inactive', locale: $locale),
            self::Banned => $translator->trans('enum.status.banned', locale: $locale),
        };
    }
}
