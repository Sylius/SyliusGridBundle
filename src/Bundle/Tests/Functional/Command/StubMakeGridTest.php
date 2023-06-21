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

namespace Sylius\Bundle\GridBundle\Tests\Functional\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class StubMakeGridTest extends KernelTestCase
{
    /** @test */
    public function it_informs_maker_bundle_is_not_registered(): void
    {
        $tester = new CommandTester((new Application(self::bootKernel(['environment' => 'test_without_maker'])))->find('make:grid'));

        $tester->execute([]);

        $this->assertStringContainsString('To run "make:grid" you need the "MakerBundle"', $tester->getDisplay());
    }
}
