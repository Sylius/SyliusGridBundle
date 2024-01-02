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

namespace Sylius\Bundle\GridBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Parameters;

final class DataSource implements DataSourceInterface
{
    private QueryBuilder $queryBuilder;

    private ExpressionBuilderInterface $expressionBuilder;

    private bool $fetchJoinCollection;

    private bool $useOutputWalkers;

    /**
     * @param bool $fetchJoinCollection must be 'true' when the query fetch-joins a to-many collection,
     *                                  otherwise the pagination will yield incorrect results
     *                                  https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/tutorials/pagination.html
     * @param bool $useOutputWalkers must be 'true' if the query has an order by statement for a field from
     *                                the to-many association, otherwise it will throw an exception
     *                                might greatly affect the performance (https://github.com/Sylius/Sylius/issues/3775)
     */
    public function __construct(QueryBuilder $queryBuilder, bool $fetchJoinCollection, bool $useOutputWalkers)
    {
        $this->queryBuilder = $queryBuilder;
        $this->expressionBuilder = new ExpressionBuilder($queryBuilder);
        $this->fetchJoinCollection = $fetchJoinCollection;
        $this->useOutputWalkers = $useOutputWalkers;
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

    public function getExpressionBuilder(): ExpressionBuilderInterface
    {
        return $this->expressionBuilder;
    }

    public function getData(Parameters $parameters)
    {
        if (!class_exists(QueryAdapter::class)) {
            throw new \LogicException('Pagerfanta ORM adapter is not available. Try running "composer require pagerfanta/doctrine-orm-adapter".');
        }

        $page = (int) $parameters->get('page', 1);

        $paginator = new Pagerfanta(
            new QueryAdapter($this->queryBuilder, $this->fetchJoinCollection, $this->useOutputWalkers),
        );
        $paginator->setNormalizeOutOfRangePages(true);
        $paginator->setCurrentPage($page > 0 ? $page : 1);

        return $paginator;
    }
}
