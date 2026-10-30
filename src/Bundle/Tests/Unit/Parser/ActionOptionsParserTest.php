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
use Sylius\Bundle\GridBundle\Parser\ActionOptionsParser;
use Sylius\Bundle\GridBundle\Parser\ActionOptionsParserInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Grid\View\GridViewInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class ActionOptionsParserTest extends TestCase
{
    private ActionOptionsParser $parser;

    protected function setUp(): void
    {
        $this->parser = new ActionOptionsParser(PropertyAccess::createPropertyAccessor());
    }

    public function testImplementsActionOptionsParserInterface(): void
    {
        $this->assertInstanceOf(ActionOptionsParserInterface::class, $this->parser);
    }

    public function testResolvesVariablesAndResourceFields(): void
    {
        $gridView = new GridView([], Grid::fromCodeAndDriverConfiguration('app_variant', 'doctrine/orm', []), new Parameters(), ['productId' => 42]);
        $resource = new class() {
            public int $id = 7;

            public string $code = 'MUG';
        };

        $this->assertSame(
            [
                'link' => [
                    'route' => 'app_admin_product_variant_update',
                    'parameters' => ['productId' => 42, 'id' => 7, 'missing' => null],
                ],
                'code' => 'MUG',
                'enabled' => true,
            ],
            $this->parser->parseActionOptions(
                [
                    'link' => [
                        'route' => 'app_admin_product_variant_update',
                        'parameters' => ['productId' => '$productId', 'id' => 'resource.id', 'missing' => '$missing'],
                    ],
                    'code' => 'resource.code',
                    'enabled' => true,
                ],
                $gridView,
                $resource,
            ),
        );
    }

    public function testResolvesArrayResourceFields(): void
    {
        $gridView = $this->createMock(GridViewInterface::class);

        $this->assertSame(['id' => 7], $this->parser->parseActionOptions(['id' => 'resource[id]'], $gridView, ['id' => 7]));
    }

    public function testResolvesVariablesToNullForAGridViewWithoutVariables(): void
    {
        $gridView = $this->createMock(GridViewInterface::class);

        $this->assertSame(['id' => null], $this->parser->parseActionOptions(['id' => '$id'], $gridView));
    }

    public function testCannotResolveAResourceFieldWithoutResource(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "resource.id" option cannot be resolved without any resource.');

        $this->parser->parseActionOptions(['id' => 'resource.id'], $this->createMock(GridViewInterface::class));
    }
}
