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

namespace Sylius\Bundle\GridBundle\FieldTypes;

use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

final class TwigFieldType implements FieldTypeInterface
{
    private DataExtractorInterface $dataExtractor;

    private Environment $twig;

    public function __construct(DataExtractorInterface $dataExtractor, Environment $twig)
    {
        $this->dataExtractor = $dataExtractor;
        $this->twig = $twig;
    }

    public function render(Field $field, $data, array $options)
    {
        if ('.' !== $field->getPath()) {
            $data = $this->dataExtractor->get($field, $data);
        }

        return $this->twig->render($options['template'], ['data' => $data, 'options' => $options]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('template');
        $resolver->setAllowedTypes('template', 'string');

        $resolver->setDefined('vars');
        $resolver->setAllowedTypes('vars', 'array');
    }
}
