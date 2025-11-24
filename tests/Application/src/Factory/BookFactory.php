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

namespace App\Factory;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Price;
use function Zenstruck\Foundry\lazy;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;

/**
 * @extends PersistentProxyObjectFactory<Book>
 */
final class BookFactory extends PersistentProxyObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Book::class;
    }

    public function withTitle(string $title): self
    {
        return $this->with(['title' => $title]);
    }

    public function withAuthor(Proxy|Author $author): self
    {
        return $this->with(['author' => $author]);
    }

    public function withPrice(Proxy|Price $price): self
    {
        return $this->with(['price' => $price]);
    }

    public function unpublished(): self
    {
        return $this->with(['state' => Book::STATE_UNPUBLISHED]);
    }

    public function published(): self
    {
        return $this->with(['state' => Book::STATE_PUBLISHED]);
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'createdAt' => self::faker()->dateTime(),
            'enabled' => self::faker()->boolean(),
            'title' => self::faker()->text(255),
            'price' => PriceFactory::new(),
            'author' => lazy(fn () => AuthorFactory::randomOrCreate()),
        ];
    }
}
