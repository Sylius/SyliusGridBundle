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
use Doctrine\ORM\Query\Expr\Literal;
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

    function it_builds_not_equals_expression_with_nested_fields(
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

        $expr->neq('o2.currency', ':order_channel_currency')->shouldBeCalled();

        $this->notEquals('order.channel.currency', '1');
    }

    function it_builds_less_than_expression_with_nested_fields(
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

        $expr->lt('o2.currency', ':order_channel_currency')->shouldBeCalled();

        $this->lessThan('order.channel.currency', '1');
    }

    function it_builds_less_than_or_equal_expression_with_nested_fields(
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

        $expr->lte('o2.currency', ':order_channel_currency')->shouldBeCalled();

        $this->lessThanOrEqual('order.channel.currency', '1');
    }

    function it_builds_greater_than_expression_with_nested_fields(
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

        $expr->gt('o2.currency', ':order_channel_currency')->shouldBeCalled();

        $this->greaterThan('order.channel.currency', '1');
    }

    function it_builds_greater_than_or_equal_expression_with_nested_fields(
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

        $expr->gte('o2.currency', ':order_channel_currency')->shouldBeCalled();

        $this->greaterThanOrEqual('order.channel.currency', '1');
    }

    function it_builds_in_expression_with_nested_fields(
        QueryBuilder $queryBuilder,
        Expr $expr
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->expr()->willReturn($expr);
        $queryBuilder->getDQLParts()->willReturn(['join' => []]);

        $queryBuilder->innerJoin('o.order', 'o1')->shouldBeCalled();
        $queryBuilder->innerJoin('o1.channel', 'o2')->shouldBeCalled();

        $expr->in('o2.currency', ['1', '2'])->shouldBeCalled();

        $this->in('order.channel.currency', ['1', '2']);
    }

    function it_builds_not_in_expression_with_nested_fields(
        QueryBuilder $queryBuilder,
        Expr $expr
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->expr()->willReturn($expr);
        $queryBuilder->getDQLParts()->willReturn(['join' => []]);

        $queryBuilder->innerJoin('o.order', 'o1')->shouldBeCalled();
        $queryBuilder->innerJoin('o1.channel', 'o2')->shouldBeCalled();

        $expr->notIn('o2.currency', ['1', '2'])->shouldBeCalled();

        $this->notIn('order.channel.currency', ['1', '2']);
    }

    function it_builds_is_null_expression_with_nested_fields(
        QueryBuilder $queryBuilder,
        Expr $expr
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->expr()->willReturn($expr);
        $queryBuilder->getDQLParts()->willReturn(['join' => []]);

        $queryBuilder->innerJoin('o.order', 'o1')->shouldBeCalled();
        $queryBuilder->innerJoin('o1.channel', 'o2')->shouldBeCalled();

        $expr->isNull('o2.currency')->shouldBeCalled();

        $this->isNull('order.channel.currency');
    }

    function it_builds_is_not_null_expression_with_nested_fields(
        QueryBuilder $queryBuilder,
        Expr $expr
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->expr()->willReturn($expr);
        $queryBuilder->getDQLParts()->willReturn(['join' => []]);

        $queryBuilder->innerJoin('o.order', 'o1')->shouldBeCalled();
        $queryBuilder->innerJoin('o1.channel', 'o2')->shouldBeCalled();

        $expr->isNotNull('o2.currency')->shouldBeCalled();

        $this->isNotNull('order.channel.currency');
    }

    function it_builds_like_expression_with_nested_fields(
        QueryBuilder $queryBuilder,
        Expr $expr,
        Literal $literal
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->expr()->willReturn($expr);
        $queryBuilder->getDQLParts()->willReturn(['join' => []]);

        $expr->literal('US')->willReturn($literal);

        $queryBuilder->innerJoin('o.order', 'o1')->shouldBeCalled();
        $queryBuilder->innerJoin('o1.channel', 'o2')->shouldBeCalled();

        $expr->like('o2.currency', $literal)->shouldBeCalled();

        $this->like('order.channel.currency', 'US');
    }

    function it_builds_not_like_expression_with_nested_fields(
        QueryBuilder $queryBuilder,
        Expr $expr,
        Literal $literal
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->expr()->willReturn($expr);
        $queryBuilder->getDQLParts()->willReturn(['join' => []]);

        $expr->literal('US')->willReturn($literal);

        $queryBuilder->innerJoin('o.order', 'o1')->shouldBeCalled();
        $queryBuilder->innerJoin('o1.channel', 'o2')->shouldBeCalled();

        $expr->notLike('o2.currency', $literal)->shouldBeCalled();

        $this->notLike('order.channel.currency', 'US');
    }

    function it_orders_by_nested_field(
        QueryBuilder $queryBuilder
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->getDQLParts()->willReturn(['join' => []]);

        $queryBuilder->innerJoin('o.order', 'o1')->shouldBeCalled();
        $queryBuilder->innerJoin('o1.channel', 'o2')->shouldBeCalled();

        $queryBuilder->orderBy('o2.currency', 'DESC')->shouldBeCalled();

        $this->orderBy('order.channel.currency', 'DESC');
    }

    function it_adds_order_by_nested_field(
        QueryBuilder $queryBuilder
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->getDQLParts()->willReturn(['join' => []]);

        $queryBuilder->innerJoin('o.order', 'o1')->shouldBeCalled();
        $queryBuilder->innerJoin('o1.channel', 'o2')->shouldBeCalled();

        $queryBuilder->addOrderBy('o2.currency', 'DESC')->shouldBeCalled();

        $this->addOrderBy('order.channel.currency', 'DESC');
    }
}
