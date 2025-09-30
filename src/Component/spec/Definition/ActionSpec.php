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
use Sylius\Component\Grid\Definition\Action;

final class ActionTest extends TestCase
{
    private Action $action;

    protected function setUp(): void
    {
        $this->action = Action::fromNameAndType('view', 'link');
    }

    public function testHasName(): void
    {
        $this->assertSame('view', $this->action->getName());
    }

    public function testHasType(): void
    {
        $this->assertSame('link', $this->action->getType());
    }

    public function testHasNoLabelByDefault(): void
    {
        $this->assertNull($this->action->getLabel());
    }

    public function testItsLabelIsMutable(): void
    {
        $this->action->setLabel('Read book');
        $this->assertSame('Read book', $this->action->getLabel());
    }

    public function testToggleable(): void
    {
        $this->assertTrue($this->action->isEnabled());

        $this->action->setEnabled(false);
        $this->assertFalse($this->action->isEnabled());
        $this->action->setEnabled(true);
        $this->assertTrue($this->action->isEnabled());
    }

    public function testHasNoTemplateByDefault(): void
    {
        $this->assertNull($this->action->getTemplate());
    }

    public function testItsTemplateIsMutable(): void
    {
        $this->action->setTemplate('path/to/action/template');
        $this->assertSame('path/to/action/template', $this->action->getTemplate());
    }

    public function testHasNoIconByDefault(): void
    {
        $this->assertNull($this->action->getIcon());
    }

    public function testItsIconIsMutable(): void
    {
        $this->action->setIcon('checkmark');
        $this->assertSame('checkmark', $this->action->getIcon());
    }

    public function testHasNoOptionsByDefault(): void
    {
        $this->assertSame([], $this->action->getOptions());
    }

    public function testCanHaveOptions(): void
    {
        $this->action->setOptions(['route' => 'sylius_admin_product_update']);
        $this->assertSame(['route' => 'sylius_admin_product_update'], $this->action->getOptions());
    }

    public function testHasLastPositionByDefault(): void
    {
        $this->assertSame(100, $this->action->getPosition());
    }

    public function testItsPositionIsMutable(): void
    {
        $this->action->setPosition(1);
        $this->assertSame(1, $this->action->getPosition());
    }
}
