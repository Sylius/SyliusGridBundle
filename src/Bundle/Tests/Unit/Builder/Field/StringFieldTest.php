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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Builder\Field;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Builder\Field\FieldInterface;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;

final class StringFieldTest extends TestCase
{
    public function testCreatesFields(): void
    {
        $field = StringField::create('firstName');

        $this->assertInstanceOf(FieldInterface::class, $field);
        $this->assertEquals('firstName', $field->getName());
    }

    public function testDefinesVarOptions(): void
    {
        $field = StringField::create('firstName');
        $field->setOption('vars', ['foo' => 'bar']);

        $this->assertInstanceOf(FieldInterface::class, $field);
        $this->assertEquals(['vars' => ['foo' => 'bar']], $field->getOptions());
    }
}
