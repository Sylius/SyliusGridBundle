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
use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\Query\Expr\Join;
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
        $entityManager = $this->queryBuilder->getEntityManager();

        $field = $this->getAbsolutePath($field);
        $rootField = explode('.', $field, 2)[0];

        $metadata = null;

        /** @var From[] $froms */
        $froms = $this->queryBuilder->getDQLPart('from');
        foreach ($froms as $from) {
            if ($from->getAlias() === $rootField) {
                $metadata = $entityManager->getClassMetadata($from->getFrom());
            }
        }

        if ($metadata === null) {
            throw new \RuntimeException(sprintf('Could not get metadata for "%s".', $field));
        }

        $explodedField = explode('.', $field, 3);
        while (count($explodedField) === 3) {
            if (isset($metadata->embeddedClasses[$explodedField[1]])) {
                return implode('.', $explodedField);
            }

            $metadata = $entityManager->getClassMetadata($metadata->getAssociationMapping($explodedField[1])['targetEntity']);
            $relatedField = sprintf('%s.%s', $explodedField[0], $explodedField[1]);

            /** @var Join[] $joins */
            $joins = array_merge([], ...array_values($this->queryBuilder->getDQLPart('join')));
            foreach ($joins as $join) {
                if ($join->getJoin() === $relatedField) {
                    unset($explodedField[0]);
                    $explodedField[1] = $join->getAlias();
                    $explodedField = explode('.', implode('.', $explodedField), 3);

                    continue 2;
                }
            }

            $this->queryBuilder->innerJoin($relatedField, md5($relatedField));
            unset($explodedField[0]);
            $explodedField[1] = md5($relatedField);
            $explodedField = explode('.', implode('.', $explodedField), 3);
        }

        return implode('.', $explodedField);
    }

    /**
     * This method returns an absolute path of a property path.
     *
     * Given the following query:
     *
     * SELECT bo FROM Book bo INNER JOIN Author au ON bo.author_id = au.id
     *
     * It will behave as follows:
     *
     * book.title => book.title
     * title => book.title
     * au => book.author
     * au.name => book.author.name
     */
    private function getAbsolutePath(string $field): string
    {
        $explodedField = explode('.', $field);

        if (!in_array($explodedField[0], $this->queryBuilder->getAllAliases(), true)) {
            $field = sprintf('%s.%s', $this->getRootAlias(), $field);

            $explodedField = explode('.', $field);
        }

        /** @var Join[] $joins */
        $joins = array_merge([], ...array_values($this->queryBuilder->getDQLPart('join')));
        while (!in_array($explodedField[0], $this->queryBuilder->getRootAliases(), true)) {
            foreach ($joins as $join) {
                if ($join->getAlias() === $explodedField[0]) {
                    $explodedField[0] = $join->getJoin();
                    $field = implode('.', $explodedField);
                    $explodedField = explode('.', $field);

                    continue 2;
                }
            }

            throw new \RuntimeException(sprintf('Could not get mapping for "%s".', $field));
        }

        return $field;
    }
}
