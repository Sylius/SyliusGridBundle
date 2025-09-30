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

namespace Sylius\Component\Grid\Tests\Unit\Definition;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\ActionGroup;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverter;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ArrayToDefinitionConverterTest extends TestCase
{
    private EventDispatcherInterface|MockObject $eventDispatcherMock;

    private ArrayToDefinitionConverter $arrayToDefinitionConverter;

    protected function setUp(): void
    {
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->arrayToDefinitionConverter = new ArrayToDefinitionConverter($this->eventDispatcherMock);
    }

    public function testImplementsArrayToDefinitionConverter(): void
    {
        $this->assertInstanceOf(ArrayToDefinitionConverterInterface::class, $this->arrayToDefinitionConverter);
    }

    public function testConvertsAnArrayToGridDefinition(): void
    {
        $grid = Grid::fromCodeAndDriverConfiguration(
            'sylius_admin_tax_category',
            'doctrine/orm',
            ['resource' => 'sylius.tax_category'],
        );

        $grid->setSorting(['code' => 'desc']);

        $grid->setLimits([9, 18]);

        $grid->setProvider('App\Provider');

        $codeField = Field::fromNameAndType('code', 'string');
        $codeField->setLabel('System Code');
        $codeField->setPath('method.code');
        $codeField->setOptions(['template' => 'bar.html.twig']);
        $codeField->setSortable('code');

        $grid->addField($codeField);

        $enabledField = Field::fromNameAndType('enabled', 'boolean');
        $enabledField->setLabel('Enabled');
        $enabledField->setPath('method.enabled');
        $enabledField->setSortable('enabled');

        $grid->addField($enabledField);

        $statusField = Field::fromNameAndType('status', 'string');
        $statusField->setLabel('Status');
        $statusField->setSortable('status');

        $grid->addField($statusField);

        $nameField = Field::fromNameAndType('name', 'string');
        $nameField->setLabel('Name');
        $nameField->setSortable('name');

        $grid->addField($nameField);

        $titleField = Field::fromNameAndType('title', 'string');
        $titleField->setLabel('Title');

        $grid->addField($titleField);

        $viewAction = Action::fromNameAndType('view', 'link');
        $viewAction->setLabel('Display Tax Category');
        $viewAction->setTemplate('path/to/action/template');
        $viewAction->setOptions(['foo' => 'bar']);
        $defaultActionGroup = ActionGroup::named('default');
        $defaultActionGroup->addAction($viewAction);

        $grid->addActionGroup($defaultActionGroup);

        $filter = Filter::fromNameAndType('enabled', 'boolean');
        $filter->setOptions(['fields' => ['firstName', 'lastName']]);
        $filter->setCriteria('true');
        $grid->addFilter($filter);

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(GridDefinitionConverterEvent::class), 'sylius.grid.admin_tax_category');

        $definitionArray = [
            'driver' => [
                'name' => 'doctrine/orm',
                'options' => ['resource' => 'sylius.tax_category'],
            ],
            'provider' => 'App\Provider',
            'sorting' => [
                'code' => 'desc',
            ],
            'limits' => [9, 18],
            'fields' => [
                'code' => [
                    'type' => 'string',
                    'label' => 'System Code',
                    'path' => 'method.code',
                    'sortable' => 'code',
                    'options' => [
                        'template' => 'bar.html.twig',
                    ],
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'label' => 'Enabled',
                    'path' => 'method.enabled',
                    'sortable' => true,
                ],
                'status' => [
                    'type' => 'string',
                    'label' => 'Status',
                    'sortable' => true,
                ],
                'name' => [
                    'type' => 'string',
                    'label' => 'Name',
                    'sortable' => null,
                ],
                'title' => [
                    'type' => 'string',
                    'label' => 'Title',
                    'sortable' => false,
                ],
            ],
            'filters' => [
                'enabled' => [
                    'type' => 'boolean',
                    'options' => [
                        'fields' => ['firstName', 'lastName'],
                    ],
                    'default_value' => 'true',
                ],
            ],
            'actions' => [
                'default' => [
                    'view' => [
                        'type' => 'link',
                        'label' => 'Display Tax Category',
                        'options' => [
                            'foo' => 'bar',
                        ],
                        'template' => 'path/to/action/template',
                    ],
                ],
            ],
        ];

        $gridDefinition = $this->arrayToDefinitionConverter->convert('sylius_admin_tax_category', $definitionArray);

        $this->assertEquals($grid, $gridDefinition);
    }
}
