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
use Pagerfanta\Doctrine\DBAL\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\GridBundle\Doctrine\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Parameters;

final class DataSource implements DataSourceInterface
{
    private QueryBuilder $queryBuilder;

    private ExpressionBuilderInterface $expressionBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->expressionBuilder = new ExpressionBuilder($queryBuilder);
    }

    public function restrict($expression, string $condition = DataSourceInterface::CONDITION_AND): void
    {
        switch ($condition) {
            case DataSourceInterface::CONDITION_AND:
                $this->queryBuilder->andWhere($expression);

                break;
            case DataSourceInterface::CONDITION_OR:
                $this->queryBuilder->orWhere($expression);

                break;
        }
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function getExpressionBuilder(): ExpressionBuilderInterface
    {
        return $this->expressionBuilder;
    }

    public function getData(Parameters $parameters)
    {
        if (!class_exists(QueryAdapter::class)) {
            throw new \LogicException('Pagerfanta DBAL adapter is not available. Try running "composer require pagerfanta/doctrine-dbal-adapter".');
        }

        $page = (int) $parameters->get('page', 1);

        $countQueryBuilderModifier = function (QueryBuilder $queryBuilder): void {
            $queryBuilder
                ->select('COUNT(DISTINCT o.id) AS total_results')
                ->setMaxResults(1)
            ;
        };

        $paginator = new Pagerfanta(new QueryAdapter($this->queryBuilder, $countQueryBuilderModifier));
        $paginator->setNormalizeOutOfRangePages(true);
        $paginator->setCurrentPage($page > 0 ? $page : 1);

        return $paginator;
    }
}
