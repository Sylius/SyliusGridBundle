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

\trigger_deprecation('sylius/grid', '1.8', '%s is deprecated, replace it with your own implementation.', MoneyFilter::class);

final class MoneyFilter implements FilterInterface
{
    public const DEFAULT_SCALE = 2;

    /**
     * @param array{
     *     field?: string,
     *     scale?: int,
     *     currency_field: string,
     * } $options
     * @param array{
     *     greaterThan?: string|float,
     *     lessThan?: string|float,
     *     currency?: string,
     * }|empty $data
     */
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (empty($data)) {
            return;
        }

        $field = $options['field'] ?? $name;
        $scale = (int) ($options['scale'] ?? self::DEFAULT_SCALE);

        $greaterThan = $data['greaterThan'] ?? '';
        $lessThan = $data['lessThan'] ?? '';

        $expressionBuilder = $dataSource->getExpressionBuilder();

        if (!empty($data['currency'])) {
            $currencyField = $options['currency_field'];
            $dataSource->restrict($expressionBuilder->equals($currencyField, $data['currency']));
        }
        if ('' !== $greaterThan) {
            $dataSource->restrict($expressionBuilder->greaterThan($field, $this->normalizeAmount((float) $greaterThan, $scale)));
        }
        if ('' !== $lessThan) {
            $dataSource->restrict($expressionBuilder->lessThan($field, $this->normalizeAmount((float) $lessThan, $scale)));
        }
    }

    private function normalizeAmount(float $amount, int $scale): int
    {
        return (int) round($amount * (10 ** $scale));
    }
}
