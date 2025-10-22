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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Doctrine\ORM\DataSource;
use Sylius\Bundle\GridBundle\Doctrine\ORM\Driver;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Parameters;

final class DriverTest extends TestCase
{
    public function testImplementsGridDriver(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $driver = new Driver($managerRegistry);

        $this->assertInstanceOf(DriverInterface::class, $driver);
    }

    public function testThrowsExceptionIfClassIsUndefined(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $driver = new Driver($managerRegistry);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing configuration: when using the ORM driver for a grid, you must define the "class" option.');

        $driver->getDataSource([], new Parameters());
    }

    public function testCreatesDataSourceViaDoctrineOrmQueryBuilder(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityRepository = $this->createMock(EntityRepository::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $managerRegistry->expects($this->once())->method('getManagerForClass')->with('App:Book')->willReturn($entityManager);
        $entityManager->expects($this->once())->method('getRepository')->with('App:Book')->willReturn($entityRepository);
        $entityRepository->expects($this->once())->method('createQueryBuilder')->with('o')->willReturn($queryBuilder);

        $driver = new Driver($managerRegistry);
        $dataSource = $driver->getDataSource(['class' => 'App:Book'], new Parameters());

        $this->assertInstanceOf(DataSource::class, $dataSource);
    }
}
