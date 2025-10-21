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
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;

final class TwigFieldTest extends TestCase
{
    public function testCreatesFields(): void
    {
        $field = TwigField::create('enabled', '@SyliusUi/Grid/Field/enabled.html.twig');

        $this->assertInstanceOf(FieldInterface::class, $field);
        $this->assertEquals('enabled', $field->getName());
    }

    public function testCreatesFieldsWithVars(): void
    {
        $field = TwigField::create('enabled', '@SyliusUi/Grid/Field/enabled.html.twig')
            ->setOption('vars', ['labels' => 'path/to/label'])
        ;

        $this->assertInstanceOf(FieldInterface::class, $field);
        $this->assertEquals('enabled', $field->getName());
    }
}
