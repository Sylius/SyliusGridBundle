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

namespace Sylius\Bundle\GridBundle\Tests\Functional\Twig\Component;

use App\Story\AppStory;
use App\Twig\BookGridComponent;
use PHPUnit\Framework\Attributes\CoversClass;
use Sylius\Component\Grid\Twig\Component\ComponentWithGridTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(ComponentWithGridTrait::class)]
final class ComponentWithGridTraitTest extends KernelTestCase
{
    use InteractsWithLiveComponents;
    use Factories;
    use ResetDatabase;

    private const FIRST_PAGE_BOOK_TITLE = 'A STUDY IN SCARLET';

    private const ANOTHER_BOOK_TITLE_ON_FIRST_PAGE = 'BOOK 1';

    private const SECOND_PAGE_BOOK_TITLE = 'BOOK 17';

    private const THIRD_PAGE_BOOK_TITLE = 'BOOK 26';

    private const LAST_PAGE_BOOK_TITLE = 'THE LOST WORLD';

    protected function setUp(): void
    {
        AppStory::load();
    }

    public function testRender(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
        );

        $this->assertStringContainsString(self::FIRST_PAGE_BOOK_TITLE, $component->render()->toString());
    }

    public function testLoadWithCriteria(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['criteria' => ['title' => ['value' => 'Jurassic']]],
        );

        $html = $component->render()->toString();

        $this->assertStringContainsString('JURASSIC PARK', $html);
        $this->assertStringNotContainsString(self::FIRST_PAGE_BOOK_TITLE, $html);
    }

    public function testChangeCriteria(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['page' => 2],
        );

        $component->call('changeCriteria', ['criteria' => ['title' => ['value' => 'Jurassic']]]);

        $html = $component->render()->toString();

        $this->assertStringContainsString('JURASSIC PARK', $html);
        $this->assertStringNotContainsString(self::FIRST_PAGE_BOOK_TITLE, $html);
    }

    public function testLoadWithSorting(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['sorting' => ['title' => 'desc']],
        );

        $this->assertStringContainsString(self::LAST_PAGE_BOOK_TITLE, $component->render()->toString());
    }

    public function testChangeSorting(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
        );

        $component->call('changeCriteria', ['criteria' => ['title' => ['value' => 'Jurassic']]]);

        $html = $component->render()->toString();

        $this->assertStringContainsString('JURASSIC PARK', $html);
        $this->assertStringNotContainsString(self::FIRST_PAGE_BOOK_TITLE, $html);
    }

    public function testLoadWithAPageLimit(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['limit' => 1],
        );

        $html = $component->render()->toString();

        $this->assertStringContainsString(self::FIRST_PAGE_BOOK_TITLE, $html);
        $this->assertStringNotContainsString(self::ANOTHER_BOOK_TITLE_ON_FIRST_PAGE, $html);
    }

    public function testChangePageLimit(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
        );

        $component->call('changeLimit', ['limit' => 1]);

        $html = $component->render()->toString();

        $this->assertStringContainsString(self::FIRST_PAGE_BOOK_TITLE, $html);
        $this->assertStringNotContainsString(self::ANOTHER_BOOK_TITLE_ON_FIRST_PAGE, $html);
    }

    public function testLoadWithASpecificPage(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['page' => 2],
        );

        $html = $component->render()->toString();

        $this->assertStringContainsString(self::SECOND_PAGE_BOOK_TITLE, $html);
        $this->assertStringNotContainsString(self::FIRST_PAGE_BOOK_TITLE, $html);
    }

    public function testChangePage(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
        );

        $component->call('changePage', ['page' => 2]);
        $this->assertStringContainsString(self::SECOND_PAGE_BOOK_TITLE, $component->render()->toString());

        $component->call('changePage', ['page' => 3]);
        $this->assertStringContainsString(self::THIRD_PAGE_BOOK_TITLE, $component->render()->toString());
    }

    public function testCallPreviousPage(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['page' => '3'],
        );

        $component->call('previousPage');
        $this->assertStringContainsString(self::SECOND_PAGE_BOOK_TITLE, $component->render()->toString());

        $component->call('previousPage');
        $this->assertStringContainsString(self::FIRST_PAGE_BOOK_TITLE, $component->render()->toString());
    }

    public function testCallNextPage(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
        );

        $component->call('nextPage');
        $this->assertStringContainsString(self::SECOND_PAGE_BOOK_TITLE, $component->render()->toString());
        $component->call('nextPage');
        $this->assertStringContainsString(self::THIRD_PAGE_BOOK_TITLE, $component->render()->toString());
    }

    public function testResetFilters(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['criteria' => ['title' => ['value' => 'Jurassic']]],
        );

        $component->call('resetFilters');

        $this->assertStringContainsString(self::FIRST_PAGE_BOOK_TITLE, $component->render()->toString());
        $this->assertComponentEmitEvent($component, 'filtersReset');
    }
}
