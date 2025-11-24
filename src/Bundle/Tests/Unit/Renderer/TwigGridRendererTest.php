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
use Sylius\Bundle\GridBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Bundle\GridBundle\Parser\OptionsParserInterface;
use Sylius\Bundle\GridBundle\Renderer\TwigGridRenderer;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Sylius\Component\Grid\Filter\StringFilter;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Grid\View\GridViewInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

final class TwigGridRendererTest extends TestCase
{
    private TwigGridRenderer $renderer;

    private Environment $twig;

    private ServiceRegistryInterface $fieldsRegistry;

    private FormFactoryInterface $formFactory;

    private FormTypeRegistryInterface $formTypeRegistry;

    private OptionsParserInterface $optionsParser;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->fieldsRegistry = $this->createMock(ServiceRegistryInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->formTypeRegistry = $this->createMock(FormTypeRegistryInterface::class);
        $this->optionsParser = $this->createMock(OptionsParserInterface::class);

        $actionTemplates = [
            'link' => '@SyliusGrid/Action/_link.html.twig',
            'form' => '@SyliusGrid/Action/_form.html.twig',
        ];
        $filterTemplates = [
            StringFilter::NAME => '@SyliusGrid/Filter/_string.html.twig',
        ];

        $this->renderer = new TwigGridRenderer(
            $this->twig,
            $this->fieldsRegistry,
            $this->formFactory,
            $this->formTypeRegistry,
            '"@SyliusGrid/default"',
            $actionTemplates,
            $filterTemplates,
            $this->optionsParser,
        );
    }

    public function testIsAGridRenderer(): void
    {
        $this->assertInstanceOf(GridRendererInterface::class, $this->renderer);
    }

    public function testUsesTwigToRenderTheGridView(): void
    {
        $gridView = $this->createMock(GridViewInterface::class);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with('"@SyliusGrid/default"', ['grid' => $gridView])
            ->willReturn('<html>Grid!</html>')
        ;

        $result = $this->renderer->render($gridView);
        $this->assertEquals('<html>Grid!</html>', $result);
    }

    public function testUsesCustomTemplateIfSpecified(): void
    {
        $gridView = $this->createMock(GridView::class);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with('"@SyliusGrid/custom"', ['grid' => $gridView])
            ->willReturn('<html>Grid!</html>')
        ;

        $result = $this->renderer->render($gridView, '"@SyliusGrid/custom"');
        $this->assertEquals('<html>Grid!</html>', $result);
    }

    public function testUsesTwigToRenderTheAction(): void
    {
        $gridView = $this->createMock(GridViewInterface::class);
        $action = $this->createMock(Action::class);

        $action->expects($this->once())->method('getType')->willReturn('link');
        $action->expects($this->once())->method('getTemplate')->willReturn(null);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with('@SyliusGrid/Action/_link.html.twig', [
                'grid' => $gridView,
                'action' => $action,
                'data' => null,
            ])
            ->willReturn('<a href="#">Action!</a>')
        ;

        $result = $this->renderer->renderAction($gridView, $action);
        $this->assertEquals('<a href="#">Action!</a>', $result);
    }

    public function testUsesCustomActionTemplateIfSpecified(): void
    {
        $gridView = $this->createMock(GridViewInterface::class);
        $action = $this->createMock(Action::class);

        $action->expects($this->once())->method('getType')->willReturn('foo');
        $action->expects($this->once())->method('getTemplate')->willReturn('path/to/template');

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with('path/to/template', [
                'grid' => $gridView,
                'action' => $action,
                'data' => null,
            ])
            ->willReturn('<a href="#">Action!</a>')
        ;

        $this->renderer->renderAction($gridView, $action, null);
    }

    public function testThrowsAnExceptionIfTemplateIsNotConfiguredForGivenActionType(): void
    {
        $gridView = $this->createMock(GridViewInterface::class);
        $action = $this->createMock(Action::class);

        $action->expects($this->once())->method('getType')->willReturn('foo');
        $action->expects($this->once())->method('getTemplate')->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing template for action type "foo".');

        $this->renderer->renderAction($gridView, $action);
    }

    public function testRendersAFieldWithDataViaAppropriateFieldType(): void
    {
        $gridView = $this->createMock(GridViewInterface::class);
        $field = $this->createMock(Field::class);
        $fieldType = $this->createMock(FieldTypeInterface::class);

        $field->method('getType')->willReturn('string');
        $this->fieldsRegistry->method('get')->with('string')->willReturn($fieldType);
        $fieldType
            ->expects($this->once())
            ->method('configureOptions')
            ->with($this->isInstanceOf(OptionsResolver::class))
            ->willReturnCallback(function (OptionsResolver $resolver) {
                $resolver->setRequired('foo');
            })
        ;

        $field->method('getOptions')->willReturn(['foo' => 'bar']);
        $this->optionsParser->method('parseOptions')->with(['foo' => 'bar'])->willReturn(['foo' => 'bar']);
        $fieldType->method('render')->with($field, 'Value', ['foo' => 'bar'])->willReturn('<strong>Value</strong>');

        $result = $this->renderer->renderField($gridView, $field, 'Value');
        $this->assertEquals('<strong>Value</strong>', $result);
    }

    public function testRendersAFieldWithDataViaAppropriateFieldTypeWhenNoOptionParserIsProvided(): void
    {
        $twig = $this->createMock(Environment::class);
        $fieldsRegistry = $this->createMock(ServiceRegistryInterface::class);
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formTypeRegistry = $this->createMock(FormTypeRegistryInterface::class);
        $gridView = $this->createMock(GridViewInterface::class);
        $field = $this->createMock(Field::class);
        $fieldType = $this->createMock(FieldTypeInterface::class);

        $actionTemplates = [
            'link' => '@SyliusGrid/Action/_link.html.twig',
            'form' => '@SyliusGrid/Action/_form.html.twig',
        ];
        $filterTemplates = [
            StringFilter::NAME => '@SyliusGrid/Filter/_string.html.twig',
        ];

        $renderer = new TwigGridRenderer(
            $twig,
            $fieldsRegistry,
            $formFactory,
            $formTypeRegistry,
            '"@SyliusGrid/default"',
            $actionTemplates,
            $filterTemplates,
            null,
        );

        $field->method('getType')->willReturn('string');
        $fieldsRegistry->method('get')->with('string')->willReturn($fieldType);
        $fieldType
            ->expects($this->once())
            ->method('configureOptions')
            ->with($this->isInstanceOf(OptionsResolver::class))
            ->willReturnCallback(function (OptionsResolver $resolver) {
                $resolver->setRequired('foo');
            })
        ;

        $field->method('getOptions')->willReturn(['foo' => 'bar']);
        $fieldType->method('render')->with($field, 'Value', ['foo' => 'bar'])->willReturn('<strong>Value</strong>');

        $result = $renderer->renderField($gridView, $field, 'Value');
        $this->assertEquals('<strong>Value</strong>', $result);
    }
}
