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

namespace spec\Sylius\Component\Grid\FieldTypes;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DatetimeFieldTypeSpec extends ObjectBehavior
{
    function let(DataExtractorInterface $dataExtractor): void
    {
        $this->beConstructedWith($dataExtractor);
    }

    function it_is_a_grid_field_type(): void
    {
        $this->shouldImplement(FieldTypeInterface::class);
    }

    function it_uses_data_extractor_to_obtain_data_parse_it_with_given_configuration_and_renders_it(
        DataExtractorInterface $dataExtractor,
        \DateTime $dateTime,
        Field $field,
    ): void {
        $dataExtractor->get($field, ['foo' => 'bar'])->willReturn($dateTime);

        $dateTime->setTimezone(Argument::any())->shouldNotBeCalled();
        $dateTime->format('Y-m-d')->willReturn('2001-10-10');

        $this->render($field, ['foo' => 'bar'], [
            'format' => 'Y-m-d',
            'timezone' => null,
        ])->shouldReturn('2001-10-10');
    }

    function it_sets_timezone_if_specified(
        DataExtractorInterface $dataExtractor,
        \DateTime $dateTime,
        Field $field,
    ): void {
        $dataExtractor->get($field, ['foo' => 'bar'])->willReturn($dateTime);

        $dateTime->setTimezone(new \DateTimeZone('Europe/Warsaw'))->willReturn($dateTime);
        $dateTime->format('Y-m-d H:i:s')->willReturn('2021-10-10 00:00:00');

        $this->render($field, ['foo' => 'bar'], [
            'format' => 'Y-m-d H:i:s',
            'timezone' => 'Europe/Warsaw',
        ])->shouldReturn('2021-10-10 00:00:00');
    }

    function it_returns_null_if_property_accessor_returns_null(DataExtractorInterface $dataExtractor, Field $field): void
    {
        $dataExtractor->get($field, ['foo' => 'bar'])->willReturn(null);

        $this->render($field, ['foo' => 'bar'], [
            'format' => '',
            'timezone' => null,
        ])->shouldReturn('');
    }

    function it_uses_timezone_parameter_as_default_timezone_option(
        DataExtractorInterface $dataExtractor,
        OptionsResolver $resolver,
    ): void {
        $this->beConstructedWith($dataExtractor, 'Europe/Warsaw');

        $resolver->setDefault('format', 'Y-m-d H:i:s')->willReturn($resolver)->shouldBeCalled();
        $resolver->setAllowedTypes('format', 'string')->willReturn($resolver)->shouldBeCalled();
        $resolver->setDefault('timezone', 'Europe/Warsaw')->willReturn($resolver)->shouldBeCalled();
        $resolver->setAllowedTypes('timezone', ['null', 'string'])->willReturn($resolver)->shouldBeCalled();
        $resolver->setDefined('vars')->willReturn($resolver)->shouldBeCalled();
        $resolver->setAllowedTypes('vars', 'array')->willReturn($resolver)->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_throws_exception_if_returned_value_is_not_datetime(DataExtractorInterface $dataExtractor, Field $field): void
    {
        $dataExtractor->get($field, ['foo' => 'bar'])->willReturn('badObject');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('render', [$field, ['foo' => 'bar'], [
                'format' => '',
                'timezone' => null,
            ]])
        ;
    }
}
