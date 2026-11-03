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

use App\Entity\Author;
use App\Factory\AuthorFactory;
use App\Story\AppStory;
use App\Twig\BookGridComponent;
use App\Twig\FilterComponent;
use PHPUnit\Framework\Attributes\CoversClass;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Twig\Component\ComponentWithGridTrait;
use Sylius\Component\Grid\View\VariablesAwareGridViewInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Symfony\UX\LiveComponent\Test\TestLiveComponent;
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

    public function testChangeCriteriaNotifiesFilters(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['criteria' => ['title' => ['value' => 'Jurassic']]],
        );

        $component->call('changeCriteria', ['criteria' => ['title' => ['value' => 'Lord']]]);

        $this->assertComponentEmitEvent($component, 'criteriaChanged')
            ->withData(['criteria' => ['title' => ['value' => 'Lord']]])
        ;
    }

    public function testLoadWithSorting(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['sorting' => ['title' => 'desc']],
        );

        $this->assertStringContainsString(self::LAST_PAGE_BOOK_TITLE, $component->render()->toString());
        $this->assertStringNotContainsString(self::FIRST_PAGE_BOOK_TITLE, $component->render()->toString());
    }

    public function testChangeSorting(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
        );

        $component->call('changeSorting', ['sorting' => ['title' => 'desc']]);

        $this->assertStringContainsString(self::LAST_PAGE_BOOK_TITLE, $component->render()->toString());
        $this->assertStringNotContainsString(self::FIRST_PAGE_BOOK_TITLE, $component->render()->toString());
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

    public function testLoadWithAPageLimitGreaterThanTheGridLimits(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['limit' => 1000],
        );

        $html = $component->render()->toString();

        // The limit is clamped to the greatest grid limit (15)
        $this->assertStringContainsString(self::FIRST_PAGE_BOOK_TITLE, $html);
        $this->assertStringContainsString(self::SECOND_PAGE_BOOK_TITLE, $html);
        $this->assertStringNotContainsString(self::THIRD_PAGE_BOOK_TITLE, $html);
        $this->assertStringNotContainsString(self::LAST_PAGE_BOOK_TITLE, $html);
    }

    public function testLoadWithAnInvalidPageLimit(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['limit' => -1],
        );

        $html = $component->render()->toString();

        // The first grid limit (10) is used
        $this->assertStringContainsString(self::FIRST_PAGE_BOOK_TITLE, $html);
        $this->assertStringNotContainsString(self::SECOND_PAGE_BOOK_TITLE, $html);
    }

    public function testLoadWithInvalidSorting(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['sorting' => ['unknown' => 'desc', 'title' => 'sideways']],
        );

        $html = $component->render()->toString();

        // The default sorting is applied
        $this->assertStringContainsString(self::FIRST_PAGE_BOOK_TITLE, $html);
        $this->assertStringNotContainsString(self::LAST_PAGE_BOOK_TITLE, $html);
    }

    public function testChangeSortingWithAnInvalidField(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
        );

        $component->call('changeSorting', ['sorting' => ['unknown' => 'desc', 'title' => 'DESC']]);

        $html = $component->render()->toString();

        $this->assertStringContainsString(self::LAST_PAGE_BOOK_TITLE, $html);
        $this->assertStringNotContainsString(self::FIRST_PAGE_BOOK_TITLE, $html);
    }

    public function testLoadWithAnInvalidPage(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['page' => -2],
        );

        $html = $component->render()->toString();

        $this->assertStringContainsString(self::FIRST_PAGE_BOOK_TITLE, $html);
        $this->assertStringNotContainsString(self::SECOND_PAGE_BOOK_TITLE, $html);
    }

    public function testCallNextPageWhenCurrentPageIsInvalid(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['page' => -2],
        );

        $component->call('nextPage');
        $this->assertCurrentPage($component, 2);
        $this->assertStringContainsString(self::SECOND_PAGE_BOOK_TITLE, $component->render()->toString());
    }

    public function testLoadWithASpecificPage(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['page' => 2],
        );

        $html = $component->render()->toString();

        $this->assertCurrentPage($component, 2);

        $this->assertStringContainsString(self::SECOND_PAGE_BOOK_TITLE, $html);
        $this->assertStringNotContainsString(self::FIRST_PAGE_BOOK_TITLE, $html);
    }

    public function testChangePage(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
        );

        $component->call('changePage', ['page' => 2]);
        $this->assertCurrentPage($component, 2);
        $this->assertStringContainsString(self::SECOND_PAGE_BOOK_TITLE, $component->render()->toString());

        $component->call('changePage', ['page' => 3]);
        $this->assertCurrentPage($component, 3);
        $this->assertStringContainsString(self::THIRD_PAGE_BOOK_TITLE, $component->render()->toString());
    }

    public function testCallPreviousPage(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['page' => '3'],
        );

        $component->call('previousPage');
        $this->assertCurrentPage($component, 2);
        $this->assertStringContainsString(self::SECOND_PAGE_BOOK_TITLE, $component->render()->toString());

        $component->call('previousPage');
        $this->assertCurrentPage($component, 1);
        $this->assertStringContainsString(self::FIRST_PAGE_BOOK_TITLE, $component->render()->toString());
    }

    public function testCallPreviousPageWhenCurrentPageIsNull(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['page' => null],
        );

        $component->call('previousPage');
        $this->assertCurrentPage($component, 1);
        $this->assertStringContainsString(self::FIRST_PAGE_BOOK_TITLE, $component->render()->toString());
    }

    public function testCallNextPage(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
        );

        $component->call('nextPage');
        $this->assertCurrentPage($component, 2);
        $this->assertStringContainsString(self::SECOND_PAGE_BOOK_TITLE, $component->render()->toString());

        $component->call('nextPage');
        $this->assertCurrentPage($component, 3);
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
        $this->assertCurrentPage($component, 1);

        $this->assertComponentEmitEvent($component, 'filtersReset');
    }

    public function testFilterChangedResetsPageToFirstPage(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['page' => 3],
        );

        $component->call('filterChanged', ['newCriteria' => ['title' => ['value' => 'Jurassic']]]);

        $this->assertCurrentPage($component, 1);
    }

    public function testFilterChangedDoesNotNotifyFilters(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
        );

        $component->call('filterChanged', ['newCriteria' => ['title' => ['value' => 'Jurassic']]]);

        $this->assertComponentNotEmitEvent($component, 'criteriaChanged');
    }

    public function testSeveralFilterComponentsOnTheSameGrid(): void
    {
        $this->emulateSession();

        /** @var Author $michaelCrichton */
        $michaelCrichton = AuthorFactory::repository()->findOneBy(['name' => 'Michael Crichton']);
        $michaelCrichtonId = (string) $michaelCrichton->getId();

        $authorFilter = Filter::fromNameAndType('author', 'entity');
        $authorFilter->setFormOptions(['class' => Author::class]);

        $stateFilter = Filter::fromNameAndType('state', 'select');
        $stateFilter->setFormOptions(['choices' => ['published' => 'published', 'unpublished' => 'unpublished']]);

        $grid = $this->createLiveComponent(name: BookGridComponent::class);
        $authorFilterComponent = $this->createLiveComponent(
            name: FilterComponent::class,
            data: ['filter' => $authorFilter],
        );
        $stateFilterComponent = $this->createLiveComponent(
            name: FilterComponent::class,
            data: ['filter' => $stateFilter],
        );

        // The author filter is changed: the grid applies it without echoing it back to the filters
        $authorFilterComponent->set('value', $michaelCrichtonId);
        $authorFilterComponent->call('onChange');
        $this->assertComponentEmitEvent($authorFilterComponent, 'filterChanged')
            ->withData(['newCriteria' => ['author' => $michaelCrichtonId]])
        ;

        $grid->call('filterChanged', ['newCriteria' => ['author' => $michaelCrichtonId]]);
        $this->assertComponentNotEmitEvent($grid, 'criteriaChanged');

        // The state filter is changed: the criteria of the author filter are kept
        $stateFilterComponent->set('value', 'published');
        $stateFilterComponent->call('onChange');
        $this->assertComponentEmitEvent($stateFilterComponent, 'filterChanged')
            ->withData(['newCriteria' => ['state' => 'published']])
        ;

        $grid->call('filterChanged', ['newCriteria' => ['state' => 'published']]);
        $this->assertComponentNotEmitEvent($grid, 'criteriaChanged');

        $html = $grid->render()->toString();
        $this->assertStringContainsString('JURASSIC PARK', $html);
        $this->assertStringNotContainsString('THE LOST WORLD', $html);
        $this->assertStringNotContainsString(self::FIRST_PAGE_BOOK_TITLE, $html);

        // The state filter is removed from the grid: every filter is notified and only the state filter is cleared
        $criteria = ['author' => $michaelCrichtonId];
        $grid->call('changeCriteria', ['criteria' => $criteria]);
        $this->assertComponentEmitEvent($grid, 'criteriaChanged')->withData(['criteria' => $criteria]);

        $stateFilterComponent->emit('criteriaChanged', ['criteria' => $criteria]);
        $stateFilterComponent->call('onChange');
        $this->assertComponentEmitEvent($stateFilterComponent, 'filterChanged')
            ->withData(['newCriteria' => ['state' => null]])
        ;

        $authorFilterComponent->emit('criteriaChanged', ['criteria' => $criteria]);
        $authorFilterComponent->call('onChange');
        $this->assertComponentEmitEvent($authorFilterComponent, 'filterChanged')
            ->withData(['newCriteria' => ['author' => $michaelCrichtonId]])
        ;

        $html = $grid->render()->toString();
        $this->assertStringContainsString('JURASSIC PARK', $html);
        $this->assertStringContainsString('THE LOST WORLD', $html);
    }

    private function emulateSession(): void
    {
        $session = new Session(new MockArraySessionStorage());
        $session->start();

        $request = Request::create('/');
        $request->setSession($session);

        self::getContainer()->get('request_stack')->push($request);
    }

    private function assertCurrentPage(TestLiveComponent $component, int $expectedPage): void
    {
        $this->assertStringContainsString('Current page: ' . $expectedPage, $component->render()->toString());
    }

    private function assertCurrentLimit(TestLiveComponent $component, int $expectedLimit): void
    {
        $this->assertStringContainsString('Current limit: ' . $expectedLimit, $component->render()->toString());
    }

    public function testGridViewHasTheGridVariables(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridComponent::class,
            data: ['gridVariables' => ['authorId' => 42]],
        );

        $component->render();

        /** @var BookGridComponent $bookGrid */
        $bookGrid = $component->component();
        $gridView = $bookGrid->getGridView();

        $this->assertInstanceOf(VariablesAwareGridViewInterface::class, $gridView);
        $this->assertSame(['authorId' => 42], $gridView->getVariables());
    }

    public function testGridViewHasNoVariablesByDefault(): void
    {
        /** @var BookGridComponent $bookGrid */
        $bookGrid = $this->createLiveComponent(name: BookGridComponent::class)->component();
        $gridView = $bookGrid->getGridView();

        $this->assertInstanceOf(VariablesAwareGridViewInterface::class, $gridView);
        $this->assertSame([], $gridView->getVariables());
    }

    public function testGridVariablesDefaultToTheRouteParameters(): void
    {
        $bookGrid = $this->createBookGridOnRoute(['authorId' => '7', '_locale' => 'en']);

        $bookGrid->captureGridVariables();

        $this->assertSame(['authorId' => '7'], $bookGrid->gridVariables);
        $this->assertSame(['authorId' => '7'], $bookGrid->getGridView()->getVariables());
    }

    public function testGridVariablesPassedWhenMountingAreNotReplacedByTheRouteParameters(): void
    {
        $bookGrid = $this->createBookGridOnRoute(['authorId' => '7']);
        $bookGrid->gridVariables = ['authorId' => 42];

        $bookGrid->captureGridVariables();

        $this->assertSame(['authorId' => 42], $bookGrid->gridVariables);
    }

    public function testGridViewIsBuiltOnlyOnceForTheSameParameters(): void
    {
        /** @var BookGridComponent $bookGrid */
        $bookGrid = $this->createLiveComponent(name: BookGridComponent::class)->component();
        $gridView = $bookGrid->getGridView();

        $this->assertSame($gridView, $bookGrid->getGridView());

        $bookGrid->nextPage();

        $this->assertNotSame($gridView, $bookGrid->getGridView());
    }

    public function testHasActiveCriteria(): void
    {
        /** @var BookGridComponent $bookGrid */
        $bookGrid = $this->createLiveComponent(name: BookGridComponent::class)->component();

        $this->assertFalse($bookGrid->hasActiveCriteria());

        $bookGrid->criteria = ['title' => ['value' => ''], 'author' => null];

        $this->assertFalse($bookGrid->hasActiveCriteria());

        $bookGrid->criteria = ['title' => ['value' => 'Jurassic'], 'author' => null];

        $this->assertTrue($bookGrid->hasActiveCriteria());
    }

    /**
     * @param array<string, mixed> $routeParameters
     */
    private function createBookGridOnRoute(array $routeParameters): BookGridComponent
    {
        $requestStack = new RequestStack();
        $requestStack->push(new Request(attributes: ['_route_params' => $routeParameters]));

        /** @var BookGridComponent $bookGrid */
        $bookGrid = $this->createLiveComponent(name: BookGridComponent::class)->component();
        $bookGrid->setRequestStack($requestStack);

        return $bookGrid;
    }
}
