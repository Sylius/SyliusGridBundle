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

namespace Sylius\Bundle\GridBundle\Form\Type\Filter;

if (!class_exists('Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType')) {
    throw new \RuntimeException('You need to install Sylius CurrencyBundle to use this filter! Run: composer require sylius/current-bundle');
}

use Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use Sylius\Component\Grid\Filter\MoneyFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MoneyFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('greaterThan', NumberType::class, [
                'label' => 'sylius.ui.greater_than',
                'required' => false,
                'scale' => $options['scale'],
            ])
            ->add('lessThan', NumberType::class, [
                'label' => 'sylius.ui.less_than',
                'required' => false,
                'scale' => $options['scale'],
            ])
            ->add('currency', CurrencyChoiceType::class, [
                'label' => 'sylius.ui.currency',
                'placeholder' => '---',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
                'scale' => MoneyFilter::DEFAULT_SCALE,
            ])
            ->setAllowedTypes('scale', ['string', 'int'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_grid_filter_money';
    }
}
