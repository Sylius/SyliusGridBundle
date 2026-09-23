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

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
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
        $collectionsExpressionBuilder = new CollectionsExpressionBuilder();
        $expressionBuilder = new ExpressionBuilder($collectionsExpressionBuilder);

        $result = $expressionBuilder->andX(
            $collectionsExpressionBuilder->eq('o.foo', 'value'),
            $collectionsExpressionBuilder->eq('o.bar', 'other'),
        );

        $this->assertInstanceOf(CompositeExpression::class, $result);
        $this->assertSame(CompositeExpression::TYPE_AND, $result->getType());
        $this->assertCount(2, $result->getExpressionList());
    }

    public function testBuildsOrx(): void
    {
        $collectionsExpressionBuilder = new CollectionsExpressionBuilder();
        $expressionBuilder = new ExpressionBuilder($collectionsExpressionBuilder);

        $result = $expressionBuilder->orX(
            $collectionsExpressionBuilder->eq('o.foo', 'value'),
            $collectionsExpressionBuilder->eq('o.bar', 'other'),
        );

        $this->assertInstanceOf(CompositeExpression::class, $result);
        $this->assertSame(CompositeExpression::TYPE_OR, $result->getType());
        $this->assertCount(2, $result->getExpressionList());
    }

    public function testBuildsEquals(): void
    {
        $expressionBuilder = new ExpressionBuilder(new CollectionsExpressionBuilder());

        $result = $expressionBuilder->equals('o.foo', 'value');

        $this->assertInstanceOf(Comparison::class, $result);
        $this->assertSame('o.foo', $result->getField());
        $this->assertSame(Comparison::EQ, $result->getOperator());
        $this->assertSame('value', $result->getValue()->getValue());
    }

    public function testBuildsNotEquals(): void
    {
        $expressionBuilder = new ExpressionBuilder(new CollectionsExpressionBuilder());

        $result = $expressionBuilder->notEquals('o.foo', 'value');

        $this->assertInstanceOf(Comparison::class, $result);
        $this->assertSame('o.foo', $result->getField());
        $this->assertSame(Comparison::NEQ, $result->getOperator());
        $this->assertSame('value', $result->getValue()->getValue());
    }

    public function testBuildsLessThanOrEqual(): void
    {
        $expressionBuilder = new ExpressionBuilder(new CollectionsExpressionBuilder());

        $result = $expressionBuilder->lessThanOrEqual('o.foo', 'value');

        $this->assertInstanceOf(Comparison::class, $result);
        $this->assertSame('o.foo', $result->getField());
        $this->assertSame(Comparison::LTE, $result->getOperator());
        $this->assertSame('value', $result->getValue()->getValue());
    }

    public function testBuildsGreaterThan(): void
    {
        $expressionBuilder = new ExpressionBuilder(new CollectionsExpressionBuilder());

        $result = $expressionBuilder->greaterThan('o.foo', 'value');

        $this->assertInstanceOf(Comparison::class, $result);
        $this->assertSame('o.foo', $result->getField());
        $this->assertSame(Comparison::GT, $result->getOperator());
        $this->assertSame('value', $result->getValue()->getValue());
    }

    public function testBuildsGreaterThanOrEqual(): void
    {
        $expressionBuilder = new ExpressionBuilder(new CollectionsExpressionBuilder());

        $result = $expressionBuilder->greaterThanOrEqual('o.foo', 'value');

        $this->assertInstanceOf(Comparison::class, $result);
        $this->assertSame('o.foo', $result->getField());
        $this->assertSame(Comparison::GTE, $result->getOperator());
        $this->assertSame('value', $result->getValue()->getValue());
    }

    public function testBuildsIn(): void
    {
        $expressionBuilder = new ExpressionBuilder(new CollectionsExpressionBuilder());

        $result = $expressionBuilder->in('o.foo', ['value']);

        $this->assertInstanceOf(Comparison::class, $result);
        $this->assertSame('o.foo', $result->getField());
        $this->assertSame(Comparison::IN, $result->getOperator());
        $this->assertSame(['value'], $result->getValue()->getValue());
    }

    public function testBuildsNotIn(): void
    {
        $expressionBuilder = new ExpressionBuilder(new CollectionsExpressionBuilder());

        $result = $expressionBuilder->notIn('o.foo', ['value']);

        $this->assertInstanceOf(Comparison::class, $result);
        $this->assertSame('o.foo', $result->getField());
        $this->assertSame(Comparison::NIN, $result->getOperator());
        $this->assertSame(['value'], $result->getValue()->getValue());
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
        $expressionBuilder = new ExpressionBuilder(new CollectionsExpressionBuilder());

        $result = $expressionBuilder->like('o.foo', 'value');

        $this->assertInstanceOf(Comparison::class, $result);
        $this->assertSame('o.foo', $result->getField());
        $this->assertSame(Comparison::CONTAINS, $result->getOperator());
        $this->assertSame('value', $result->getValue()->getValue());
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
