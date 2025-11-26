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
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\ExpressionBuilder as CollectionsExpressionBuilder;
use Doctrine\ODM\PHPCR\Query\Builder\ConstraintComparison;
use Doctrine\ODM\PHPCR\Query\Builder\OrderBy;
use Doctrine\ODM\PHPCR\Query\Builder\Ordering;
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use Doctrine\ODM\PHPCR\Query\Builder\WhereOr;
use Doctrine\ODM\PHPCR\Query\Query;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\DataSource;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExpressionBuilder;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExpressionBuilderInterface;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Parameters;

/**
 * @require Doctrine\ODM\PHPCR\DocumentManagerInterface
 */
final class DataSourceTest extends TestCase
{
    public function testImplementsDataSource(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $dataSource = new DataSource($queryBuilder, $expressionBuilder);

        $this->assertInstanceOf(DataSourceInterface::class, $dataSource);
    }

    public function testShouldRestrictWithOrCondition(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $comparison = $this->createMock(Comparison::class);
        $value = $this->createMock(\Doctrine\Common\Collections\Expr\Value::class);
        $constraint = $this->createMock(WhereOr::class);
        $comparisonConstraint = $this->createMock(ConstraintComparison::class);

        $queryBuilder->expects($this->once())->method('orWhere')->willReturn($constraint);
        $value->expects($this->once())->method('getValue')->willReturn('value');
        $comparison->expects($this->once())->method('getValue')->willReturn($value);
        $comparison->expects($this->once())->method('getField')->willReturn('foo');
        $comparison->expects($this->once())->method('getOperator')->willReturn('=');
        $constraint->expects($this->once())->method('eq')->willReturn($comparisonConstraint);
        $comparisonConstraint->expects($this->once())->method('field')->with('o.foo')->willReturn($comparisonConstraint);
        $comparisonConstraint->expects($this->once())->method('literal')->with('value')->willReturn($comparisonConstraint);
        $comparisonConstraint->expects($this->once())->method('end');

        $dataSource = new DataSource($queryBuilder, $expressionBuilder);
        $dataSource->restrict($comparison, DataSourceInterface::CONDITION_OR);
    }

    public function testShouldThrowAnExceptionIfAnUnknownConditionIsPassed(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $comparison = $this->createMock(Comparison::class);

        $dataSource = new DataSource($queryBuilder, $expressionBuilder);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unknown restrict condition "foo"');

        $dataSource->restrict($comparison, 'foo');
    }

    public function testShouldReturnTheExpressionBuilder(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $dataSource = new DataSource($queryBuilder, $expressionBuilder);

        $this->assertSame($expressionBuilder, $dataSource->getExpressionBuilder());
    }

    public function testShouldGetTheData(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $query = $this->createMock(Query::class);
        $orderBy = $this->createMock(OrderBy::class);

        $expressionBuilder->expects($this->once())->method('getOrderBys')->willReturn([]);
        $queryBuilder->expects($this->once())->method('orderBy')->willReturn($orderBy);

        $dataSource = new DataSource($queryBuilder, $expressionBuilder);
        $data = $dataSource->getData(new Parameters(['page' => '1']));

        $this->assertInstanceOf(Pagerfanta::class, $data);
        $this->assertEquals(1, $data->getCurrentPage());
        $this->assertFalse($data->getNormalizeOutOfRangePages());
    }

    public function testShouldSetTheOrderOnTheQueryBuilder(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $query = $this->createMock(Query::class);
        $orderBy = $this->createMock(OrderBy::class);
        $ordering = $this->createMock(Ordering::class);

        $expressionBuilder->expects($this->once())->method('getOrderBys')->willReturn([
            'foo' => 'asc',
            'bar' => 'desc',
        ]);
        $queryBuilder->expects($this->once())->method('orderBy')->willReturn($orderBy);
        $orderBy->expects($this->once())->method('asc')->willReturn($ordering);
        $orderBy->expects($this->once())->method('desc')->willReturn($ordering);
        $ordering->expects($this->exactly(2))->method('field')->willReturnCallback(function ($field) use ($ordering) {
            static $callCount = 0;
            $expectedFields = ['o.foo', 'o.bar'];
            $this->assertEquals($expectedFields[$callCount++], $field);

            return $ordering;
        });

        $dataSource = new DataSource($queryBuilder, $expressionBuilder);
        $data = $dataSource->getData(new Parameters(['page' => '1']));

        $this->assertInstanceOf(Pagerfanta::class, $data);
    }

    public function testShouldSetTheOrderOnTheQueryBuilderAsFieldsOnly(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $query = $this->createMock(Query::class);
        $orderBy = $this->createMock(OrderBy::class);
        $ordering = $this->createMock(Ordering::class);

        $expressionBuilder->expects($this->once())->method('getOrderBys')->willReturn([
            'foo',
            'bar',
        ]);
        $queryBuilder->expects($this->once())->method('orderBy')->willReturn($orderBy);
        $orderBy->expects($this->exactly(2))->method('asc')->willReturn($ordering);
        $ordering->expects($this->exactly(2))->method('field')->willReturnCallback(function ($field) use ($ordering) {
            static $callCount = 0;
            $expectedFields = ['o.foo', 'o.bar'];
            $this->assertEquals($expectedFields[$callCount++], $field);

            return $ordering;
        });

        $dataSource = new DataSource($queryBuilder, $expressionBuilder);
        $data = $dataSource->getData(new Parameters(['page' => '1']));

        $this->assertInstanceOf(Pagerfanta::class, $data);
    }

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

        $this->assertEquals(\Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExtraComparison::IS_NULL, $expr->getOperator());
        $this->assertEquals('o.foo', $expr->getField());
    }

    public function testBuildsIsNotNull(): void
    {
        $expressionBuilder = new ExpressionBuilder();
        $expr = $expressionBuilder->isNotNull('o.foo');

        $this->assertEquals(\Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExtraComparison::IS_NOT_NULL, $expr->getOperator());
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

        $this->assertEquals(\Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExtraComparison::NOT_CONTAINS, $expr->getOperator());
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
