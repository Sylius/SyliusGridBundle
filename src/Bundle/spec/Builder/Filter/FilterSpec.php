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

namespace Sylius\Bundle\GridBundle\Tests\Unit\Builder\Filter;

use App\Entity\Author;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface;

final class FilterTest extends TestCase
{
    private Filter $filter;

    public function setUp(): void
    {
        $this->filter = Filter::create('search', 'string');
    }

    public function testImplementsAnInterface(): void
    {
        $this->assertInstanceOf(FilterInterface::class, $this->filter);
    }

    public function testHasNoLabelByDefault(): void
    {
        $this->assertNull($this->filter->getLabel());
    }

    public function testSetsStringLabel(): void
    {
        $this->filter->setLabel('Search');

        $this->assertEquals('Search', $this->filter->getLabel());
    }

    public function testSetsBooleanLabel(): void
    {
        $this->filter->setLabel(false);

        $this->assertFalse($this->filter->getLabel());
    }

    public function testSetsNullLabel(): void
    {
        $this->filter->setLabel(null);

        $this->assertNull($this->filter->getLabel());
    }

    public function testIsEnabledByDefault(): void
    {
        $this->assertTrue($this->filter->isEnabled());
    }

    public function testEnablesFilters(): void
    {
        $this->filter->setEnabled(true);

        $this->assertTrue($this->filter->isEnabled());
    }

    public function testDisablesFilters(): void
    {
        $this->filter->setEnabled(false);

        $this->assertFalse($this->filter->isEnabled());
    }

    public function testHasNoTemplateByDefault(): void
    {
        $this->assertNull($this->filter->getTemplate());
    }

    public function testSetsTemplate(): void
    {
        $this->filter->setTemplate('/path/to/template');

        $this->assertEquals('/path/to/template', $this->filter->getTemplate());
    }

    public function testHasNoOptionsByDefault(): void
    {
        $this->assertEquals([], $this->filter->getOptions());
    }

    public function testSetsOptions(): void
    {
        $this->filter->setOptions(['fields' => ['name', 'code']]);

        $this->assertEquals(['fields' => ['name', 'code']], $this->filter->getOptions());
    }

    public function testAddsOptions(): void
    {
        $this->filter->addOption('fields', ['name', 'code']);

        $this->assertEquals(['fields' => ['name', 'code']], $this->filter->getOptions());
    }

    public function testRemoveOption(): void
    {
        $this->filter->addOption('fields', ['name', 'code']);

        $this->filter->removeOption('fields');

        $this->assertEquals([], $this->filter->getOptions());
    }

    public function testSetsFormOptions(): void
    {
        $this->filter->setFormOptions(['multiple' => true]);

        $this->assertEquals(['multiple' => true], $this->filter->getFormOptions());
    }

    public function testAddsFormOptions(): void
    {
        $this->filter
            ->addFormOption('class', Author::class)
            ->addFormOption('multiple', true)
        ;

        $this->assertEquals([
            'class' => Author::class,
            'multiple' => true,
        ], $this->filter->getFormOptions());
    }

    public function testRemovesFormOptions(): void
    {
        $this->filter
            ->addFormOption('class', Author::class)
            ->addFormOption('multiple', true)
            ->removeFormOption('multiple')
        ;

        $this->assertEquals([
            'class' => Author::class,
        ], $this->filter->getFormOptions());
    }

    public function testHasNoCriteriaByDefault(): void
    {
        $this->assertEquals([], $this->filter->getCriteria());
    }

    public function testSetsCriteria(): void
    {
        $this->filter->setCriteria(['name' => 'test']);

        $this->assertEquals(['name' => 'test'], $this->filter->getCriteria());
    }

    public function testHasNoDefaultValueByDefault(): void
    {
        $this->assertNull($this->filter->getDefaultValue());
    }

    public function testsDefaultValueIsMutable(): void
    {
        $this->filter->setDefaultValue(false);

        $this->assertFalse($this->filter->getDefaultValue());
    }
}
