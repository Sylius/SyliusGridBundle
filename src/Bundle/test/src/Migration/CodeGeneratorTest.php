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

namespace App\Migration;

use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Echo_;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Migration\CodeGenerator;
use Sylius\Bundle\GridBundle\Migration\CodeOutputter;

final class CodeGeneratorTest extends TestCase
{
    private CodeGenerator $codeGenerator;

    private CodeOutputter $codeOutputter;

    public function setup(): void
    {
        $this->codeOutputter = new CodeOutputter();
        $this->codeGenerator = new CodeGenerator($this->codeOutputter);
    }

    public function testGeneratingAnEmptyFile(): void
    {
        $this->assertEquals("<?php\n", $this->codeGenerator->build());
    }

    /** @dataProvider dataGeneratingImportStatements */
    public function testGeneratingImportStatements(array $statementsToImport, string $expectedCode): void
    {
        foreach ($statementsToImport as $class) {
            $this->codeGenerator->addUseStatement($class);
        }

        $this->assertSame($expectedCode, $this->codeGenerator->build());
    }

    public function dataGeneratingImportStatements(): iterable
    {
        yield 'Importing one class' => [
            ['SomeNamespace\\SomeClass'],
<<<PHP
<?php
use SomeNamespace\\SomeClass;
PHP,
        ];

        yield 'Importing the same class multiple times and not get conflicts' => [
            [
                'SomeNamespace\\SomeClass',
                'SomeNamespace\\SomeClass',
            ],
<<<PHP
<?php
use SomeNamespace\\SomeClass;
PHP,
        ];
    }

    public function testGeneratingNamespace(): void
    {
        $this->codeGenerator->setNamespace('Sylius\\SomeNamespace');

        $this->assertSame(
            <<<PHP
<?php
namespace Sylius\\SomeNamespace;

PHP,
            $this->codeGenerator->build(),
        );
    }

    public function testGeneratingAClass(): void
    {
        $this->codeGenerator->addClass('TestClass', 'SomeClass', [], []);

        $this->assertSame(
            <<<PHP
<?php
class TestClass extends SomeClass
{
}
PHP,
            $this->codeGenerator->build(),
        );
    }

    public function testGeneratingAStaticFunctionThatReturnsAString(): void
    {
        $function = CodeGenerator::createStaticFunction('someFunction', 'Hello World');

        $this->assertSame(
            <<<PHP
public static function someFunction() : string
{
    return 'Hello World';
}
PHP,
            $this->codeOutputter->prettyPrint([$function]),
        );
    }

    public function testGeneratingAFunction(): void
    {
        $function = CodeGenerator::createFunction(
            'greet',
            [['string', 'name']],
            [new Echo_([new String_('Hello'), new Variable(new Name('name'))])],
        );

        $this->assertSame(
            <<<PHP
public function greet(string \$name) : void
{
    echo 'Hello', \$name;
}
PHP,
            $this->codeOutputter->prettyPrint([$function]),
        );
    }

    public function testGettingRelativeClass(): void
    {
        $relativeName = $this->codeGenerator->getRelativeClassName('SomeNamespace\\SomeClass');

        $this->assertSame('SomeClass', $relativeName->toString());
        $this->assertEquals(
            <<<PHP
<?php
use SomeNamespace\\SomeClass;
PHP,
            $this->codeGenerator->build(),
        );
    }

    /** @dataProvider dataGeneratingAResourceClass */
    public function testGeneratingAResourceClass(string $resourceClass, string $expectedCode): void
    {
        $code = $this->codeGenerator->generateGetResourceClassFunction($resourceClass);

        $this->assertEquals(
            $expectedCode,
            $this->codeGenerator->build() . "\n" .
            $this->codeOutputter->prettyPrint([$code]),
        );
    }

    public function dataGeneratingAResourceClass(): iterable
    {
        yield 'A resource class that exists' => [
            self::class,
<<<PHP
<?php
use App\Migration\CodeGenatorTest;
public function getResourceClass() : string
{
    return CodeGenatorTest::class;
}
PHP
        ];

        yield 'A class like that does not exist' => [
            'SomeNamespace\\SomeClass',
<<<PHP
<?php

public function getResourceClass() : string
{
    return 'SomeNamespace\\\\SomeClass';
}
PHP
        ];

        yield 'Not class like' => [
            'If you are using the % syntax, please do not',
<<<PHP
<?php

public function getResourceClass() : string
{
    return 'If you are using the % syntax, please do not';
}
PHP
        ];
    }
}
