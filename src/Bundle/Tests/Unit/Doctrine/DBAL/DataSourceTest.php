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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Doctrine\DBAL;

use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Doctrine\DBAL\DataSource;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Parameters;

final class DataSourceTest extends TestCase
{
    public function testImplementsDataSource(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $dataSource = new DataSource($queryBuilder);

        $this->assertInstanceOf(DataSourceInterface::class, $dataSource);
    }

    public function testGetsTheData(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $dataSource = new DataSource($queryBuilder);
        $data = $dataSource->getData(new Parameters(['page' => '1']));

        $this->assertInstanceOf(Pagerfanta::class, $data);
        $this->assertEquals(1, $data->getCurrentPage());
        $this->assertTrue($data->getNormalizeOutOfRangePages());
    }
}
