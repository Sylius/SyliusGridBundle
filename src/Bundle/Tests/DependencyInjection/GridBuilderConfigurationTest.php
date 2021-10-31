<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\GridBundle\Tests\DependencyInjection;

use App\Entity\Book;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\Field;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\DependencyInjection\Configuration;
use Sylius\Bundle\GridBundle\Doctrine\ORM\Driver;

final class GridBuilderConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_builds_grid_with_only_a_name(): void
    {
        $gridBuilder = GridBuilder::create('app_admin_book');

        $this->assertProcessedConfigurationEquals(
            [[
                'grids' => [
                    'app_admin_book' => $gridBuilder->toArray(),
                ],
            ]],
            [
                'grids' => [
                    'app_admin_book' => [
                        'driver' => [
                            'name' => Driver::NAME,
                            'options' => [],
                        ],
                    ],
                ],
            ],
            'grids.*.driver'
        );
    }

    /**
     * @test
     */
    public function it_builds_grid_with_resource_class(): void
    {
        $gridBuilder = GridBuilder::create('app_admin_book', Book::class);

        $this->assertProcessedConfigurationEquals(
            [[
                'grids' => [
                    'app_admin_book' => $gridBuilder->toArray(),
                ],
            ]],
            [
                'grids' => [
                    'app_admin_book' => [
                        'driver' => [
                            'name' => Driver::NAME,
                            'options' => [
                                'class' => Book::class,
                            ],
                        ],
                    ],
                ],
            ],
            'grids.*.driver'
        );
    }

    /**
     * @test
     */
    public function it_builds_grid_with_filters(): void
    {
        $gridBuilder = GridBuilder::create('app_admin_book', Book::class)
            ->addFilter(Filter::create('search', 'string'))
        ;

        $this->assertProcessedConfigurationEquals(
            [[
                'grids' => [
                    'app_admin_book' => $gridBuilder->toArray(),
                ],
            ]],
            [
                'grids' => [
                    'app_admin_book' => [
                        'driver' => [
                            'name' => Driver::NAME,
                            'options' => [
                                'class' => Book::class,
                            ],
                        ],
                        'sorting' => [],
                        'limits' => [10, 25, 50],
                        'fields' => [],
                        'filters' => [
                            'search' => [
                                'type' => 'string',
                                'enabled' => true,
                                'position' => 100,
                                'options' => [],
                                'form_options' => [],
                            ],
                        ],
                        'actions' => [],
                    ],
                ],
            ],
            'grids'
        );
    }

    /**
     * @test
     */
    public function it_builds_grid_with_fields(): void
    {
        $gridBuilder = GridBuilder::create('app_admin_book', Book::class)
            ->addField(Field::create('name', 'string'))
            ->addField(Field::create('author', 'twig')
                ->setOptions(['template' => 'admin/book/grid/field/author.html.twig'])
            )
        ;

        $this->assertProcessedConfigurationEquals(
            [[
                'grids' => [
                    'app_admin_book' => $gridBuilder->toArray(),
                ],
            ]],
            [
                'grids' => [
                    'app_admin_book' => [
                        'driver' => [
                            'name' => Driver::NAME,
                            'options' => [
                                'class' => Book::class,
                            ],
                        ],
                        'sorting' => [],
                        'limits' => [10, 25, 50],
                        'fields' => [
                            'name' => [
                                'type' => 'string',
                                'enabled' => true,
                                'position' => 100,
                                'options' => [],
                            ],
                            'author' => [
                                'type' => 'twig',
                                'enabled' => true,
                                'position' => 100,
                                'options' => [
                                    'template' => 'admin/book/grid/field/author.html.twig',
                                ],
                            ],
                        ],
                        'filters' => [],
                        'actions' => [],
                    ],
                ],
            ],
            'grids'
        );
    }

    /**
     * @test
     */
    public function it_builds_grid_with_predefined_fields(): void
    {
        $gridBuilder = GridBuilder::create('app_admin_book', Book::class)
            ->addField(StringField::create('name'))
            ->addField(TwigField::create('author', 'admin/book/grid/field/author.html.twig'))
            ->addField(DateTimeField::create('createdAt'))
        ;

        $this->assertProcessedConfigurationEquals(
            [[
                'grids' => [
                    'app_admin_book' => $gridBuilder->toArray(),
                ],
            ]],
            [
                'grids' => [
                    'app_admin_book' => [
                        'driver' => [
                            'name' => Driver::NAME,
                            'options' => [
                                'class' => Book::class,
                            ],
                        ],
                        'sorting' => [],
                        'limits' => [10, 25, 50],
                        'fields' => [
                            'name' => [
                                'type' => 'string',
                                'enabled' => true,
                                'position' => 100,
                                'options' => [],
                            ],
                            'author' => [
                                'type' => 'twig',
                                'enabled' => true,
                                'position' => 100,
                                'options' => [
                                    'template' => 'admin/book/grid/field/author.html.twig',
                                ],
                            ],
                            'createdAt' => [
                                'type' => 'datetime',
                                'enabled' => true,
                                'position' => 100,
                                'options' => [
                                    'format' => 'Y-m-d H:i:s',
                                ],
                            ],
                        ],
                        'filters' => [],
                        'actions' => [],
                    ],
                ],
            ],
            'grids'
        );
    }

    /**
     * @test
     */
    public function it_builds_grid_with_actions(): void
    {
        $gridBuilder = GridBuilder::create('app_admin_book', Book::class)
            ->addAction(Action::create('create', 'create'), 'main')
            ->addAction(Action::create('update', 'update'), 'item')
            ->addAction(Action::create('delete', 'delete'), 'item')
            ->addAction(Action::create('authors', 'links'), 'subitem')
            ->addAction(Action::create('delete', 'delete'), 'bulk')
        ;

        $this->assertProcessedConfigurationEquals(
            [[
                'grids' => [
                    'app_admin_book' => $gridBuilder->toArray(),
                ],
            ]],
            [
                'grids' => [
                    'app_admin_book' => [
                        'driver' => [
                            'name' => Driver::NAME,
                            'options' => [
                                'class' => Book::class,
                            ],
                        ],
                        'sorting' => [],
                        'limits' => [10, 25, 50],
                        'fields' => [],
                        'filters' => [],
                        'actions' => [
                            'main' => [
                                'create' => [
                                    'type' => 'create',
                                    'enabled' => true,
                                    'position' => 100,
                                    'options' => [],
                                ],
                            ],
                            'item' => [
                                'update' => [
                                    'type' => 'update',
                                    'enabled' => true,
                                    'position' => 100,
                                    'options' => [],
                                ],
                                'delete' => [
                                    'type' => 'delete',
                                    'enabled' => true,
                                    'position' => 100,
                                    'options' => [],
                                ],
                            ],
                            'bulk' => [
                                'delete' => [
                                    'type' => 'delete',
                                    'enabled' => true,
                                    'position' => 100,
                                    'options' => [],
                                ],
                            ],
                            'subitem' => [
                                'authors' => [
                                    'type' => 'links',
                                    'enabled' => true,
                                    'position' => 100,
                                    'options' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'grids'
        );
    }

    /**
     * @test
     */
    public function it_builds_grid_with_predefined_actions(): void
    {
        $gridBuilder = GridBuilder::create('app_admin_book', Book::class)
            ->addAction(CreateAction::create(), 'main')
            ->addAction(ShowAction::create(), 'item')
            ->addAction(UpdateAction::create(), 'item')
            ->addAction(DeleteAction::create(), 'item')
            ->addAction(DeleteAction::create(), 'bulk')
        ;

        $this->assertProcessedConfigurationEquals(
            [[
                'grids' => [
                    'app_admin_book' => $gridBuilder->toArray(),
                ],
            ]],
            [
                'grids' => [
                    'app_admin_book' => [
                        'driver' => [
                            'name' => Driver::NAME,
                            'options' => [
                                'class' => Book::class,
                            ],
                        ],
                        'sorting' => [],
                        'limits' => [10, 25, 50],
                        'fields' => [],
                        'filters' => [],
                        'actions' => [
                            'main' => [
                                'create' => [
                                    'type' => 'create',
                                    'enabled' => true,
                                    'position' => 100,
                                    'options' => [],
                                    'label' => 'sylius.ui.create',
                                ],
                            ],
                            'item' => [
                                'show' => [
                                    'type' => 'show',
                                    'enabled' => true,
                                    'position' => 100,
                                    'options' => [],
                                    'label' => 'sylius.ui.show'
                                ],
                                'update' => [
                                    'type' => 'update',
                                    'enabled' => true,
                                    'position' => 100,
                                    'options' => [],
                                    'label' => 'sylius.ui.update'
                                ],
                                'delete' => [
                                    'type' => 'delete',
                                    'enabled' => true,
                                    'position' => 100,
                                    'options' => [],
                                    'label' => 'sylius.ui.delete'
                                ],
                            ],
                            'bulk' => [
                                'delete' => [
                                    'type' => 'delete',
                                    'enabled' => true,
                                    'position' => 100,
                                    'options' => [],
                                    'label' => 'sylius.ui.delete'
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'grids'
        );
    }

    /**
     * @test
     */
    public function it_builds_grid_with_predefined_action_groups(): void
    {
        $gridBuilder = GridBuilder::create('app_admin_book', Book::class)
            ->addActionGroup(MainActionGroup::create(CreateAction::create()))
            ->addActionGroup(ItemActionGroup::create(
                ShowAction::create(),
                UpdateAction::create(),
                DeleteAction::create(),
            ))
            ->addActionGroup(BulkActionGroup::create(DeleteAction::create()))
        ;

        $this->assertProcessedConfigurationEquals(
            [[
                'grids' => [
                    'app_admin_book' => $gridBuilder->toArray(),
                ],
            ]],
            [
                'grids' => [
                    'app_admin_book' => [
                        'driver' => [
                            'name' => Driver::NAME,
                            'options' => [
                                'class' => Book::class,
                            ],
                        ],
                        'sorting' => [],
                        'limits' => [10, 25, 50],
                        'fields' => [],
                        'filters' => [],
                        'actions' => [
                            'main' => [
                                'create' => [
                                    'type' => 'create',
                                    'enabled' => true,
                                    'position' => 100,
                                    'options' => [],
                                    'label' => 'sylius.ui.create',
                                ],
                            ],
                            'item' => [
                                'show' => [
                                    'type' => 'show',
                                    'enabled' => true,
                                    'position' => 100,
                                    'options' => [],
                                    'label' => 'sylius.ui.show'
                                ],
                                'update' => [
                                    'type' => 'update',
                                    'enabled' => true,
                                    'position' => 100,
                                    'options' => [],
                                    'label' => 'sylius.ui.update'
                                ],
                                'delete' => [
                                    'type' => 'delete',
                                    'enabled' => true,
                                    'position' => 100,
                                    'options' => [],
                                    'label' => 'sylius.ui.delete'
                                ],
                            ],
                            'bulk' => [
                                'delete' => [
                                    'type' => 'delete',
                                    'enabled' => true,
                                    'position' => 100,
                                    'options' => [],
                                    'label' => 'sylius.ui.delete'
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'grids'
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
