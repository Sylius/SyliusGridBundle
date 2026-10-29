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

namespace Sylius\Component\Grid\Tests\Unit\Pagination;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Pagination\MaxPerPageResolver;
use Sylius\Component\Grid\Pagination\MaxPerPageResolverInterface;

final class MaxPerPageResolverTest extends TestCase
{
    public function testImplementsMaxPerPageResolverInterface(): void
    {
        $this->assertInstanceOf(MaxPerPageResolverInterface::class, new MaxPerPageResolver());
    }

    /**
     * @param int[] $gridLimits
     */
    #[DataProvider('maxPerPageDataProvider')]
    public function testResolvesMaxPerPage(?int $requestedLimit, array $gridLimits, ?int $fallbackLimit, int $expectedMaxPerPage): void
    {
        $this->assertSame($expectedMaxPerPage, (new MaxPerPageResolver())->resolve($requestedLimit, $gridLimits, $fallbackLimit));
    }

    public static function maxPerPageDataProvider(): iterable
    {
        yield 'no limit uses the first grid limit' => [null, [25, 10, 50], null, 25];
        yield 'no limit and no grid limits uses the fallback limit' => [null, [], 30, 30];
        yield 'no limit, no grid limits and no fallback limit uses the default limit' => [null, [], null, 10];
        yield 'a limit lower than the greatest grid limit is kept' => [5, [10, 25, 50], null, 5];
        yield 'a limit equal to a grid limit is kept' => [25, [10, 25, 50], null, 25];
        yield 'a limit greater than the greatest grid limit is clamped' => [1000, [10, 50, 25], null, 50];
        yield 'a limit without grid limits is kept' => [1000, [], 30, 1000];
        yield 'a zero limit is ignored' => [0, [10, 25], null, 10];
        yield 'a negative limit is ignored' => [-5, [], 30, 30];
        yield 'non positive grid limits are ignored' => [null, [0, -1, 20], null, 20];
    }

    public function testUsesTheConfiguredDefaultLimit(): void
    {
        $this->assertSame(42, (new MaxPerPageResolver(42))->resolve(null));
    }
}
