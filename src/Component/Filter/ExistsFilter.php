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

final class ExistsFilter implements FilterInterface
{
    public const TRUE = true;

    public const FALSE = false;

    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (null === $data) {
            return;
        }

        $field = $options['field'] ?? $name;

        if (self::TRUE === (bool) $data) {
            $dataSource->restrict($dataSource->getExpressionBuilder()->isNotNull($field));

            return;
        }

        $dataSource->restrict($dataSource->getExpressionBuilder()->isNull($field));
    }
}
