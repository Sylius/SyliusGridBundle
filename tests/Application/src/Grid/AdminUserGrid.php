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

namespace App\Grid;

use App\Entity\AdminUser;
use App\Enum\AdminUserStatusEnum;
use Sylius\Bundle\GridBundle\Builder\Field\EnumField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\EnumFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(resourceClass: AdminUser::class, name: 'app_admin_user')]
final class AdminUserGrid extends AbstractGrid
{
    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addFilter(Filter::create('name', 'string'))
            ->addFilter(EnumFilter::create('status', AdminUserStatusEnum::class))
            ->addField(
                StringField::create('username')
                    ->setLabel('Username')
                    ->setSortable(true),
            )
            ->addField(
                EnumField::create('status')
                    ->setLabel('Status'),
            )
            ->setLimits([10, 5, 15, 100])
        ;
    }
}
