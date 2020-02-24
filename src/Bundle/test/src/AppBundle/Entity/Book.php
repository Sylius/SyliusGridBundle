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

class Book implements ResourceInterface
{
    public const STATE_INITIAL = 'initial';

    public const STATE_PUBLISHED = 'published';

    public const STATE_UNPUBLISHED = 'unpublished';

    /** @var int|null */
    private $id;

    /** @var string|null */
    private $title;

    /** @var Author|null */
    private $author;

    /** @var Price|null */
    private $price;

    /** @var Collection&Attribute[] */
    private $attributes;

    /** @var string */
    private $state;

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
        $this->state = self::STATE_INITIAL;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author): void
    {
        $this->author = $author;
        $this->author->addBook($this);
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(Price $price): void
    {
        $this->price = $price;
    }

    public function addAttribute(Attribute $attribute): void
    {
        $this->attributes->add($attribute);
    }

    public function removeAttribute(Attribute $attribute): void
    {
        $this->attributes->removeElement($attribute);
    }

    /**
     * @return Collection&Book[]
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }
}
