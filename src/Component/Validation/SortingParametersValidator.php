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

namespace Sylius\Component\Grid\Validation;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class SortingParametersValidator implements SortingParametersValidatorInterface
{
    public function validateSortingParameters(array $sorting, array $enabledFields): void
    {
        foreach (array_keys($enabledFields) as $key) {
            if (array_key_exists($key, $sorting) && !in_array($sorting[$key], ['asc', 'desc'])) {
                throw new BadRequestHttpException(sprintf('%s is not valid, use asc or desc instead.', $sorting[$key]));
            }
        }
    }
}
