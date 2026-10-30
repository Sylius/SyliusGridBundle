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

namespace Sylius\Bundle\GridBundle\Parser;

use Sylius\Component\Grid\View\GridViewInterface;
use Sylius\Component\Grid\View\VariablesAwareGridViewInterface;
use Sylius\Component\Grid\View\VariablesResolver;
use Sylius\Component\Grid\View\VariablesResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @experimental
 */
final class ActionOptionsParser implements ActionOptionsParserInterface
{
    private VariablesResolverInterface $variablesResolver;

    public function __construct(
        private readonly PropertyAccessorInterface $propertyAccessor,
        ?VariablesResolverInterface $variablesResolver = null,
    ) {
        $this->variablesResolver = $variablesResolver ?? new VariablesResolver();
    }

    public function parseActionOptions(array $options, GridViewInterface $gridView, mixed $data = null): array
    {
        $variables = $gridView instanceof VariablesAwareGridViewInterface ? $gridView->getVariables() : [];

        return $this->parseOptions($options, $variables, $data);
    }

    /**
     * @param array<array-key, mixed> $options
     * @param array<string, mixed> $variables
     *
     * @return array<array-key, mixed>
     */
    private function parseOptions(array $options, array $variables, mixed $data): array
    {
        return array_map(
            fn (mixed $option): mixed => is_array($option) ? $this->parseOptions($option, $variables, $data) : $this->parseOption($option, $variables, $data),
            $options,
        );
    }

    /** @param array<string, mixed> $variables */
    private function parseOption(mixed $option, array $variables, mixed $data): mixed
    {
        if (!is_string($option)) {
            return $option;
        }

        // Same syntax as the OptionsParser of sylius/resource-bundle
        if (str_starts_with($option, 'resource.')) {
            return $this->getResourceValue($data, substr($option, 9));
        }

        if (str_starts_with($option, 'resource[')) {
            return $this->getResourceValue($data, substr($option, 8));
        }

        return $this->variablesResolver->resolveValue($option, $variables, ['resource' => $data]);
    }

    private function getResourceValue(mixed $data, string $propertyPath): mixed
    {
        if (!is_array($data) && !is_object($data)) {
            throw new \InvalidArgumentException(sprintf('The "resource%s" option cannot be resolved without any resource.', str_starts_with($propertyPath, '[') ? $propertyPath : '.' . $propertyPath));
        }

        return $this->propertyAccessor->getValue($data, $propertyPath);
    }
}
