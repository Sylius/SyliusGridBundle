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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Grid;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\InvokableGrid;
use Sylius\Component\Grid\Attribute\AsGrid;
use Sylius\Component\Grid\Exception\InvalidArgumentException;
use Sylius\Component\Grid\Exception\LogicException;

#[CoversClass(InvokableGrid::class)]
final class InvokableGridTest extends TestCase
{
    public function testHasItsNameIsTheFQCNByDefault(): void
    {
        $grid = new InvokableGrid(new SimpleGrid());

        $this->assertSame(SimpleGrid::class, $grid->getName());
    }

    public function testHasItsNameCanBeCustomizable(): void
    {
        $grid = new InvokableGrid(new CustomNameGrid());

        $this->assertSame('custom', $grid->getName());
    }

    public function testCanHaveAResourceClass(): void
    {
        $grid = new InvokableGrid(new ResourceGrid());

        $this->assertSame(SimpleResource::class, $grid->getResourceClass());
    }

    public function testItThrowsAnErrorOnClosureFunction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The grid must be an invokable object.');

        new InvokableGrid(function (GridBuilderInterface $gridBuilder): void {});
    }

    public function testItThrowsAnErrorOnGridNotUsingTheAsGridAttribute(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The grid must use the "Sylius\Component\Grid\Attribute\AsGrid" attribute.');

        new InvokableGrid(new class() {
            public function __invoke(GridBuilderInterface $gridBuilder)
            {
            }
        });
    }
}

#[AsGrid]
final class SimpleGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
    }
}

#[AsGrid(name: 'custom')]
final class CustomNameGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
    }
}

#[AsGrid(resourceClass: SimpleResource::class)]
final class ResourceGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
    }
}

final class SimpleResource
{
}
