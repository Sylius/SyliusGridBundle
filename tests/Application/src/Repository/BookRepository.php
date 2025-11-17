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

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class BookRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $managerRegistry,
    ) {
        parent::__construct($managerRegistry, Book::class);
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
            ->innerJoin('b.author', 'author')
            ->innerJoin('author.nationality', 'na')
            ->andWhere('na.name = :nationality')
            ->setParameter(':nationality', 'English')
        ;
    }
}
