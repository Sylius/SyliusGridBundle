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
use Sylius\Bundle\GridBundle\Config\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Config\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Config\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Config\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Config\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Config\Builder\Field\Field;
use Sylius\Bundle\GridBundle\Config\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Config\Builder\GridBuilder;
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
            ->AddFilter(Filter::create('search', 'string'))
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
                            ]
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

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
