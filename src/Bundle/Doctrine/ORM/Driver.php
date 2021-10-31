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

namespace Sylius\Bundle\GridBundle\Doctrine\ORM;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\DriverInterface;
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
            throw new \InvalidArgumentException('"class" must be configured.');
        }

        /** @var ObjectManager $manager */
        $manager = $this->managerRegistry->getManagerForClass($configuration['class']);

        /** @var EntityRepository $repository */
        $repository = $manager->getRepository($configuration['class']);

        $fetchJoinCollection = $configuration['pagination']['fetch_join_collection'] ?? true;
        $useOutputWalkers = $configuration['pagination']['use_output_walkers'] ?? true;

        if (!isset($configuration['repository']['method'])) {
            return new DataSource($repository->createQueryBuilder('o'), $fetchJoinCollection, $useOutputWalkers);
        }

        $arguments = isset($configuration['repository']['arguments']) ? array_values($configuration['repository']['arguments']) : [];
        $method = $configuration['repository']['method'];
        if (is_array($method) && 2 === count($method)) {
            $queryBuilder = $method[0];
            $method = $method[1];

            return new DataSource($queryBuilder->$method(...$arguments), $fetchJoinCollection, $useOutputWalkers);
        }

        return new DataSource($repository->$method(...$arguments), $fetchJoinCollection, $useOutputWalkers);
    }
}
