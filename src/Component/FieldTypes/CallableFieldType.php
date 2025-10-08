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

use Psr\Container\ContainerInterface;
use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Exception\UnexpectedValueException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CallableFieldType implements FieldTypeInterface
{
    public function __construct(
        private DataExtractorInterface $dataExtractor,
        private ContainerInterface $locator,
    ) {
    }

    public function render(Field $field, $data, array $options): string
    {
        if (isset($options['callable']) === isset($options['service'])) {
            throw new \RuntimeException('Exactly one of the "callable" or "service" options must be defined.');
        }

        $value = $this->dataExtractor->get($field, $data);
        $value = call_user_func($this->getCallable($options), $value);

        try {
            $value = (string) $value;
        } catch (\Throwable $e) {
            throw new UnexpectedValueException(\sprintf(
                'Callable field (name "%s") returned value could not be converted to string: "%s".',
                $field->getName(),
                $e->getMessage(),
            ));
        }

        if ($options['htmlspecialchars']) {
            $value = htmlspecialchars($value);
        }

        return $value;
    }

    private function getCallable(array $options): callable
    {
        if (isset($options['callable'])) {
            return $options['callable'];
        }

        if (!$this->locator->has($options['service'])) {
            throw new \RuntimeException(sprintf('Service "%s" not found, make sure it is tagged with "sylius.grid_field_callable_service".', $options['service']));
        }

        $service = $this->locator->get($options['service']);
        if (isset($options['method'])) {
            $callable = [$service, $options['method']];

            if (!is_callable($callable)) {
                throw new \RuntimeException(sprintf('The method "%s" is not callable on service "%s".', $options['method'], $options['service']));
            }

            return $callable;
        }

        if (!is_callable($service)) {
            throw new \RuntimeException(sprintf('The service "%s" is not callable.', $options['service']));
        }

        return $service;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('callable');
        $resolver->setAllowedTypes('callable', 'callable');

        $resolver->setDefined('service');
        $resolver->setAllowedTypes('service', 'string');

        $resolver->setDefined('method');
        $resolver->setAllowedTypes('method', 'string');

        $resolver->setDefault('htmlspecialchars', true);
        $resolver->setAllowedTypes('htmlspecialchars', 'bool');
    }
}
