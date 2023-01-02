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

namespace Sylius\Bundle\GridBundle\Doctrine\DBAL;

use Doctrine\DBAL\Query\QueryBuilder;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;

final class ExpressionBuilder implements ExpressionBuilderInterface
{
    private QueryBuilder $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function andX(...$expressions)
    {
        return $this->queryBuilder->expr()->andX(...$expressions);
    }

    public function orX(...$expressions)
    {
        return $this->queryBuilder->expr()->orX(...$expressions);
    }

    public function comparison(string $field, string $operator, $value)
    {
        return $this->queryBuilder->expr()->comparison($field, $operator, $value);
    }

    public function equals(string $field, $value)
    {
        $this->queryBuilder->setParameter($field, $value);

        return $this->queryBuilder->expr()->eq($field, ':' . $field);
    }

    public function notEquals(string $field, $value)
    {
        $this->queryBuilder->setParameter($field, $value);

        return $this->queryBuilder->expr()->neq($field, ':' . $field);
    }

    public function lessThan(string $field, $value)
    {
        $this->queryBuilder->setParameter($field, $value);

        return $this->queryBuilder->expr()->lt($field, ':' . $field);
    }

    public function lessThanOrEqual(string $field, $value)
    {
        $this->queryBuilder->setParameter($field, $value);

        return $this->queryBuilder->expr()->lte($field, ':' . $field);
    }

    public function greaterThan(string $field, $value)
    {
        $this->queryBuilder->setParameter($field, $value);

        return $this->queryBuilder->expr()->gt($field, ':' . $field);
    }

    public function greaterThanOrEqual(string $field, $value)
    {
        $this->queryBuilder->setParameter($field, $value);

        return $this->queryBuilder->expr()->gte($field, ':' . $field);
    }

    public function in(string $field, array $values)
    {
        return $this->queryBuilder->expr()->in($field, $values);
    }

    public function notIn(string $field, array $values)
    {
        return $this->queryBuilder->expr()->notIn($field, $values);
    }

    public function isNull(string $field)
    {
        return $this->queryBuilder->expr()->isNull($field);
    }

    public function isNotNull(string $field)
    {
        return $this->queryBuilder->expr()->isNotNull($field);
    }

    public function like(string $field, string $pattern)
    {
        return $this->queryBuilder->expr()->like($field, $this->queryBuilder->expr()->literal($pattern));
    }

    public function notLike(string $field, string $pattern)
    {
        return $this->queryBuilder->expr()->notLike($field, $this->queryBuilder->expr()->literal($pattern));
    }

    public function orderBy(string $field, string $direction)
    {
        return $this->queryBuilder->orderBy($field, $direction);
    }

    public function addOrderBy(string $field, string $direction)
    {
        return $this->queryBuilder->addOrderBy($field, $direction);
    }
}
