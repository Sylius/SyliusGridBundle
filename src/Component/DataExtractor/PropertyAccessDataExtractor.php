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
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final class PropertyAccessDataExtractor implements DataExtractorInterface
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function get(Field $field, $data)
    {
        return $this->propertyAccessor->getValue($data, $field->getPath());
    }
}
