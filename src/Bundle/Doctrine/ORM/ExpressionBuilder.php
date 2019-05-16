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

namespace Sylius\Bundle\GridBundle\Doctrine\ORM;

use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;

final class ExpressionBuilder implements ExpressionBuilderInterface
{
    /** @var QueryBuilder */
    private $queryBuilder;

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
        return new Comparison($field, $operator, $value);
    }

    public function equals(string $field, $value)
    {
        $field = $this->adjustField($field);
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        return $this->queryBuilder->expr()->eq($this->resolveFieldByAddingJoins($field), ':' . $parameterName);
    }

    public function notEquals(string $field, $value)
    {
        $field = $this->adjustField($field);
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        return $this->queryBuilder->expr()->neq($this->resolveFieldByAddingJoins($field), ':' . $parameterName);
    }

    public function lessThan(string $field, $value)
    {
        $field = $this->adjustField($field);
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        return $this->queryBuilder->expr()->lt($this->resolveFieldByAddingJoins($field), ':' . $parameterName);
    }

    public function lessThanOrEqual(string $field, $value)
    {
        $field = $this->adjustField($field);
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        return $this->queryBuilder->expr()->lte($this->resolveFieldByAddingJoins($field), ':' . $parameterName);
    }

    public function greaterThan(string $field, $value)
    {
        $field = $this->adjustField($field);
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        return $this->queryBuilder->expr()->gt($this->resolveFieldByAddingJoins($field), ':' . $parameterName);
    }

    public function greaterThanOrEqual(string $field, $value)
    {
        $field = $this->adjustField($field);
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        return $this->queryBuilder->expr()->gte($this->resolveFieldByAddingJoins($field), ':' . $parameterName);
    }

    public function in(string $field, array $values)
    {
        $field = $this->adjustField($field);

        return $this->queryBuilder->expr()->in($this->resolveFieldByAddingJoins($field), $values);
    }

    public function notIn(string $field, array $values)
    {
        $field = $this->adjustField($field);

        return $this->queryBuilder->expr()->notIn($this->resolveFieldByAddingJoins($field), $values);
    }

    public function isNull(string $field)
    {
        $field = $this->adjustField($field);

        return $this->queryBuilder->expr()->isNull($this->resolveFieldByAddingJoins($field));
    }

    public function isNotNull(string $field)
    {
        $field = $this->adjustField($field);

        return $this->queryBuilder->expr()->isNotNull($this->resolveFieldByAddingJoins($field));
    }

    public function like(string $field, string $pattern)
    {
        $field = $this->adjustField($field);

        return $this->queryBuilder->expr()->like($this->resolveFieldByAddingJoins($field), $this->queryBuilder->expr()->literal($pattern));
    }

    public function notLike(string $field, string $pattern)
    {
        $field = $this->adjustField($field);

        return $this->queryBuilder->expr()->notLike($this->resolveFieldByAddingJoins($field), $this->queryBuilder->expr()->literal($pattern));
    }

    public function orderBy(string $field, string $direction)
    {
        $field = $this->adjustField($field);

        return $this->queryBuilder->orderBy($this->resolveFieldByAddingJoins($field), $direction);
    }

    public function addOrderBy(string $field, string $direction)
    {
        $field = $this->adjustField($field);

        return $this->queryBuilder->addOrderBy($this->resolveFieldByAddingJoins($field), $direction);
    }

    private function getFieldName(string $field): string
    {
        if (false === strpos($field, '.')) {
            return $this->queryBuilder->getRootAliases()[0] . '.' . $field;
        }

        return $field;
    }

    private function getParameterName(string $field): string
    {
        $parameterName = str_replace('.', '_', $field);

        $i = 1;
        while ($this->hasParameterName($parameterName)) {
            $parameterName .= $i;
        }

        return $parameterName;
    }

    private function hasParameterName(string $parameterName): bool
    {
        return null !== $this->queryBuilder->getParameter($parameterName);
    }

    private function getRootAlias(): string
    {
        return $this->queryBuilder->getRootAliases()[0];
    }

    private function adjustField(string $field): string
    {
        $rootAlias = $this->getRootAlias();
        if (0 === strpos($field, $rootAlias . '.')) {
            return substr_replace($field, '', 0, strlen($rootAlias) + 1);
        }

        return $field;
    }

    private function resolveFieldByAddingJoins(string $field): string
    {
        if (0 === substr_count($field, '.')) {
            return $this->getFieldName($field);
        }

        $key = 0;
        $newAlias = $this->getAlias($key);
        $fields = explode('.', $field);
        foreach ($fields as $field) {
            if ($key === count($fields) - 1) {
                break;
            }

            $joinAlias = $newAlias;
            $newAlias = $this->getAlias(++$key);

            $this->queryBuilder->innerJoin(sprintf('%s.%s', $joinAlias, $field), $newAlias);
        }

        return sprintf('%s.%s', $newAlias, $fields[$key]);
    }

    private function getAlias(int $number): string
    {
        $rootAlias = $this->getRootAlias();
        $alias = $rootAlias . ($number === 0 ? '' : (string) $number);
        $joins = $this->queryBuilder->getDQLParts()['join'];

        if (empty($joins)) {
            return $alias;
        }

        foreach ($joins[$rootAlias] as $existentJoin) {
            if ($existentJoin->getAlias() === $alias) {
                return $this->getAlias($number + 1);
            }
        }

        return $alias;
    }
}
