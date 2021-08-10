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
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Annotation as Serializer;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class Book implements ResourceInterface
{
    public const STATE_INITIAL = 'initial';

    public const STATE_PUBLISHED = 'published';

    public const STATE_UNPUBLISHED = 'unpublished';

    /**
     *
     * @Serializer\Expose
     * @Serializer\Type("integer")
     */
    private ?int $id = null;

    /** @var ArrayCollection|PersistentCollection|TranslationInterface[]
     * @Serializer\Expose
     */
    private $translations;

    /**
     *
     * @Serializer\Expose
     * @Serializer\Type("string")
     */
    private ?string $title = null;

    /**
     * @Serializer\Expose
     */
    private ?Author $author = null;

    /**
     * @Serializer\Expose
     */
    private ?Price $price = null;

    private string $state;

    public function __construct()
    {
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

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function setTranslations($translations): void
    {
        $this->translations = $translations;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }
}
