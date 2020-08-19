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

final class SelectFilter implements FilterInterface
{
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (
            (isset($options['falsy_values']) && !\in_array($data, $options['falsy_values'], true)) ||
            (!isset($options['falsy_values']) && empty($data))
        ) {
            return;
        }

        $field = $options['field'] ?? $name;

        if (\is_array($data)) {
            $dataSource->restrict($dataSource->getExpressionBuilder()->in($field, $data));

            return;
        }

        $dataSource->restrict($dataSource->getExpressionBuilder()->equals($field, $data));
    }
}
