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

namespace Functional;

use ApiTestCase\ApiTestCase;
use Coduo\PHPMatcher\Backtrace\VoidBacktrace;
use Coduo\PHPMatcher\Matcher;
use Symfony\Component\HttpFoundation\Response;

final class BoardGameUiTest extends ApiTestCase
{
    /** @test */
    public function it_allows_browsing_board_games(): void
    {
        $boardGames = $this->loadFixturesFromFile('fixtures.yml');

        $this->client->request('GET', '/admin/board-games');
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);
        $content = $response->getContent();

        $this->assertStringContainsString('<td>Stone Age</td>', $content);
        $this->assertStringContainsString(sprintf('<a href="/admin/board-games/%s">Show</a>', $boardGames['stone_age']->id()), $content);
        $this->assertStringContainsString(sprintf('<a href="/admin/board-games/%s/edit">Edit</a>', $boardGames['stone_age']->id()), $content);
        $this->assertStringContainsString(sprintf('<form action="/admin/board-games/%s" method="post">', $boardGames['stone_age']->id()), $content);

        $this->assertStringContainsString('<td>Ticket to Ride</td>', $content);
        $this->assertStringContainsString(sprintf('<a href="/admin/board-games/%s">Show</a>', $boardGames['ticket_to_ride']->id()), $content);
        $this->assertStringContainsString(sprintf('<a href="/admin/board-games/%s/edit">Edit</a>', $boardGames['ticket_to_ride']->id()), $content);
        $this->assertStringContainsString(sprintf('<form action="/admin/board-games/%s" method="post">', $boardGames['ticket_to_ride']->id()), $content);
    }

    protected function buildMatcher(): Matcher
    {
        return $this->matcherFactory->createMatcher(new VoidBacktrace());
    }
}
