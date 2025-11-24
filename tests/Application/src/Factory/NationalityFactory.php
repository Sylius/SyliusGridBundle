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

use App\Entity\Nationality;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Nationality>
 */
final class NationalityFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Nationality::class;
    }

    public function withName(string $name): self
    {
        return $this->with(['name' => $name]);
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->text(255),
        ];
    }
}
