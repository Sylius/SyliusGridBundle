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

namespace App\Entity;

use App\Enum\AdminUserStatusEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\Table(name: 'app_admin_user')]
#[ORM\Entity]
class AdminUser
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $username = null;

    #[ORM\Column(enumType: AdminUserStatusEnum::class)]
    private AdminUserStatusEnum $status = AdminUserStatusEnum::Active;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getStatus(): AdminUserStatusEnum
    {
        return $this->status;
    }

    public function setStatus(AdminUserStatusEnum $status): void
    {
        $this->status = $status;
    }
}
