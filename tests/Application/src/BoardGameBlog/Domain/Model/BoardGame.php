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

namespace App\BoardGameBlog\Domain\Model;

final class BoardGame
{
    public function __construct(
        public string $id,
        public string $name,
        public string $shortDescription,
    ) {
    }
}
