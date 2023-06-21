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

final class FieldValidator implements FieldValidatorInterface
{
    public function validateFieldName(string $fieldName, array $enabledFields): void
    {
        $enabledFieldsNames = array_keys($enabledFields);

        if (!in_array($fieldName, $enabledFieldsNames, true)) {
            throw new BadRequestHttpException(sprintf('%s is not valid field, did you mean one of these: %s?', $fieldName, implode(', ', $enabledFieldsNames)));
        }
    }
}
