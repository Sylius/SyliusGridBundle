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

namespace Sylius\Component\Grid\View;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Webmozart\Assert\Assert;

class GridView implements GridViewInterface
{
    /** @var mixed */
    private $data;

    private Grid $definition;

    private Parameters $parameters;

    /**
     * @param mixed $data
     */
    public function __construct($data, Grid $definition, Parameters $parameters)
    {
        $this->data = $data;
        $this->definition = $definition;
        $this->parameters = $parameters;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getDefinition(): Grid
    {
        return $this->definition;
    }

    public function getParameters(): Parameters
    {
        return $this->parameters;
    }

    public function getSortingOrder(string $fieldName): ?string
    {
        $this->assertFieldIsSortable($fieldName);

        $currentSorting = $this->getCurrentlySortedBy();

        if (array_key_exists($fieldName, $currentSorting)) {
            return $currentSorting[$fieldName];
        }

        $definedSorting = $this->definition->getSorting();

        return reset($definedSorting) ?: null;
    }

    public function isSortedBy(string $fieldName): bool
    {
        $this->assertFieldIsSortable($fieldName);

        if ($this->parameters->has('sorting')) {
            /** @var array<string, string> $sorting */
            $sorting = $this->parameters->get('sorting');

            return array_key_exists($fieldName, $sorting);
        }

        $sortingDefinition = $this->getDefinition()->getSorting();
        $sortedFields = array_keys($sortingDefinition);

        return $fieldName === array_shift($sortedFields);
    }

    public function isFieldVisible(string $fieldName): bool
    {
        $this->assertFieldExists($fieldName);

        $field = $this->definition->getField($fieldName);
        if (!$field->isEnabled()) {
            return false;
        }

        $visibility = $this->parameters->get('visibility', []);
        if (!is_array($visibility)) {
            return $field->isVisible();
        }

        return match ($visibility[$fieldName] ?? null) {
            'visible' => true,
            'hidden' => false,
            default => $field->isVisible(),
        };
    }

    /**
     * @return array<string, string>
     */
    private function getCurrentlySortedBy(): array
    {
        $defaultSorting = $this->definition->getSorting();
        if (!$this->parameters->has('sorting')) {
            return $defaultSorting;
        }

        /** @var array<string, string> $sorting */
        $sorting = $this->parameters->get('sorting');

        return array_merge($defaultSorting, $sorting);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertFieldIsSortable(string $fieldName): void
    {
        $this->assertFieldExists($fieldName);
        Assert::true(
            $this->definition->getField($fieldName)->isSortable(),
            sprintf('Field "%s" is not sortable.', $fieldName),
        );
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertFieldExists(string $fieldName): void
    {
        Assert::true($this->definition->hasField($fieldName), sprintf('Field "%s" does not exist.', $fieldName));
    }
}
