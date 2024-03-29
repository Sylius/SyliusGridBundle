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

namespace Sylius\Component\Grid\FieldTypes;

use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class StringFieldType implements FieldTypeInterface
{
    private DataExtractorInterface $dataExtractor;

    public function __construct(DataExtractorInterface $dataExtractor)
    {
        $this->dataExtractor = $dataExtractor;
    }

    public function render(Field $field, $data, array $options): string
    {
        $value = $this->dataExtractor->get($field, $data);

        return htmlspecialchars((string) $value);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
