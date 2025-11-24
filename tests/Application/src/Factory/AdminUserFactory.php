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

use App\Entity\AdminUser;
use App\Enum\AdminUserStatusEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<AdminUser>
 */
final class AdminUserFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return AdminUser::class;
    }

    public function active(): self
    {
        return $this->with(['status' => AdminUserStatusEnum::Active]);
    }

    public function inactive(): self
    {
        return $this->with(['status' => AdminUserStatusEnum::Inactive]);
    }

    public function banned(): self
    {
        return $this->with(['status' => AdminUserStatusEnum::Banned]);
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'status' => self::faker()->randomElement(AdminUserStatusEnum::cases()),
            'username' => self::faker()->userName(),
        ];
    }
}
