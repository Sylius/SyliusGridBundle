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

final class CallableField
{
    public static function create(string $name, callable $callable, bool $htmlspecialchars = true): FieldInterface
    {
        return Field::create($name, 'callable')
            ->setOption('callable', $callable)
            ->setOption('htmlspecialchars', $htmlspecialchars)
        ;
    }

    public static function createForService(string $name, string $service, ?string $method = null, bool $htmlspecialchars = true): FieldInterface
    {
        $field = Field::create($name, 'callable')
            ->setOption('service', $service)
            ->setOption('htmlspecialchars', $htmlspecialchars)
        ;

        if ($method !== null) {
            $field->setOption('method', $method);
        }

        return $field;
    }
}
