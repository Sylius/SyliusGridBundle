<?php


declare(strict_types=1);

namespace App\BoardGameBlog\Infrastructure\Sylius\Grid\Provider;

use App\BoardGameBlog\Application\Query\FindBoardGamesQuery;
use App\BoardGameBlog\Domain\Model\BoardGame;
use App\BoardGameBlog\Domain\Repository\BoardGameRepositoryInterface;
use App\BoardGameBlog\Domain\ValueObject\BoardGameName;
use App\BoardGameBlog\Infrastructure\Doctrine\DoctrineBoardGameRepository;
use App\BoardGameBlog\Infrastructure\Sylius\Resource\BoardGameResource;
use App\BookStore\Infrastructure\ApiPlatform\Resource\BookResource;
use App\Shared\Application\Query\QueryBusInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class BoardGameGridProvider implements DataProviderInterface
{
    public function __construct(
        private DoctrineBoardGameRepository $boardGameRepository,
    ) {
    }

    public function getData(Grid $grid, Parameters $parameters)
    {
        $itemsPerPage = 10;

        $queryBuilder = $this->boardGameRepository->createQueryBuilder('o');
        $page = (int) $parameters->get('page', 1);

        $paginator = new Pagerfanta(
            new QueryAdapter($queryBuilder),
        );
        $paginator->setNormalizeOutOfRangePages(true);
        $paginator->setCurrentPage($page > 0 ? $page : 1);
        $paginator->setMaxPerPage($itemsPerPage);

        $resources = [];
        foreach ($paginator as $model) {
            $resources[] = BoardGameResource::fromModel($model);
        }

        return new Pagerfanta(new ArrayAdapter($resources));
    }
}
