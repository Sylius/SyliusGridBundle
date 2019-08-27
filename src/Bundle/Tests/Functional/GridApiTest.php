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
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixturesFromFile('fixtures.yml');
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
        $this->client->request('GET', '/authors/');

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
        $this->client->request('GET', '/authors/?sorting[name]=desc');

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

    private function getItemsFromCurrentResponse(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true)['_embedded']['items'];
    }

    private function getFirstItemFromCurrentResponse(): array
    {
        return current($this->getItemsFromCurrentResponse());
    }
}
