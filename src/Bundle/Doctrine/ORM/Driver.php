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

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Psr\Container\ContainerInterface;
use Sylius\Bundle\GridBundle\Doctrine\ORM\DataSource;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Parameters;

final class Driver implements DriverInterface
{
    public const NAME = 'doctrine/orm';

    /** @var RegistryInterface */
    protected $resourceRegistry;

    /** @var ContainerInterface */
    private $container;

    /** @var ManagerRegistry */
    private $managerRegistry;

    public function __construct(
        RegistryInterface $resourceRegistry,
        ContainerInterface $container,
        ManagerRegistry $managerRegistry
    ) {
        $this->resourceRegistry = $resourceRegistry;
        $this->container = $container;
        $this->managerRegistry = $managerRegistry;
    }

    public function getDataSource(array $configuration, Parameters $parameters): DataSourceInterface
    {
        if (!array_key_exists('class', $configuration)) {
            throw new \InvalidArgumentException('"class" must be configured.');
        }

        try {
            $resourceMetadata = $this->resourceRegistry->getByClass($configuration['class']);
            $repositoryServiceId = $resourceMetadata->getServiceId('repository');
            $repository = $this->container->get($repositoryServiceId);
        } catch (\InvalidArgumentException $exception) {
            /** @var ObjectManager $manager */
            $manager = $this->managerRegistry->getManagerForClass($configuration['class']);

            /** @var EntityRepository $repository */
            $repository = $manager->getRepository($configuration['class']);
        }

        if (!isset($configuration['repository']['method'])) {
            return new DataSource($repository->createQueryBuilder('o'));
        }

        $arguments = isset($configuration['repository']['arguments']) ? array_values(
            $configuration['repository']['arguments']
        ) : [];
        $method = $configuration['repository']['method'];
        if (is_array($method) && 2 === count($method)) {
            $queryBuilder = $method[0];
            $method = $method[1];

            return new DataSource($queryBuilder->$method(...$arguments));
        }

        return new DataSource($repository->$method(...$arguments));
    }
}
