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

final class SortingTest extends JsonApiTestCase
{
    private array $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = $this->loadFixturesFromFile('fixtures.yml');
    }

    /** @test */
    public function it_returns_error_instead_of_sorted_authors_by_book_title_with_use_output_walkers_disabled(): void
    {
        $this->client->request('GET', '/authors/with-books/with-use-output-walkers-disabled?sorting[book]=asc');

        self::assertStringContainsString(
            'Cannot select distinct identifiers from query with LIMIT and ORDER BY on a column from a fetch joined to-many association. Use output walkers.',
            $this->client->getResponse()->getContent()
        );
    }

    /** @test */
    public function it_returns_correct_amount_of_sorted_authors_by_book_title_with_use_output_walkers_enabled_by_default(): void
    {
        $this->client->request('GET', '/authors/with-books/with-use-output-walkers-enabled?sorting[book]=asc');

        self::assertCount(10, $this->getItemsFromCurrentResponse());
    }

    private function getItemsFromCurrentResponse(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true)['_embedded']['items'];
    }
}
