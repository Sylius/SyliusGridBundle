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

use App\Entity\Author;
use App\Entity\Nationality;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;

/**
 * @extends PersistentProxyObjectFactory<Author>
 */
final class AuthorFactory extends PersistentProxyObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Author::class;
    }

    public function withName(string $name): self
    {
        return $this->with(['name' => $name]);
    }

    public function withNationality(Proxy|Nationality $nationality): self
    {
        return $this->with(['nationality' => $nationality]);
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->firstName() . ' ' . self::faker()->lastName(),
        ];
    }
}
