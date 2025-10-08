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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\ActionGroup;

final class ActionGroupTest extends TestCase
{
    private ActionGroup $actionGroup;

    protected function setUp(): void
    {
        $this->actionGroup = ActionGroup::named('row');
    }

    public function testHasCode(): void
    {
        $this->assertSame('row', $this->actionGroup->getName());
    }

    public function testDoesNotHaveAnyActionsByDefault(): void
    {
        $this->assertSame([], $this->actionGroup->getActions());
    }

    public function testCanHaveActionDefinitions(): void
    {
        /** @var Action|MockObject $actionMock */
        $actionMock = $this->createMock(Action::class);
        $actionMock->expects($this->once())->method('getName')->willReturn('display_summary');

        $this->actionGroup->addAction($actionMock);

        $this->assertSame($actionMock, $this->actionGroup->getAction('display_summary'));
        $this->assertSame(['display_summary' => $actionMock], $this->actionGroup->getActions());
    }

    public function testCannotHaveTwoActionsWithTheSameName(): void
    {
        /** @var Action|MockObject $firstActionMock */
        $firstActionMock = $this->createMock(Action::class);

        /** @var Action|MockObject $secondActionMock */
        $secondActionMock = $this->createMock(Action::class);

        $firstActionMock->expects($this->once())->method('getName')->willReturn('read_book');
        $secondActionMock->expects($this->once())->method('getName')->willReturn('read_book');

        $this->actionGroup->addAction($firstActionMock);

        $this->expectException(\InvalidArgumentException::class);
        $this->actionGroup->addAction($secondActionMock);
    }

    public function testKnowsIfActionWithGivenNameAlreadyExists(): void
    {
        /** @var Action|MockObject $actionMock */
        $actionMock = $this->createMock(Action::class);
        $actionMock->expects($this->once())->method('getName')->willReturn('read_book');

        $this->actionGroup->addAction($actionMock);

        $this->assertTrue($this->actionGroup->hasAction('read_book'));
        $this->assertFalse($this->actionGroup->hasAction('delete_book'));
    }
}
