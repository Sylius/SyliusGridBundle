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

namespace AppBundle\FieldTypes;

use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FooType implements FieldTypeInterface
{
    public function render(Field $field, $data, array $options): string
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
