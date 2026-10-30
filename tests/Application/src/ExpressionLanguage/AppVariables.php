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

namespace App\ExpressionLanguage;

use Sylius\Component\Grid\Attribute\AsExpressionVariables;
use Sylius\Component\Grid\ExpressionLanguage\VariablesCollectionInterface;

#[AsExpressionVariables]
final class AppVariables implements VariablesCollectionInterface
{
    public function getCollection(): array
    {
        return ['app_name' => 'Grid'];
    }
}
