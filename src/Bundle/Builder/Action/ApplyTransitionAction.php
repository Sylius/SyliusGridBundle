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

namespace Sylius\Bundle\GridBundle\Builder\Action;

use Sylius\Component\Grid\Builder\Action\ActionInterface;

final class ApplyTransitionAction
{
    /**
     * @param array<string, mixed> $routeParameters
     * @param array<string, mixed> $options
     */
    public static function create(string $name, string $route, array $routeParameters = [], array $options = []): ActionInterface
    {
        $action = Action::create($name, 'apply_transition');

        $options = array_merge([
            'link' => [
                'route' => $route,
                'parameters' => $routeParameters,
            ],
            'transition' => $name,
        ], $options);

        $action->setOptions($options);

        return $action;
    }
}
