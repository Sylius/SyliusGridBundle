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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LiveListBooksController extends AbstractController
{
    #[Route(path: '/live-books', name: 'app_live_book_index', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('book/live_index.html.twig');
    }
}
