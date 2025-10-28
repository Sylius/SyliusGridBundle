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
use Sylius\Component\Grid\Exception\LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class EnumFieldType implements FieldTypeInterface
{
    public function __construct(
        private DataExtractorInterface $dataExtractor,
        private ?TranslatorInterface $translator = null,
    ) {
    }

    public function render(Field $field, $data, array $options): string
    {
        $enum = $this->dataExtractor->get($field, $data);

        if (null === $enum) {
            return '';
        }

        Assert::isInstanceOf($enum, \UnitEnum::class);

        if ($enum instanceof TranslatableInterface) {
            if (null === $this->translator) {
                throw new LogicException('You have configured a translatable enum, but Symfony translator is not available. Try running "composer require symfony/translator".');
            }

            return $enum->trans($this->translator);
        }

        if ($enum instanceof \BackedEnum) {
            return (string) $enum->value;
        }

        return $enum->name;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('vars');
        $resolver->setAllowedTypes('vars', 'array');
    }
}
