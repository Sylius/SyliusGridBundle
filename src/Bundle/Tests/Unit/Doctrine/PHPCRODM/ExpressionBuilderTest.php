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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Doctrine\PHPCRODM;

use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\ExpressionBuilder as CollectionsExpressionBuilder;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExpressionBuilder;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExtraComparison;

/**
 * @require Doctrine\ODM\PHPCR\DocumentManagerInterface
 */
final class ExpressionBuilderTest extends TestCase
{
    public function testBuildsAndx(): void
    {
        $expression = $this->createMock(Expression::class);
        $collectionsExpressionBuilder = $this->createMock(CollectionsExpressionBuilder::class);
        $collectionsExpressionBuilder->expects($this->once())->method('andX')->with($expression);

        $expressionBuilder = new ExpressionBuilder($collectionsExpressionBuilder);
        $expressionBuilder->andX($expression);
    }

    public function testBuildsOrx(): void
    {
        $expression = $this->createMock(Expression::class);
        $collectionsExpressionBuilder = $this->createMock(CollectionsExpressionBuilder::class);
        $collectionsExpressionBuilder->expects($this->once())->method('orX')->with($expression);

        $expressionBuilder = new ExpressionBuilder($collectionsExpressionBuilder);
        $expressionBuilder->orX($expression);
    }

    public function testBuildsEquals(): void
    {
        $collectionsExpressionBuilder = $this->createMock(CollectionsExpressionBuilder::class);
        $collectionsExpressionBuilder->expects($this->once())->method('eq')->with('o.foo', 'value');

        $expressionBuilder = new ExpressionBuilder($collectionsExpressionBuilder);
        $expressionBuilder->equals('o.foo', 'value');
    }

    public function testBuildsNotEquals(): void
    {
        $collectionsExpressionBuilder = $this->createMock(CollectionsExpressionBuilder::class);
        $collectionsExpressionBuilder->expects($this->once())->method('neq')->with('o.foo', 'value');

        $expressionBuilder = new ExpressionBuilder($collectionsExpressionBuilder);
        $expressionBuilder->notEquals('o.foo', 'value');
    }

    public function testBuildsLessThanOrEqual(): void
    {
        $collectionsExpressionBuilder = $this->createMock(CollectionsExpressionBuilder::class);
        $collectionsExpressionBuilder->expects($this->once())->method('lte')->with('o.foo', 'value');

        $expressionBuilder = new ExpressionBuilder($collectionsExpressionBuilder);
        $expressionBuilder->lessThanOrEqual('o.foo', 'value');
    }

    public function testBuildsGreaterThan(): void
    {
        $collectionsExpressionBuilder = $this->createMock(CollectionsExpressionBuilder::class);
        $collectionsExpressionBuilder->expects($this->once())->method('gt')->with('o.foo', 'value');

        $expressionBuilder = new ExpressionBuilder($collectionsExpressionBuilder);
        $expressionBuilder->greaterThan('o.foo', 'value');
    }

    public function testBuildsGreaterThanOrEqual(): void
    {
        $collectionsExpressionBuilder = $this->createMock(CollectionsExpressionBuilder::class);
        $collectionsExpressionBuilder->expects($this->once())->method('gte')->with('o.foo', 'value');

        $expressionBuilder = new ExpressionBuilder($collectionsExpressionBuilder);
        $expressionBuilder->greaterThanOrEqual('o.foo', 'value');
    }

    public function testBuildsIn(): void
    {
        $collectionsExpressionBuilder = $this->createMock(CollectionsExpressionBuilder::class);
        $collectionsExpressionBuilder->expects($this->once())->method('in')->with('o.foo', ['value']);

        $expressionBuilder = new ExpressionBuilder($collectionsExpressionBuilder);
        $expressionBuilder->in('o.foo', ['value']);
    }

    public function testBuildsNotIn(): void
    {
        $collectionsExpressionBuilder = $this->createMock(CollectionsExpressionBuilder::class);
        $collectionsExpressionBuilder->expects($this->once())->method('notIn')->with('o.foo', ['value']);

        $expressionBuilder = new ExpressionBuilder($collectionsExpressionBuilder);
        $expressionBuilder->notIn('o.foo', ['value']);
    }

    public function testBuildsIsNull(): void
    {
        $expressionBuilder = new ExpressionBuilder();
        $expr = $expressionBuilder->isNull('o.foo');

        $this->assertEquals(ExtraComparison::IS_NULL, $expr->getOperator());
        $this->assertEquals('o.foo', $expr->getField());
    }

    public function testBuildsIsNotNull(): void
    {
        $expressionBuilder = new ExpressionBuilder();
        $expr = $expressionBuilder->isNotNull('o.foo');

        $this->assertEquals(ExtraComparison::IS_NOT_NULL, $expr->getOperator());
        $this->assertEquals('o.foo', $expr->getField());
    }

    public function testBuildsLike(): void
    {
        $collectionsExpressionBuilder = $this->createMock(CollectionsExpressionBuilder::class);
        $collectionsExpressionBuilder->expects($this->once())->method('contains')->with('o.foo', 'value');

        $expressionBuilder = new ExpressionBuilder($collectionsExpressionBuilder);
        $expressionBuilder->like('o.foo', 'value');
    }

    public function testBuildsNotLike(): void
    {
        $expressionBuilder = new ExpressionBuilder();
        $expr = $expressionBuilder->notLike('o.foo', 'value');

        $this->assertEquals(ExtraComparison::NOT_CONTAINS, $expr->getOperator());
        $this->assertEquals('o.foo', $expr->getField());
    }

    public function testOrdersBy(): void
    {
        $expressionBuilder = new ExpressionBuilder();
        $expressionBuilder->orderBy('o.foo', 'asc');

        $this->assertEquals([
            'o.foo' => 'asc',
        ], $expressionBuilder->getOrderBys());
    }

    public function testAddsOrderBy(): void
    {
        $expressionBuilder = new ExpressionBuilder();
        $expressionBuilder->orderBy('o.foo', 'asc');
        $expressionBuilder->addOrderBy('o.bar', 'desc');

        $this->assertEquals([
            'o.foo' => 'asc',
            'o.bar' => 'desc',
        ], $expressionBuilder->getOrderBys());
    }
}
