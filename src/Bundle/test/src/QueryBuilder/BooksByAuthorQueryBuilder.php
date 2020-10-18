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

namespace App\QueryBuilder;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class BooksByAuthorQueryBuilder
{
    /** @var EntityRepository */
    private $authorRepository;

    public function __construct(EntityRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function create(): QueryBuilder
    {
        return $this->authorRepository->createQueryBuilder('a')
            ->addSelect('book')
            ->innerJoin('a.books', 'book')
        ;
    }
}
