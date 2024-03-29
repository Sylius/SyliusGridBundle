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

namespace Sylius\Component\Grid\Data;

use Sylius\Component\Grid\Parameters;

interface DataSourceInterface
{
    public const CONDITION_AND = 'and';

    public const CONDITION_OR = 'or';

    /**
     * @param mixed $expression
     */
    public function restrict($expression, string $condition = self::CONDITION_AND): void;

    public function getExpressionBuilder(): ExpressionBuilderInterface;

    /**
     * @return mixed
     */
    public function getData(Parameters $parameters);
}
