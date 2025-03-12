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

namespace spec\Sylius\Component\Grid\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Configuration\GridConfigurationExtender;
use Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandlerInterface;
use Sylius\Component\Grid\Configuration\GridConfigurationSortingHandlerInterface;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\GridProviderInterface;

final class ArrayGridProviderSpec extends ObjectBehavior
{
    function let(
        ArrayToDefinitionConverterInterface $converter,
        GridConfigurationRemovalsHandlerInterface $gridConfigurationRemovalsHandler,
        GridConfigurationSortingHandlerInterface $gridConfigurationSortingHandler,
        Grid $firstGrid,
        Grid $secondGrid,
        Grid $thirdGrid,
        Grid $fourthGrid,
        Grid $fifthGrid,
        Grid $sixthGrid,
        Grid $seventhGrid,
    ): void {
        $converter->convert('sylius_admin_tax_category', ['configuration1'])->willReturn($firstGrid);
        $converter->convert('sylius_admin_product', ['configuration2' => 'foo'])->willReturn($secondGrid);
        $converter->convert('sylius_admin_order', ['configuration3'])->willReturn($thirdGrid);
        $converter->convert('sylius_admin_product_from_taxon', ['configuration4' => 'bar', 'configuration2' => 'foo'])->willReturn($fourthGrid);
        $converter->convert('sylius_admin_book', ['extends' => '404'])->willReturn($fifthGrid);
        $converter->convert('sylius_admin_customer', ['fields' => []])->willReturn($sixthGrid);
        $converter->convert('sylius_admin_book_per_author', [
        'fields' => [
            'title' => ['sortable' => true],
        ],
        'sorting' => [
            'title' => 'asc',
        ]])->willReturn($seventhGrid);

        $gridConfigurationRemovalsHandler->handle(['configuration1'])->willReturn(['configuration1']);
        $gridConfigurationSortingHandler->handle(['configuration1'])->willReturn(['configuration1']);
        $gridConfigurationRemovalsHandler->handle(['configuration2' => 'foo'])->willReturn(['configuration2' => 'foo']);
        $gridConfigurationSortingHandler->handle(['configuration2' => 'foo'])->willReturn(['configuration2' => 'foo']);
        $gridConfigurationRemovalsHandler->handle(['configuration3'])->willReturn(['configuration3']);
        $gridConfigurationSortingHandler->handle(['configuration3'])->willReturn(['configuration3']);
        $gridConfigurationRemovalsHandler->handle(['configuration4' => 'bar', 'configuration2' => 'foo'])->willReturn(['configuration4' => 'bar', 'configuration2' => 'foo']);
        $gridConfigurationSortingHandler->handle(['configuration4' => 'bar', 'configuration2' => 'foo'])->willReturn(['configuration4' => 'bar', 'configuration2' => 'foo']);
        $gridConfigurationRemovalsHandler->handle(['extends' => '404'])->willReturn(['extends' => '404']);
        $gridConfigurationSortingHandler->handle(['extends' => '404'])->willReturn(['extends' => '404']);
        $gridConfigurationRemovalsHandler->handle([
            'fields' => ['customer' => []],
            'removals' => [
                'fields' => ['customer'],
            ],
        ])->willReturn([
            'fields' => [],
        ]);
        $gridConfigurationSortingHandler->handle(['fields' => []])->willReturn(['fields' => []]);
        $gridConfigurationRemovalsHandler->handle([
            'fields' => [
                'title' => [],
            ],
            'sorting' => [
                'title' => 'asc',
            ],
        ])->willReturn([
            'fields' => [
                'title' => [],
            ],
            'sorting' => [
                'title' => 'asc',
            ],
        ]);
        $gridConfigurationSortingHandler->handle([
            'fields' => [
                'title' => [],
            ],
            'sorting' => [
                'title' => 'asc',
            ],
        ])->willReturn([
            'fields' => [
                'title' => ['sortable' => true],
            ],
            'sorting' => [
                'title' => 'asc',
            ],
        ]);

        $this->beConstructedWith(
            $converter,
            [
                'sylius_admin_tax_category' => ['configuration1'],
                'sylius_admin_product' => ['configuration2' => 'foo'],
                'sylius_admin_order' => ['configuration3'],
                'sylius_admin_product_from_taxon' => ['extends' => 'sylius_admin_product', 'configuration4' => 'bar'],
                'sylius_admin_book' => ['extends' => '404'],
                'sylius_admin_customer' => ['fields' => ['customer' => []], 'removals' => ['fields' => ['customer']]],
                'sylius_admin_book_per_author' => [
                    'fields' => [
                        'title' => [],
                    ],
                    'sorting' => [
                        'title' => 'asc',
                    ],
                ],
            ],
            new GridConfigurationExtender(),
            $gridConfigurationRemovalsHandler,
            $gridConfigurationSortingHandler,
        );
    }

    function it_implements_grid_provider_interface(): void
    {
        $this->shouldImplement(GridProviderInterface::class);
    }

    function it_returns_cloned_grid_definition_by_name(Grid $firstGrid, Grid $secondGrid, Grid $thirdGrid): void
    {
        $this->get('sylius_admin_tax_category')->shouldBeLike($firstGrid);
        $this->get('sylius_admin_product')->shouldBeLike($secondGrid);
        $this->get('sylius_admin_order')->shouldBeLike($thirdGrid);
    }

    function it_supports_grid_inheritance(Grid $fourthGrid): void
    {
        $this->get('sylius_admin_product_from_taxon')->shouldBeLike($fourthGrid);
    }

    function it_throws_an_exception_if_grid_does_not_exist(): void
    {
        $this
            ->shouldThrow(new UndefinedGridException('sylius_admin_order_item'))
            ->during('get', ['sylius_admin_order_item'])
        ;
    }

    function it_throws_an_invalid_argument_exception_when_parent_grid_is_not_found(): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('get', ['sylius_admin_book']);
    }

    function it_supports_grid_removals(
        ArrayToDefinitionConverterInterface $converter,
        Grid $sixthGrid,
    ): void {
        $this->get('sylius_admin_customer')->shouldReturn($sixthGrid);
    }

    function it_makes_fields_sortable_if_sorting_is_enabled_for_it(
        ArrayToDefinitionConverterInterface $converter,
        Grid $seventhGrid,
    ): void {
        $this->get('sylius_admin_book_per_author')->shouldReturn($seventhGrid);
    }
}
