<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\GridBundle\Builder\Field;

final class TwigField
{
    public static function create(string $name, string $template): FieldInterface
    {
        return Field::create($name, 'twig')
            ->setOptions(['template' => $template])
        ;
    }
}
