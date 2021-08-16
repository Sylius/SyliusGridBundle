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

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as Serializer;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class Author implements ResourceInterface
{
    /**
     *
     * @Serializer\Expose
     * @Serializer\Type("integer")
     */
    private ?int $id = null;

    /**
     *
     * @Serializer\Expose
     * @Serializer\Type("string")
     */
    private ?string $name = null;

    /**
     * @Serializer\Expose
     */
    private ?Nationality $nationality = null;

    /** @var Collection&Book[] */
    private Collection $books;

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

    public function getNationality(): ?Nationality
    {
        return $this->nationality;
    }

    public function setNationality(?Nationality $nationality): void
    {
        $this->nationality = $nationality;
    }

    public function addBook(Book $book): void
    {
        $this->books[] = $book;
    }

    /**
     * @return Collection&Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }
}
