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

namespace Sylius\Bundle\GridBundle\Doctrine\ORM;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Exception\RuntimeException;
use Sylius\Component\Grid\Parameters;

final class Driver implements DriverInterface
{
    public const NAME = 'doctrine/orm';

    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function getDataSource(array $configuration, Parameters $parameters): DataSourceInterface
    {
        if (!array_key_exists('class', $configuration)) {
            throw new \InvalidArgumentException('Missing configuration: when using the ORM driver for a grid, you must define the "class" option.');
        }

        /** @var class-string $class */
        $class = $configuration['class'];

        $manager = $this->managerRegistry->getManagerForClass($class);

        if (null === $manager) {
            throw new RuntimeException(sprintf('Doctrine ORM manager for class "%s" not found.', $class));
        }

        /** @var EntityRepository<object> $repository */
        $repository = $manager->getRepository($class);

        /** @var bool $fetchJoinCollection */
        $fetchJoinCollection = $configuration['pagination']['fetch_join_collection'] ?? true;
        /** @var bool $useOutputWalkers */
        $useOutputWalkers = $configuration['pagination']['use_output_walkers'] ?? true;

        if (!isset($configuration['repository']['method'])) {
            return new DataSource($repository->createQueryBuilder('o'), $fetchJoinCollection, $useOutputWalkers);
        }

        /** @var array<int|string, mixed> $repositoryArguments */
        $repositoryArguments = $configuration['repository']['arguments'] ?? [];
        $arguments = array_values($repositoryArguments);
        $method = $configuration['repository']['method'];
        if (is_array($method) && 2 === count($method)) {
            /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
            $queryBuilder = $method[0];
            /** @var string $method */
            $method = $method[1];

            /** @var \Doctrine\ORM\QueryBuilder $resultQueryBuilder */
            $resultQueryBuilder = $queryBuilder->$method(...$arguments);
            return new DataSource($resultQueryBuilder, $fetchJoinCollection, $useOutputWalkers);
        }

        /** @var \Doctrine\ORM\QueryBuilder $resultQueryBuilder */
        $resultQueryBuilder = $repository->$method(...$arguments);
        return new DataSource($resultQueryBuilder, $fetchJoinCollection, $useOutputWalkers);
    }
}
