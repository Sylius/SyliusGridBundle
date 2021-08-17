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

use JMS\Serializer\Annotation as Serializer;
use Sylius\Component\Resource\Model\AbstractTranslation;

class BookTranslation extends AbstractTranslation
{
    /**
     * @Serializer\Expose
     * @Serializer\Type("integer")
     */
    protected $id;

    /**
     * @Serializer\Expose
     */
    protected string $title;

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }
}
