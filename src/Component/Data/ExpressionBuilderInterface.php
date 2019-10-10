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

namespace Sylius\Component\Grid\Data;

interface ExpressionBuilderInterface
{
    /**
     * @param mixed ...$expressions
     *
     * @return mixed
     */
    public function andX(...$expressions);

    /**
     * @param mixed ...$expressions
     *
     * @return mixed
     */
    public function orX(...$expressions);

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function comparison(string $field, string $operator, $value);

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function equals(string $field, $value);

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function notEquals(string $field, $value);

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function lessThan(string $field, $value);

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function lessThanOrEqual(string $field, $value);

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function greaterThan(string $field, $value);

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function greaterThanOrEqual(string $field, $value);

    /**
     * @return mixed
     */
    public function in(string $field, array $values);

    /**
     * @return mixed
     */
    public function notIn(string $field, array $values);

    /**
     * @return mixed
     */
    public function isNull(string $field);

    /**
     * @return mixed
     */
    public function isNotNull(string $field);

    /**
     * @return mixed
     */
    public function like(string $field, string $pattern);

    /**
     * @return mixed
     */
    public function notLike(string $field, string $pattern);

    /**
     * @return mixed
     */
    public function orderBy(string $field, string $direction);

    /**
     * @return mixed
     */
    public function addOrderBy(string $field, string $direction);
}
