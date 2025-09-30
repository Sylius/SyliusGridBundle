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

namespace Sylius\Component\Grid\Tests\Unit\DataExtractor;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\DataExtractor\PropertyAccessDataExtractor;
use Sylius\Component\Grid\Definition\Field;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final class PropertyAccessDataExtractorTest extends TestCase
{
    private PropertyAccessorInterface|MockObject $propertyAccessorMock;

    private PropertyAccessDataExtractor $propertyAccessDataExtractor;

    protected function setUp(): void
    {
        $this->propertyAccessorMock = $this->createMock(PropertyAccessorInterface::class);
        $this->propertyAccessDataExtractor = new PropertyAccessDataExtractor($this->propertyAccessorMock);
    }

    public function testADataExtractor(): void
    {
        $this->assertInstanceOf(DataExtractorInterface::class, $this->propertyAccessDataExtractor);
    }

    public function testUsesPropertyAccessorToExtractTheData(): void
    {
        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);
        $fieldMock->expects($this->once())->method('getPath')->willReturn('foo');
        $this->propertyAccessorMock->expects($this->once())->method('getValue')->with(['foo' => 'bar'], 'foo')->willReturn('Value');

        $this->assertSame('Value', $this->propertyAccessDataExtractor->get($fieldMock, ['foo' => 'bar']));
    }
}
