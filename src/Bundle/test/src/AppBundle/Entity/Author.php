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

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

class Author implements ResourceInterface
{
    /** @var int|null */
    private $id;

    /** @var string|null */
    private $name;

    /**
     * @var Collection&Book[]
     * @psalm-var Collection<array-key, Book>
     */
    private $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function addBook(Book $book): void
    {
        $this->books[] = $book;
    }

    /**
     * @return Collection&Book[]
     * @psalm-return Collection<array-key, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }
}
