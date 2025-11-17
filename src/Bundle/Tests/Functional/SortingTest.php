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

namespace Sylius\Bundle\GridBundle\Tests\Functional;

use ApiTestCase\ApiTestCase;
use Coduo\PHPMatcher\Backtrace\VoidBacktrace;
use Coduo\PHPMatcher\Matcher;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

final class SortingTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixturesFromFile('fixtures.yml');
    }

    /** @test */
    public function it_returns_error_instead_of_sorted_authors_by_book_title_with_use_output_walkers_disabled(): void
    {
        $this->client->request('GET', '/authors/with-books/with-use-output-walkers-disabled?sorting[book]=asc');

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /** @test */
    public function it_returns_correct_amount_of_sorted_authors_by_book_title_with_use_output_walkers_enabled_by_default(): void
    {
        $this->client->request('GET', '/authors/with-books/with-use-output-walkers-enabled?sorting[book]=asc');

        self::assertCount(10, $this->getAuthorNamesFromResponse());
    }

    /** @test */
    public function it_allows_for_sorting_by_disabled_field(): void
    {
        $this->client->request('GET', '/authors?sorting[id]=asc');

        self::assertCount(10, $this->getAuthorNamesFromResponse());
    }

    /** @return string[] */
    private function getAuthorNamesFromResponse(): array
    {
        return $this->getCrawler()
            ->filter('[data-test-name]')
            ->each(
                fn (Crawler $node): string => $node->text(),
            );
    }

    private function getCrawler(): Crawler
    {
        return $this->client->getCrawler();
    }

    protected function buildMatcher(): Matcher
    {
        return $this->matcherFactory->createMatcher(new VoidBacktrace());
    }
}
