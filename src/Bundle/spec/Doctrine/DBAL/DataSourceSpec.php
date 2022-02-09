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

namespace spec\Sylius\Bundle\GridBundle\Doctrine\DBAL;

use Doctrine\DBAL\Query;
use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Parameters;

final class DataSourceSpec extends ObjectBehavior
{
    function let(QueryBuilder $queryBuilder): void
    {
        $this->beConstructedWith($queryBuilder);
    }

    function it_implements_data_source(): void
    {
        $this->shouldImplement(DataSourceInterface::class);
    }

    function it_gets_the_data(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->getType()->willReturn(QueryBuilder::SELECT);

        $data = $this->getData(new Parameters(['page' => '1']));

        $data->shouldHaveType(Pagerfanta::class);
        $data->getCurrentPage()->shouldReturn(1);
        $data->getNormalizeOutOfRangePages()->shouldReturn(true);
    }
}
