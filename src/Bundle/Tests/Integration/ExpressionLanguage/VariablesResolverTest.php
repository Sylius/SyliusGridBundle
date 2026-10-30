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

namespace Sylius\Bundle\GridBundle\Tests\Integration\ExpressionLanguage;

use Sylius\Component\Grid\View\VariablesResolverInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class VariablesResolverTest extends KernelTestCase
{
    public function testResolvesTheVariablesAndTheExpressionsWithTheRegisteredFunctionsAndVariables(): void
    {
        /** @var VariablesResolverInterface $variablesResolver */
        $variablesResolver = self::getContainer()->get(VariablesResolverInterface::class);

        $this->assertSame(
            [
                'authorId' => 42,
                'greeting' => 'HELLO TOLKIEN FROM GRID',
                'title' => 'The Hobbit',
            ],
            $variablesResolver->resolve(
                [
                    'authorId' => '$authorId',
                    'greeting' => "@=shout('hello ' ~ variables['author'] ~ ' from ' ~ app_name)",
                    'title' => '@=resource.title',
                ],
                ['authorId' => 42, 'author' => 'tolkien'],
                ['resource' => (object) ['title' => 'The Hobbit']],
            ),
        );
    }
}
