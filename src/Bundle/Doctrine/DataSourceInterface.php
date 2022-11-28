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

namespace Sylius\Bundle\GridBundle\Doctrine;

use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder as ODMQueryBuilder;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;
use Sylius\Component\Grid\Data\DataSourceInterface as BaseDataSourceInterface;

interface DataSourceInterface extends BaseDataSourceInterface
{
    public function getQueryBuilder(): ORMQueryBuilder|DBALQueryBuilder|ODMQueryBuilder;
}
