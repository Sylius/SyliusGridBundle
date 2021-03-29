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

namespace Sylius\Component\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class RangeNumericalFilter implements FilterInterface
{
    public const DEFAULT_INCLUSIVE_FROM = true;

    public const DEFAULT_INCLUSIVE_TO = true;

    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        $expressionBuilder = $dataSource->getExpressionBuilder();

        $field = (string) $this->getOption($options, 'field', $name);
        $from = $data['from'] ?? null;

        if (false === empty($from)) {
            $dataSource->restrict(
                $expressionBuilder->greaterThanOrEqual($field, $from),
                DataSourceInterface::CONDITION_HAVING_AND
            );
        }

        $to = $data['to'] ?? null;

        if (false === empty($to)) {
            $dataSource->restrict(
                $expressionBuilder->lessThanOrEqual($field, $to),
                DataSourceInterface::CONDITION_HAVING_AND
            );
        }
    }

    private function getOption(array $options, string $name, $default)
    {
        return $options[$name] ?? $default;
    }
}
