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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Parser;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Parser\OptionsParser;
use Sylius\Bundle\GridBundle\Parser\OptionsParserInterface;
use Sylius\Component\Grid\Exception\InvalidArgumentException;

final class OptionsParserTest extends TestCase
{
    public function testItImplementsOptionsParserInterface(): void
    {
        $this->assertInstanceOf(OptionsParserInterface::class, new OptionsParser());
    }

    public function testItParsesOptionsWithCallable(): void
    {
        $options = (new OptionsParser())->parseOptions([
            'type' => 'callable',
            'option' => [
                'callable' => 'callable:strtoupper',
            ],
            'label' => 'app.ui.id',
        ]);

        $this->assertArrayHasKey('type', $options);
        $this->assertArrayHasKey('option', $options);
        $this->assertArrayHasKey('label', $options);

        $this->assertIsCallable($options['option']['callable'] ?? null);
    }

    public function testItFailsWhileParsingOptionsWithInvalidCallable(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $options = (new OptionsParser())->parseOptions([
            'type' => 'callable',
            'option' => [
                'callable' => 'callable:foobar',
            ],
            'label' => 'app.ui.id',
        ]);
    }
}
