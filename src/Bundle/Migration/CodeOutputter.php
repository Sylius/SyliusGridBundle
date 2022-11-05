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

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinter\Standard;

final class CodeOutputter extends Standard
{
    protected function pExpr_MethodCall(Expr\MethodCall $node): string
    {
        if ($node->var instanceof Expr\Variable) {
            $this->indent();
        }

        // Prints $var->methodName
        $methodCall = $this->pDereferenceLhs($node->var) . $this->nl . '->' . $this->pObjectProperty($node->name);

        // If the first argument is a static call then we want to print it in multiple lines
        $firstArgument = $node->args[0]->value ?? null;
        if ($this->unwrapChainedMethodCall($firstArgument) instanceof Expr\StaticCall) {
            $arguments = '(' . $this->pCommaSeparatedMultiline($node->args, false) . $this->nl . ')';
        } else {
            $arguments = '(' . $this->pMaybeMultiline($node->args) . ')';
        }

        return $methodCall . $arguments;
    }

    private function unwrapChainedMethodCall(mixed $methodCall): mixed
    {
        if ($methodCall === null || $methodCall instanceof Expr\StaticCall) {
            return $methodCall;
        }

        $methodCallRef = $methodCall;
        while ($methodCallRef instanceof Expr\MethodCall) {
            $methodCallRef = $methodCallRef->var;
        }

        return $methodCallRef;
    }

    protected function pExpr_Array(Expr\Array_ $node): string
    {
        /** @var array<Node> $items */
        $items = $node->items;

        return '[' . $this->pCommaSeparatedMultiline($items, true) . $this->nl . ']';
    }

    protected function pStmt_Expression(Stmt\Expression $node): string
    {
        $printedExpression = $this->p($node->expr);

        if (strpos($printedExpression, "\n")) {
            $this->outdent();
            $printedExpression .= $this->nl;
        }
        $printedExpression .= ';';

        return $printedExpression;
    }
}
