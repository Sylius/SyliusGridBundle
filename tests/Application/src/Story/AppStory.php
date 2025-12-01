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

namespace App\Story;

use App\Factory\AdminUserFactory;
use App\Factory\AuthorFactory;
use App\Factory\BookFactory;
use App\Factory\NationalityFactory;
use App\Factory\PriceFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {
        // Nationalities
        $american = NationalityFactory::new()->withName('American')->create();
        $english = NationalityFactory::new()->withName('English')->create();

        // Prices
        $priceOne = PriceFactory::new()->withAmount(4200)->withCurrencyCode('EUR')->create();
        $priceTwo = PriceFactory::new()->withAmount(1000)->withCurrencyCode('GBP')->create();

        // Authors
        $michaelCrichton = AuthorFactory::new()->withName('Michael Crichton')->withNationality($american)->create();
        $johnWatson = AuthorFactory::new()->withName('John Watson')->withNationality($english)->create();
        AuthorFactory::createOne();
        $authors = AuthorFactory::new()->withNationality($english)->many(19)->create();

        // Books
        BookFactory::new()->withTitle('Jurassic Park')->withAuthor($michaelCrichton)->withPrice($priceOne)->published()->create();
        BookFactory::new()->withTitle('The Lost World')->withAuthor($michaelCrichton)->withPrice($priceTwo)->unpublished()->create();
        BookFactory::new()->withTitle('A Study in Scarlet')->withAuthor($johnWatson)->create();
        BookFactory::createSequence(
            function () use ($authors) {
                foreach (range(1, 100) as $i) {
                    yield ['title' => 'Book ' . $i, 'author' => $authors[rand(0, 18)]];
                }
            },
        );

        // Admin users
        AdminUserFactory::new()->active()->create();
        AdminUserFactory::new()->inactive()->create();
        AdminUserFactory::new()->banned()->create();
    }
}
