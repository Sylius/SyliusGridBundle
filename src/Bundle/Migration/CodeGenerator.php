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
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;

class CodeGenerator
{
    public function __construct(
        private CodeOutputter $codeOutputter,
    ) {
    }

    /** @var array<string> */
    private array $classesToUse = [];

    /** @var array<Node> */
    private array $classNodes = [];

    private ?Node $namespace = null;

    public function setNamespace(string $namespace): void
    {
        $this->namespace = new Node\Stmt\Namespace_(new Name($namespace));
    }

    public function addUseStatement(string $classToUse): void
    {
        $this->classesToUse[] = $classToUse;
    }

    public function addClass(string $className, ?string $extends, ?array $implements, array $body): void
    {
        $classConfiguration = ['stmts' => $body];

        if ($extends !== null) {
            $classConfiguration['extends'] = new Identifier($extends);
        }

        if (count($implements ?? []) > 0) {
            $classConfiguration['implements'] = array_map(
                static fn (string $interfaceName) => new Identifier($interfaceName),
                $implements,
            );
        }

        $this->classNodes[] = new Class_(new Identifier($className), $classConfiguration);
    }

    public static function createNonStaticFunction(string $functionName, string $returnValue): Node
    {
        return new ClassMethod(
            new Identifier($functionName),
            [
                'flags' => Class_::MODIFIER_PUBLIC,
                'returnType' => 'string',
                'stmts' => [
                    new Return_(new String_($returnValue)),
                ],
            ],
        );
    }

    public static function createStaticFunction(string $functionName, string $returnValue): Node
    {
        return new ClassMethod(
            new Identifier($functionName),
            [
                'flags' => Class_::MODIFIER_PUBLIC | Class_::MODIFIER_STATIC,
                'returnType' => 'string',
                'stmts' => [
                    new Return_(new String_($returnValue)),
                ],
            ],
        );
    }

    public static function createFunction(
        string $functionName,
        array $params,
        array $body,
    ): Node {
        return new ClassMethod(
            new Identifier('buildGrid'),
            [
                'flags' => Class_::MODIFIER_PUBLIC,
                'returnType' => 'void',
                'params' => array_map(
                    fn (array $param) => new Param(new Variable($param[1]), null, $param[0]),
                    $params,
                ),
                'stmts' => $body,
            ],
        );
    }

    public function build(): string
    {
        $nodes = [];
        if ($this->namespace !== null) {
            $nodes[] = $this->namespace;
        }

        foreach (array_map(
            static fn (string $classToUse) => new Use_([new UseUse(new Name($classToUse))]),
            $this->classesToUse,
        ) as $useStatements) {
            $nodes[] = $useStatements;
        }

        foreach ($this->classNodes as $classNode) {
            $nodes[] = $classNode;
        }

        $generatedCode = $this->codeOutputter->printCode($nodes);

        $this->classNodes = [];
        $this->namespace = null;
        $this->classesToUse = [];

        return $generatedCode;
    }
}
