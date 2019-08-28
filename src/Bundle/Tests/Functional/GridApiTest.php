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

namespace Sylius\Bundle\GridBundle\Tests\Functional;

use ApiTestCase\JsonApiTestCase;

final class GridApiTest extends JsonApiTestCase
{
    /** @var array */
    private $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = $this->loadFixturesFromFile('fixtures.yml');
    }

    /** @test */
    public function it_shows_authors_grid(): void
    {
        $this->client->request('GET', '/authors/');

        $this->assertResponse($this->client->getResponse(), 'authors_grid');
    }

    /** @test */
    public function it_sorts_authors_by_name_ascending_by_default(): void
    {
        $this->client->request('GET', '/authors/?limit=100');

        $items = $this->getItemsFromCurrentResponse();
        $names = array_map(static function (array $item): string {
            return $item['name'];
        }, $items);

        $sortedNames = $names;
        sort($names);

        $this->assertSame($sortedNames, $names);
    }

    /** @test */
    public function it_sorts_authors_by_name_descending(): void
    {
        $this->client->request('GET', '/authors/?sorting[name]=desc&limit=100');

        $items = $this->getItemsFromCurrentResponse();
        $names = array_map(static function (array $item): string {
            return $item['name'];
        }, $items);

        $sortedNames = $names;
        rsort($names);

        $this->assertSame($sortedNames, $names);
    }

    /** @test */
    public function it_paginates_authors_by_10_by_default(): void
    {
        $this->client->request('GET', '/authors/');

        $this->assertCount(10, $this->getItemsFromCurrentResponse());
    }

    /** @test */
    public function it_paginates_authors_by_5_or_15(): void
    {
        $this->client->request('GET', '/authors/?limit=5');

        $this->assertCount(5, $this->getItemsFromCurrentResponse());

        $this->client->request('GET', '/authors/?limit=15');

        $this->assertCount(15, $this->getItemsFromCurrentResponse());
    }

    /** @test */
    public function it_filters_books_by_title(): void
    {
        $this->client->request('GET', sprintf(
            '/books/?criteria[title][type]=equal&criteria[title][value]=%s',
            urlencode('Book 5')
        ));

        $this->assertCount(1, $this->getItemsFromCurrentResponse());
        $this->assertSame('Book 5', $this->getFirstItemFromCurrentResponse()['title']);
    }

    /** @test */
    public function it_filters_books_by_author(): void
    {
        $authorId = $this->data['author_michael_crichton']->getId();

        $this->client->request('GET', sprintf('/books/?criteria[author]=%d', $authorId));

        $this->assertCount(2, $this->getItemsFromCurrentResponse());
        $this->assertSame('Jurassic Park', $this->getFirstItemFromCurrentResponse()['title']);
    }

    /** @test */
    public function it_filters_books_by_authors_nationality(): void
    {
        $authorNationalityId = $this->data['author_michael_crichton']->getNationality()->getId();

        $this->client->request('GET', sprintf('/books/?criteria[nationality]=%d', $authorNationalityId));

        $this->assertCount(2, $this->getItemsFromCurrentResponse());
        $this->assertSame('Jurassic Park', $this->getFirstItemFromCurrentResponse()['title']);
    }

    /** @test */
    public function it_sorts_books_ascending_by_author(): void
    {
        $this->client->request('GET', '/books/?sorting[author]=asc&limit=100');

        $items = $this->getItemsFromCurrentResponse();
        $names = array_map(static function (array $item): string {
            return $item['author']['name'];
        }, $items);

        $sortedNames = $names;
        sort($names);

        $this->assertSame($sortedNames, $names);
    }

    /** @test */
    public function it_sorts_books_descending_by_authors_nationality(): void
    {
        $this->client->request('GET', '/books/?sorting[nationality]=desc&limit=100');

        $items = $this->getItemsFromCurrentResponse();
        $names = array_map(static function (array $item): string {
            return $item['author']['nationality']['name'];
        }, $items);

        $sortedNames = $names;
        rsort($names);

        $this->assertSame($sortedNames, $names);
    }

    /** @test */
    public function it_sorts_authors_using_table_alias_defined_in_query_builder(): void
    {
        $this->client->request('GET', '/by-american-authors/books/?sorting[author]=asc');

        $this->assertResponse($this->client->getResponse(), 'american_authors_sorted_ascending');
    }

    private function getItemsFromCurrentResponse(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true)['_embedded']['items'];
    }

    private function getFirstItemFromCurrentResponse(): array
    {
        return current($this->getItemsFromCurrentResponse());
    }
}
