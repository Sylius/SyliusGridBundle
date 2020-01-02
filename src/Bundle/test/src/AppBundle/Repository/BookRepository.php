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

namespace AppBundle\Repository;

use AppBundle\Entity\Author;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class BookRepository extends EntityRepository
{
    public function createListQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.attributes', 'size', Join::WITH, 'size.code = :sizeCode')
            ->leftJoin('b.attributes', 'condition', Join::WITH, 'condition.code = :conditionCode')
            ->setParameter(':sizeCode', 'size')
            ->setParameter(':conditionCode', 'condition');
    }

    public function createAmericanBooksQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.author', 'author')
            ->innerJoin('author.nationality', 'na')
            ->andWhere('na.name = :nationality')
            ->setParameter(':nationality', 'American')
        ;
    }

    public function createEnglishBooksQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('b')
            ->innerJoin(Author::class, 'author', Join::WITH, 'author.id = b.author')
            ->innerJoin('author.nationality', 'na')
            ->andWhere('na.name = :nationality')
            ->setParameter(':nationality', 'English')
            ;
    }
}
