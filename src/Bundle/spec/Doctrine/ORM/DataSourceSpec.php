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

namespace spec\Sylius\Bundle\GridBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Parameters;

final class DataSourceSpec extends ObjectBehavior
{
    function it_implements_data_source(QueryBuilder $queryBuilder): void
    {
        $this->beConstructedWith($queryBuilder, false, false);

        $this->shouldImplement(DataSourceInterface::class);
    }

    function it_gets_the_data(QueryBuilder $queryBuilder): void
    {
        $this->beConstructedWith($queryBuilder, false, false);

        $this->getData(new Parameters(['page' => '1']))->shouldHaveType(Pagerfanta::class);
    }
}
