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

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class BookRepository extends EntityRepository
{
    public function createAmericanBooksQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.author', 'author')
            ->innerJoin('author.nationality', 'na')
            ->andWhere('na.name = :nationality')
            ->setParameter(':nationality', 'American')
        ;
    }
}
