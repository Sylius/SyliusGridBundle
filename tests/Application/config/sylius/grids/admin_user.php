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

use App\Entity\AdminUser;
use App\Enum\AdminUserStatusEnum;
use Sylius\Bundle\GridBundle\Builder\Field\EnumField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\EnumFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(
        GridBuilder::create('app_admin_user', AdminUser::class)
        ->addFilter(StringFilter::create('username'))
        ->addFilter(EnumFilter::create('status', AdminUserStatusEnum::class, true))
        ->addField(
            StringField::create('username')
                ->setLabel('Username')
                ->setSortable(true),
        )
        ->addField(
            EnumField::create('status')
                ->setLabel('Status'),
        )
        ->setLimits([10, 5, 15, 100]),
    );
};
