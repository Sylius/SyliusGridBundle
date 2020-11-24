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

final class EntityFilter implements FilterInterface
{
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (empty($data)) {
            return;
        }

        $values = is_array($data) ? $data : [$data];
        $fields = $options['fields'] ?? [$name];

        $expressionBuilder = $dataSource->getExpressionBuilder();

        $expressions = [];
        foreach ($fields as $field) {
            foreach ($values as $value) {
                $expressions[] = $expressionBuilder->equals($field, $value);
            }
        }

        $dataSource->restrict($expressionBuilder->orX(...$expressions));
    }
}
