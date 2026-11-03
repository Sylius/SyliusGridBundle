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
use App\Twig\BookGridWithFiltersFormComponent;
use App\Twig\FilterComponent;
use PHPUnit\Framework\Attributes\CoversClass;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Twig\Component\ComponentWithGridFiltersFormTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(ComponentWithGridFiltersFormTrait::class)]
final class ComponentWithGridFiltersFormTraitTest extends KernelTestCase
{
    use InteractsWithLiveComponents;
    use Factories;
    use ResetDatabase;

    protected function setUp(): void
    {
        AppStory::load();
    }

    public function testCriteriaAreInitializedWithTheEmptyValuesOfTheFiltersForm(): void
    {
        /** @var BookGridWithFiltersFormComponent $bookGrid */
        $bookGrid = $this->createLiveComponent(name: BookGridWithFiltersFormComponent::class)->component();

        // The type of the string filters is a select without placeholder: it starts with its first choice, as displayed
        $this->assertSame(['type' => 'contains', 'value' => ''], $bookGrid->criteria['title']);
        $this->assertSame(['type' => 'contains', 'value' => ''], $bookGrid->criteria['currencyCode']);
        $this->assertSame([], $bookGrid->criteria['author']);
        $this->assertSame([], $bookGrid->criteria['state']);
        $this->assertFalse($bookGrid->hasActiveCriteria());
    }

    public function testCriteriaPassedWhenMountingAreKept(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridWithFiltersFormComponent::class,
            data: ['criteria' => ['title' => ['value' => 'Jurassic']]],
        );

        /** @var BookGridWithFiltersFormComponent $bookGrid */
        $bookGrid = $component->component();

        $this->assertSame(['type' => 'contains', 'value' => 'Jurassic'], $bookGrid->criteria['title']);
        $this->assertTrue($bookGrid->hasActiveCriteria());
        $this->assertStringContainsString('JURASSIC PARK', $component->render()->toString());
    }

    public function testCriteriaAreActiveWhenTheyDifferFromTheInitialValues(): void
    {
        /** @var BookGridWithFiltersFormComponent $bookGrid */
        $bookGrid = $this->createLiveComponent(name: BookGridWithFiltersFormComponent::class)->component();

        $bookGrid->criteria['title'] = ['type' => 'empty', 'value' => ''];

        $this->assertTrue($bookGrid->hasActiveCriteria());

        $bookGrid->criteria['title'] = ['type' => 'contains', 'value' => ''];
        $bookGrid->criteria['state'] = ['published'];

        $this->assertTrue($bookGrid->hasActiveCriteria());
    }

    public function testApplyFiltersGoesBackToTheFirstPage(): void
    {
        $component = $this->createLiveComponent(
            name: BookGridWithFiltersFormComponent::class,
            data: ['page' => 2],
        );

        $component->set('criteria.title.value', 'Jurassic');
        $component->call('applyFilters');

        /** @var BookGridWithFiltersFormComponent $bookGrid */
        $bookGrid = $component->component();

        $this->assertSame(1, $bookGrid->page);
        $this->assertStringContainsString('JURASSIC PARK', $component->render()->toString());
    }

    public function testFilterComponentOnAGridWithAFiltersForm(): void
    {
        $this->emulateSession();

        $michaelCrichtonId = $this->getMichaelCrichtonId();

        $grid = $this->createLiveComponent(
            name: BookGridWithFiltersFormComponent::class,
            data: ['page' => 2],
        );
        $authorFilterComponent = $this->createLiveComponent(
            name: FilterComponent::class,
            data: ['filter' => $this->createAuthorFilter()],
        );

        $authorFilterComponent->set('value', $michaelCrichtonId);
        $authorFilterComponent->call('onChange');
        $this->assertComponentEmitEvent($authorFilterComponent, 'filterChanged')
            ->withData(['newCriteria' => ['author' => $michaelCrichtonId]])
        ;

        $grid->call('filterChanged', ['newCriteria' => ['author' => $michaelCrichtonId]]);

        /** @var BookGridWithFiltersFormComponent $bookGrid */
        $bookGrid = $grid->component();

        // The criteria of the filters form are kept
        $this->assertSame($michaelCrichtonId, $bookGrid->criteria['author']);
        $this->assertSame(['type' => 'contains', 'value' => ''], $bookGrid->criteria['title']);
        $this->assertSame(1, $bookGrid->page);
        $this->assertTrue($bookGrid->hasActiveCriteria());

        $html = $grid->render()->toString();
        $this->assertStringContainsString('JURASSIC PARK', $html);
        $this->assertStringContainsString('THE LOST WORLD', $html);
        $this->assertStringNotContainsString('A STUDY IN SCARLET', $html);
    }

    public function testFilterComponentIsInitializedWithTheCriteriaOfAGridWithAFiltersForm(): void
    {
        $this->emulateSession();

        $michaelCrichtonId = $this->getMichaelCrichtonId();

        /** @var BookGridWithFiltersFormComponent $bookGrid */
        $bookGrid = $this->createLiveComponent(
            name: BookGridWithFiltersFormComponent::class,
            data: ['criteria' => ['author' => $michaelCrichtonId]],
        )->component();

        $authorFilterComponent = $this->createLiveComponent(
            name: FilterComponent::class,
            data: ['filter' => $this->createAuthorFilter(), 'criteria' => $bookGrid->criteria],
        );

        /** @var FilterComponent $authorFilter */
        $authorFilter = $authorFilterComponent->component();

        $this->assertSame($michaelCrichtonId, $authorFilter->value);
    }

    public function testResetFiltersWithAFilterComponentOnAGridWithAFiltersForm(): void
    {
        $this->emulateSession();

        $michaelCrichtonId = $this->getMichaelCrichtonId();

        $grid = $this->createLiveComponent(
            name: BookGridWithFiltersFormComponent::class,
            data: ['criteria' => ['author' => $michaelCrichtonId]],
        );
        $authorFilterComponent = $this->createLiveComponent(
            name: FilterComponent::class,
            data: ['filter' => $this->createAuthorFilter(), 'criteria' => ['author' => $michaelCrichtonId]],
        );

        $grid->call('resetFilters');
        $this->assertComponentEmitEvent($grid, 'filtersReset');

        /** @var BookGridWithFiltersFormComponent $bookGrid */
        $bookGrid = $grid->component();

        // The criteria are back to the empty values of the filters form
        $this->assertSame([], $bookGrid->criteria['author']);
        $this->assertFalse($bookGrid->hasActiveCriteria());

        $authorFilterComponent->emit('filtersReset');

        /** @var FilterComponent $authorFilter */
        $authorFilter = $authorFilterComponent->component();

        $this->assertNull($authorFilter->value);
    }

    private function createAuthorFilter(): Filter
    {
        $authorFilter = Filter::fromNameAndType('author', 'entity');
        $authorFilter->setFormOptions(['class' => Author::class]);

        return $authorFilter;
    }

    private function getMichaelCrichtonId(): string
    {
        /** @var Author $michaelCrichton */
        $michaelCrichton = AuthorFactory::repository()->findOneBy(['name' => 'Michael Crichton']);

        return (string) $michaelCrichton->getId();
    }

    private function emulateSession(): void
    {
        $session = new Session(new MockArraySessionStorage());
        $session->start();

        $request = Request::create('/');
        $request->setSession($session);

        self::getContainer()->get('request_stack')->push($request);
    }
}
