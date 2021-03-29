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

namespace Sylius\Bundle\GridBundle\Form\Type\Filter;

use Sylius\Component\Grid\Filter\NumericRangeFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class NumericRangeFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('greaterThan', NumberType::class, [
                'label' => 'sylius.ui.greater_than',
                'required' => false,
                'scale' => $options['scale'],
                'rounding_mode' => $options['rounding_mode'],
            ])
            ->add('lessThan', NumberType::class, [
                'label' => 'sylius.ui.less_than',
                'required' => false,
                'scale' => $options['scale'],
                'rounding_mode' => $options['rounding_mode'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
                'scale' => NumericRangeFilter::DEFAULT_SCALE,
                'rounding_mode' => NumericRangeFilter::DEFAULT_ROUNDING_MODE,
            ])
            ->setAllowedTypes('scale', ['string', 'int'])
            ->setAllowedTypes('rounding_mode', ['string', 'int'])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_grid_filter_numeric_range';
    }
}
