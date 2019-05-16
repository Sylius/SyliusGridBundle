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

namespace spec\Sylius\Bundle\GridBundle\Doctrine\ORM;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;

final class ExpressionBuilderSpec extends ObjectBehavior
{
    function let(QueryBuilder $queryBuilder): void
    {
        $this->beConstructedWith($queryBuilder);
    }

    function it_implements_expression_builder_interface(): void
    {
        $this->shouldImplement(ExpressionBuilderInterface::class);
    }

    function it_builds_simple_equals_expression(
        QueryBuilder $queryBuilder,
        Expr $expr
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->getParameter('channel')->willReturn(null);
        $queryBuilder->expr()->willReturn($expr);

        $queryBuilder->setParameter('channel', '1')->shouldBeCalled();

        $expr->eq('o.channel', ':channel')->shouldBeCalled();

        $this->equals('channel', '1');
    }

    function it_builds_equals_expression_with_nested_field(
        QueryBuilder $queryBuilder,
        Expr $expr
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->getParameter('channel_currency')->willReturn(null);
        $queryBuilder->expr()->willReturn($expr);
        $queryBuilder->getDQLParts()->willReturn(['join' => []]);

        $queryBuilder->setParameter('channel_currency', '1')->shouldBeCalled();
        $queryBuilder->innerJoin('o.channel', 'o1')->shouldBeCalled();

        $expr->eq('o1.currency', ':channel_currency')->shouldBeCalled();

        $this->equals('channel.currency', '1');
    }

    function it_builds_equals_expression_with_nested_fields(
        QueryBuilder $queryBuilder,
        Expr $expr
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->getParameter('order_channel_currency')->willReturn(null);
        $queryBuilder->expr()->willReturn($expr);
        $queryBuilder->getDQLParts()->willReturn(['join' => []]);

        $queryBuilder->setParameter('order_channel_currency', '1')->shouldBeCalled();
        $queryBuilder->innerJoin('o.order', 'o1')->shouldBeCalled();
        $queryBuilder->innerJoin('o1.channel', 'o2')->shouldBeCalled();

        $expr->eq('o2.currency', ':order_channel_currency')->shouldBeCalled();

        $this->equals('order.channel.currency', '1');
    }

    function it_builds_equals_expression_with_nested_fields_when_the_alias_is_already_used(
        QueryBuilder $queryBuilder,
        Expr $expr,
        Join $firstJoin,
        Join $secondJoin
    ): void {
        $firstJoin->getAlias()->willReturn('o1');
        $secondJoin->getAlias()->willReturn('o2');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->getParameter('order_channel_currency')->willReturn(null);
        $queryBuilder->expr()->willReturn($expr);
        $queryBuilder->getDQLParts()->willReturn(
            ['join' => ['o' => [$firstJoin]]],
            ['join' => ['o' => [$firstJoin]]],
            ['join' => ['o' => [$firstJoin]]],
            ['join' => ['o' => [$firstJoin, $secondJoin]]]
        );

        $queryBuilder->setParameter('order_channel_currency', '1')->shouldBeCalled();
        $queryBuilder->innerJoin('o.order', 'o2')->shouldBeCalled();
        $queryBuilder->innerJoin('o2.channel', 'o3')->shouldBeCalled();

        $expr->eq('o3.currency', ':order_channel_currency')->shouldBeCalled();

        $this->equals('order.channel.currency', '1');
    }
}
