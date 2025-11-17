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

final class ListAuthorsWithBooksController extends AbstractController
{
    use GridTrait;

    #[Route(path: '/authors/with-books/with-fetch-join-collection-disabled', name: 'app_author_with_books_with_fetch_join_collection_disabled', methods: ['GET'])]
    public function withBooksWithFetchJoinCollectionDisabled(): Response
    {
        return $this->render('author/index.html.twig', [
            'grid' => $this->getGridView('app_author_with_books_with_fetch_join_collection_disabled'),
        ]);
    }

    #[Route(path: '/authors/with-books/with-fetch-join-collection-enabled', name: 'app_author_with_books_with_fetch_join_collection_enabled', methods: ['GET'])]
    public function withBooksWithFetchJoinCollectionEnabled(): Response
    {
        return $this->render('author/index.html.twig', [
            'grid' => $this->getGridView('app_author_with_books_with_fetch_join_collection_enabled'),
        ]);
    }

    #[Route(path: 'authors/with-books/with-use-output-walkers-disabled', name: 'app_author_with_books_with_use_output_walkers_disabled', methods: ['GET'])]
    public function withBooksWithUseOutputWalkersDisabled(): Response
    {
        return $this->render('author/index.html.twig', [
            'grid' => $this->getGridView('app_author_with_books_with_use_output_walkers_disabled'),
        ]);
    }

    #[Route(path: 'authors/with-books/with-use-output-walkers-enabled', name: 'app_author_with_books_with_use_output_walkers_enabled', methods: ['GET'])]
    public function withBooksWithUseOutputWalkersEnabled(): Response
    {
        return $this->render('author/index.html.twig', [
            'grid' => $this->getGridView('app_author_with_books_with_use_output_walkers_enabled'),
        ]);
    }
}
