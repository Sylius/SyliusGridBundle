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

namespace App\Helper;

use Sylius\Component\Grid\Annotation\AsGridFieldCallableService;

#[AsGridFieldCallableService]
final class GridHelper
{
    public function __invoke(?string $value): string
    {
        return $this->formatNationality($value);
    }

    public function formatNationality(?string $value): string
    {
        return match ($value) {
            'English' => 'EN',
            'American' => 'US',
            null => '',
        };
    }

    public static function addHashPrefix(string $value): string
    {
        return '#' . $value;
    }
}
