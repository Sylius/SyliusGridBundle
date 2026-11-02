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

namespace Sylius\Component\Grid\Sanitizing;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Pagination\MaxPerPageResolver;
use Sylius\Component\Grid\Pagination\MaxPerPageResolverInterface;
use Sylius\Component\Grid\Parameters;

final class ParametersSanitizer implements ParametersSanitizerInterface
{
    private const SORTING_DIRECTIONS = ['asc', 'desc'];

    private MaxPerPageResolverInterface $maxPerPageResolver;

    public function __construct(?MaxPerPageResolverInterface $maxPerPageResolver = null)
    {
        $this->maxPerPageResolver = $maxPerPageResolver ?? new MaxPerPageResolver();
    }

    public function sanitize(Grid $grid, Parameters $parameters): Parameters
    {
        $sanitizedParameters = $parameters->all();

        if ($parameters->has('page')) {
            $page = $this->sanitizePositiveInteger($parameters->get('page'));

            if (null === $page) {
                unset($sanitizedParameters['page']);
            } else {
                $sanitizedParameters['page'] = $page;
            }
        }

        if ($parameters->has('limit')) {
            $limit = $this->sanitizePositiveInteger($parameters->get('limit'));

            if (null === $limit) {
                unset($sanitizedParameters['limit']);
            } else {
                $sanitizedParameters['limit'] = $this->maxPerPageResolver->resolve($limit, $grid->getLimits());
            }
        }

        if ($parameters->has('sorting')) {
            $sorting = $this->sanitizeSorting($grid, $parameters->get('sorting'));

            // Without any valid sorting, the default sorting of the grid is applied
            if ([] === $sorting) {
                unset($sanitizedParameters['sorting']);
            } else {
                $sanitizedParameters['sorting'] = $sorting;
            }
        }

        return new Parameters($sanitizedParameters);
    }

    private function sanitizePositiveInteger(mixed $value): ?int
    {
        if (!is_int($value) && !is_string($value)) {
            return null;
        }

        $value = filter_var($value, \FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        return false === $value ? null : $value;
    }

    /**
     * @return array<string, string>
     */
    private function sanitizeSorting(Grid $grid, mixed $sorting): array
    {
        if (!is_array($sorting)) {
            return [];
        }

        $sanitizedSorting = [];

        foreach ($sorting as $field => $direction) {
            if (!is_string($field) || !is_string($direction)) {
                continue;
            }

            if (!$grid->hasField($field) || !$grid->getField($field)->isSortable()) {
                continue;
            }

            $direction = strtolower($direction);
            if (!in_array($direction, self::SORTING_DIRECTIONS, true)) {
                continue;
            }

            $sanitizedSorting[$field] = $direction;
        }

        return $sanitizedSorting;
    }
}
