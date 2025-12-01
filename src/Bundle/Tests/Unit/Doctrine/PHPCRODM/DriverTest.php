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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Doctrine\PHPCRODM;

use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\ODM\PHPCR\DocumentRepository;
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\DataSource;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\Driver;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Parameters;

/**
 * @require Doctrine\ODM\PHPCR\DocumentManagerInterface
 */
final class DriverTest extends TestCase
{
    public function testImplementsGridDriver(): void
    {
        $this->skipIfNecessary();

        $documentManager = $this->createMock(DocumentManagerInterface::class);
        $driver = new Driver($documentManager);

        $this->assertInstanceOf(DriverInterface::class, $driver);
    }

    public function testThrowsExceptionIfClassIsUndefined(): void
    {
        $this->skipIfNecessary();

        $documentManager = $this->createMock(DocumentManagerInterface::class);
        $driver = new Driver($documentManager);

        $this->expectException(\InvalidArgumentException::class);

        $driver->getDataSource([], new Parameters());
    }

    public function testCreatesDataSourceViaDoctrinePhpcrodmQueryBuilder(): void
    {
        $this->skipIfNecessary();

        $documentManager = $this->createMock(DocumentManagerInterface::class);
        $documentRepository = $this->createMock(DocumentRepository::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $documentManager->expects($this->once())->method('getRepository')->with('App:Book')->willReturn($documentRepository);
        $documentRepository->expects($this->once())->method('createQueryBuilder')->with('o')->willReturn($queryBuilder);

        $driver = new Driver($documentManager);
        $dataSource = $driver->getDataSource(['class' => 'App:Book'], new Parameters());

        $this->assertInstanceOf(DataSource::class, $dataSource);
    }

    private function skipIfNecessary(): void
    {
        if (class_exists(DocumentManagerInterface::class)) {
            return;
        }

        $this->markTestSkipped(message: sprintf('Skipped since: %s is not available', DocumentManagerInterface::class));
    }
}
