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

namespace Sylius\Bundle\GridBundle\Migration;

use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;

trait CommonConverterTrait
{
    /** @param mixed $value */
    public function convertValue($value): Expr
    {
        if (is_string($value)) {
            return new String_($value);
        }

        if (is_bool($value)) {
            return new ConstFetch(new Name($value ? 'true' : 'false'));
        }

        if (null === $value) {
            return new ConstFetch(new Name('null'));
        }
        
        if (is_int($value)) {
            return new ConstFetch(new Name((string) $value));
        }

        if (is_array($value)) {
            $items = [];
            foreach ($value as $key => $subValue) {
                $val = $this->convertValue($subValue);
                $convertedKey = null;
                if (is_string($key)) {
                    $convertedKey = new String_($key);
                }
                $items[] = new Node\Expr\ArrayItem($val, $convertedKey);
            }

            return new Array_($items, ['kind' => Array_::KIND_SHORT]);
        }

        throw new InvalidArgumentException('Could not convert datatype: ' . get_debug_type($value));
    }

    private function checkUnconsumedConfiguration(string $key, array $configuration): void
    {
        if (count($configuration) !== 0) {
            throw new InvalidArgumentException(
                sprintf(
                    'There are unconsumed fields under the key "%s": %s',
                    $key,
                    print_r($configuration, true),
                ),
            );
        }
    }

    private function convertToFunctionCall(Expr &$field, array &$configuration, string $fieldName): void
    {
        if (!array_key_exists($fieldName, $configuration)) {
            return;
        }

        $methodName = preg_replace_callback('#_\w#', static fn ($a) => strtoupper($a[0][1]), $fieldName);
        if ($methodName === null) {
            throw new \InvalidArgumentException(sprintf('Could not convert field name "%s" to method name', $fieldName));
        }

        // converting form_options to setFormOptions
        $methodName = 'set' . ucfirst($methodName);
        $field = new MethodCall($field, $methodName, [
            new Arg($this->convertValue($configuration[$fieldName])),
        ]);
        unset($configuration[$fieldName]);
    }
}
