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

use Sylius\Component\Grid\Exception\InvalidArgumentException;

final class OptionsParser implements OptionsParserInterface
{
    public function parseOptions(array $parameters): array
    {
        return array_map(
            function (mixed $parameter): mixed {
                if (is_array($parameter)) {
                    return $this->parseOptions($parameter);
                }

                return $this->parseOption($parameter);
            },
            $parameters,
        );
    }

    private function parseOption(mixed $parameter): mixed
    {
        if (!is_string($parameter)) {
            return $parameter;
        }

        if (str_starts_with($parameter, 'callable:')) {
            return $this->parseOptionCallable(substr($parameter, 9));
        }

        return $parameter;
    }

    private function parseOptionCallable(string $callable): \Closure
    {
        if (!is_callable($callable)) {
            throw new InvalidArgumentException(\sprintf('%s is not a callable.', $callable));
        }

        return $callable(...);
    }
}
