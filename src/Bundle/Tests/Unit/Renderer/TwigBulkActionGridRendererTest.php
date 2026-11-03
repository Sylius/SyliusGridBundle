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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Renderer;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Parser\ActionOptionsParser;
use Sylius\Bundle\GridBundle\Renderer\TwigBulkActionGridRenderer;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Renderer\BulkActionGridRendererInterface;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Grid\View\GridViewInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class TwigBulkActionGridRendererTest extends TestCase
{
    private TwigBulkActionGridRenderer $renderer;

    private Environment $twig;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->renderer = new TwigBulkActionGridRenderer($this->twig, ['delete' => '@SyliusGrid/BulkAction/_delete.html.twig']);
    }

    public function testIsABulkActionGridRenderer(): void
    {
        $this->assertInstanceOf(BulkActionGridRendererInterface::class, $this->renderer);
    }

    public function testUsesTwigToRenderTheBulkAction(): void
    {
        $gridView = $this->createMock(GridViewInterface::class);
        $bulkAction = $this->createMock(Action::class);

        $bulkAction->method('getType')->willReturn('delete');
        $bulkAction->method('getOptions')->willReturn([]);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with('@SyliusGrid/BulkAction/_delete.html.twig', [
                'grid' => $gridView,
                'action' => $bulkAction,
                'data' => null,
            ])
            ->willReturn('<a href="#">Delete</a>')
        ;

        $result = $this->renderer->renderBulkAction($gridView, $bulkAction);
        $this->assertEquals('<a href="#">Delete</a>', $result);
    }

    public function testThrowsAnExceptionIfTemplateIsNotConfiguredForGivenBulkActionType(): void
    {
        $gridView = $this->createMock(GridViewInterface::class);
        $bulkAction = $this->createMock(Action::class);

        $bulkAction->method('getType')->willReturn('foo');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing template for bulk action type "foo".');

        $this->renderer->renderBulkAction($gridView, $bulkAction);
    }

    public function testPassesTheParsedOptionsToTheBulkActionTemplateWhenAnActionOptionsParserIsGiven(): void
    {
        $renderer = new TwigBulkActionGridRenderer(
            new Environment(new ArrayLoader(['@SyliusGrid/BulkAction/_delete.html.twig' => '{{ options.link.parameters.productId }}'])),
            ['delete' => '@SyliusGrid/BulkAction/_delete.html.twig'],
            new ActionOptionsParser(PropertyAccess::createPropertyAccessor()),
        );

        $gridView = new GridView([], Grid::fromCodeAndDriverConfiguration('app_variant', 'doctrine/orm', []), new Parameters(), ['productId' => 42]);
        $bulkAction = Action::fromNameAndType('delete', 'delete');
        $bulkAction->setOptions(['link' => ['parameters' => ['productId' => '$productId']]]);

        $this->assertSame('42', $renderer->renderBulkAction($gridView, $bulkAction));
    }
}
