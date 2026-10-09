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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class LiveListBooksController extends AbstractController
{
    #[Route(path: '/live-books', name: 'app_live_book_index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        if ($page <= 0 || $limit <= 0) {
            throw new NotFoundHttpException('Page not found.');
        }

        return $this->render('book/live_index.html.twig');
    }
}
