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

namespace Sylius\Component\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class NumericRangeFilter implements FilterInterface
{
    public const DEFAULT_SCALE = 0;

    public const DEFAULT_ROUNDING_MODE = \NumberFormatter::ROUND_HALFUP;

    public const DEFAULT_INCLUSIVE_FROM = true;

    public const DEFAULT_INCLUSIVE_TO = true;

    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (empty($data)) {
            return;
        }

        $field = (string) ($options['field'] ?? $name);
        $scale = (int) ($options['scale'] ?? self::DEFAULT_SCALE);
        $mode = (int) ($options['rounding_mode'] ?? self::DEFAULT_ROUNDING_MODE);

        $greaterThan = $this->getDataValue($data, 'greaterThan');
        $lessThan = $this->getDataValue($data, 'lessThan');

        $expressionBuilder = $dataSource->getExpressionBuilder();

        if ('' !== $greaterThan) {
            $inclusive = (bool) ($options['inclusive_from'] ?? self::DEFAULT_INCLUSIVE_FROM);
            $amount = $this->normalizeAmount((float) $greaterThan, $scale, $mode);

            if ($inclusive) {
                $dataSource->restrict($expressionBuilder->greaterThanOrEqual($field, $amount));
            } else {
                $dataSource->restrict($expressionBuilder->greaterThan($field, $amount));
            }
        }

        if ('' !== $lessThan) {
            $inclusive = (bool) ($options['inclusive_to'] ?? self::DEFAULT_INCLUSIVE_TO);
            $amount = $this->normalizeAmount((float) $lessThan, $scale, $mode);

            if ($inclusive) {
                $dataSource->restrict($expressionBuilder->lessThanOrEqual($field, $amount));
            } else {
                $dataSource->restrict($expressionBuilder->lessThan($field, $amount));
            }
        }
    }

    private function normalizeAmount(float $amount, int $scale, int $mode): int
    {
        return (int) round($amount * (10 ** $scale), $mode);
    }

    /** @param array<array-key, string> $data */
    private function getDataValue(array $data, string $key): string
    {
        return $data[$key] ?? '';
    }
}
