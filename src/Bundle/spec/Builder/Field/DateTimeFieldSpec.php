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
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\FieldInterface;

final class DateTimeFieldTest extends TestCase
{
    public function testCreatesFields(): void
    {
        $field = DateTimeField::create('createdAt');

        $this->assertInstanceOf(FieldInterface::class, $field);
        $this->assertEquals('createdAt', $field->getName());
    }

    public function testDefinesVarOptions(): void
    {
        $field = DateTimeField::create('createdAt');
        $field->setOption('vars', ['foo' => 'bar']);

        $this->assertInstanceOf(FieldInterface::class, $field);
        $this->assertEquals(['foo' => 'bar'], $field->getOptions()['vars']);
    }
}
