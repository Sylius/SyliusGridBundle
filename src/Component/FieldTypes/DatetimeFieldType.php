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
use Webmozart\Assert\Assert;

final class DatetimeFieldType implements FieldTypeInterface
{
    private DataExtractorInterface $dataExtractor;

    public function __construct(
        DataExtractorInterface $dataExtractor,
        private ?string $timezone = null,
    ) {
        $this->dataExtractor = $dataExtractor;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function render(Field $field, $data, array $options): string
    {
        $value = $this->dataExtractor->get($field, $data);
        if (null === $value) {
            return '';
        }

        /** @var \DateTimeImmutable|\DateTime $value */
        Assert::isInstanceOf($value, \DateTimeInterface::class);

        if (null !== $options['timezone']) {
            $value = $value->setTimezone(new \DateTimeZone($options['timezone']));
        }

        return $value->format($options['format']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('format', 'Y-m-d H:i:s');
        $resolver->setAllowedTypes('format', 'string');
        $resolver->setDefault('timezone', $this->timezone);
        $resolver->setAllowedTypes('timezone', ['null', 'string']);
        $resolver->setDefined('vars');
        $resolver->setAllowedTypes('vars', 'array');
    }
}
