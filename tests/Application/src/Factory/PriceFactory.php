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

namespace App\Factory;

use App\Entity\Price;
use Zenstruck\Foundry\ObjectFactory;

/**
 * @extends ObjectFactory<Price>
 */
final class PriceFactory extends ObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Price::class;
    }

    public function withAmount(int $amount): self
    {
        return $this->with(['amount' => $amount]);
    }

    public function withCurrencyCode(string $currencyCode): self
    {
        return $this->with(['currencyCode' => $currencyCode]);
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'amount' => self::faker()->randomNumber(),
            'currencyCode' => self::faker()->randomElement(['USD', 'EUR', 'GBP']),
        ];
    }
}
