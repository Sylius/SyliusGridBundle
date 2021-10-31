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

final class PaginationTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixturesFromFile('fixtures.yml');
    }

    /** @test */
    public function it_returns_incorrect_amount_of_items_per_page_with_fetch_join_collection_disabled(): void
    {
        $this->client->request('GET', '/authors/with-books/with-fetch-join-collection-disabled');

        self::assertNotCount(10, $this->getItemsFromCurrentResponse());
    }

    /** @test */
    public function it_returns_correct_amount_of_items_per_page_with_fetch_join_collection_enabled_by_default(): void
    {
        $this->client->request('GET', '/authors/with-books/with-fetch-join-collection-enabled');

        self::assertCount(10, $this->getItemsFromCurrentResponse());
    }

    private function getItemsFromCurrentResponse(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR)['_embedded']['items'];
    }
}
