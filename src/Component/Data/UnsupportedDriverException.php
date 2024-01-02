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

namespace Sylius\Component\Grid\Data;

class UnsupportedDriverException extends \InvalidArgumentException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Grid data driver "%s" is not supported.', $name));
    }
}
