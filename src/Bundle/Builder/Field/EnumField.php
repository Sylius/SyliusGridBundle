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

namespace Sylius\Bundle\GridBundle\Builder\Field;

use Sylius\Component\Grid\Builder\Field\FieldInterface;

final class EnumField
{
    public static function create(string $name): FieldInterface
    {
        return Field::create($name, 'enum');
    }
}
