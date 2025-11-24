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

namespace Sylius\Bundle\GridBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Webmozart\Assert\Assert;

/**
 * @implements DataTransformerInterface<array<string, mixed>, array<string, mixed>>
 */
final class DateTimeFilterTransformer implements DataTransformerInterface
{
    /** @var array<string, array{hour: string, minute: string}> */
    private static array $defaultTime = [
        'from' => ['hour' => '00', 'minute' => '00'],
        'to' => ['hour' => '23', 'minute' => '59'],
    ];

    public function __construct(private readonly string $type)
    {
        Assert::oneOf($type, array_keys(self::$defaultTime));
    }

    public function transform(mixed $value): mixed
    {
        return $value;
    }

    public function reverseTransform(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        if (!isset($value['date']) || !is_array($value['date']) || !($value['date']['year'] ?? false)) {
            return $value;
        }

        if (!isset($value['time']) || !is_array($value['time'])) {
            return $value;
        }

        $value['time']['hour'] = $value['time']['hour'] === '' ? self::$defaultTime[$this->type]['hour'] : $value['time']['hour'];
        $value['time']['minute'] = $value['time']['minute'] === '' ? self::$defaultTime[$this->type]['minute'] : $value['time']['minute'];

        return $value;
    }
}
