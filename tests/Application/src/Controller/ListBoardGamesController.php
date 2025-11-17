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

namespace App\Controller;

use App\BoardGameBlog\Infrastructure\Sylius\Grid\BoardGameGrid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListBoardGamesController extends AbstractController
{
    use GridTrait;

    #[Route(path: '/board-games', name: 'app_board_game_index', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('board_game/index.html.twig', [
            'grid' => $this->getGridView(BoardGameGrid::class),
        ]);
    }
}
