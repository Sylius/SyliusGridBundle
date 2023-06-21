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

namespace Sylius\Component\Grid\DataExtractor;

use Sylius\Component\Grid\Definition\Field;

interface DataExtractorInterface
{
    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function get(Field $field, $data);
}
