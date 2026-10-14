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

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Filter;

final class FilterTest extends TestCase
{
    private Filter $filter;

    protected function setUp(): void
    {
        $this->filter = Filter::fromNameAndType('keywords', 'string');
    }

    public function testHasName(): void
    {
        $this->assertSame('keywords', $this->filter->getName());
    }

    public function testHasType(): void
    {
        $this->assertSame('string', $this->filter->getType());
    }

    public function testHasLabelWhichDefaultsToName(): void
    {
        $this->assertSame('keywords', $this->filter->getLabel());

        $this->filter->setLabel('Search by keyword');
        $this->assertSame('Search by keyword', $this->filter->getLabel());
    }

    public function testHasNoTemplateByDefault(): void
    {
        $this->assertNull($this->filter->getTemplate());
    }

    public function testItsTemplateIsMutable(): void
    {
        $this->filter->setTemplate('@SyliusGrid/Filter/template.html.twig');
        $this->assertSame('@SyliusGrid/Filter/template.html.twig', $this->filter->getTemplate());
    }

    public function testHasNoOptionsByDefault(): void
    {
        $this->assertSame([], $this->filter->getOptions());
    }

    public function testCanHaveOptions(): void
    {
        $this->filter->setOptions(['fields' => ['firstName', 'lastName', 'email']]);
        $this->assertSame(['fields' => ['firstName', 'lastName', 'email']], $this->filter->getOptions());
    }

    public function testHasLastPositionByDefault(): void
    {
        $this->assertSame(100, $this->filter->getPosition());
    }

    public function testItsPositionIsMutable(): void
    {
        $this->filter->setPosition(1);
        $this->assertSame(1, $this->filter->getPosition());
    }

    public function testHasNoCriteriaByDefault(): void
    {
        $this->assertNull($this->filter->getCriteria());
    }

    public function testItsCriteriaIsMutable(): void
    {
        $this->filter->setCriteria('false');
        $this->assertSame('false', $this->filter->getCriteria());

        $this->filter->setCriteria(['type' => 'contains']);
        $this->assertSame(['type' => 'contains'], $this->filter->getCriteria());
    }

    public function testCanBeConvertedToAnArrayAndBack(): void
    {
        $filter = Filter::fromNameAndType('author', 'entity');
        $filter->setLabel('app.ui.author');
        $filter->setEnabled(false);
        $filter->setTemplate('filter/author.html.twig');
        $filter->setOptions(['fields' => ['author.id']]);
        $filter->setFormOptions(['class' => 'App\\Entity\\Author']);
        $filter->setPosition(3);
        $filter->setCriteria(['author' => '42']);

        $this->assertEquals($filter, Filter::fromArray($filter->toArray()));
    }

    public function testLabelDefaultsToNameWhenCreatedFromAnArrayWithoutLabel(): void
    {
        $filter = Filter::fromArray(['name' => 'title', 'type' => 'string']);

        $this->assertSame('title', $filter->getLabel());
        $this->assertTrue($filter->isEnabled());
        $this->assertNull($filter->getTemplate());
        $this->assertSame([], $filter->getFormOptions());
        $this->assertSame(100, $filter->getPosition());
    }

    public function testCannotBeCreatedFromAnArrayWithoutType(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @phpstan-ignore argument.type */
        Filter::fromArray(['name' => 'title']);
    }

    public function testCanBeConvertedToAnArrayAndBackWithScalarCriteria(): void
    {
        $filter = Filter::fromNameAndType('enabled', 'boolean');
        $filter->setCriteria(true);

        $this->assertEquals($filter, Filter::fromArray($filter->toArray()));
    }

    public function testCannotBeCreatedFromAnArrayWithNonSerializableCriteria(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The criteria of the "author" filter must be null, a scalar or an array, "stdClass" given.');

        /** @phpstan-ignore argument.type */
        Filter::fromArray(['name' => 'author', 'type' => 'entity', 'criteria' => new \stdClass()]);
    }
}
