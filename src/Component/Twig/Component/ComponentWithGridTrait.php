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

namespace Sylius\Component\Grid\Twig\Component;

use Pagerfanta\PagerfantaInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Grid\View\GridViewFactoryInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Sylius\Component\Grid\View\VariablesAwareGridViewFactoryInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * @experimental
 */
trait ComponentWithGridTrait
{
    use ComponentToolsTrait;

    #[ExposeInTemplate(getter: 'getGridView')]
    public GridView $grid;

    #[ExposeInTemplate(getter: 'getFilters')]
    public array $filters;

    #[LiveProp(writable: true, url: true)]
    public ?int $page = null;

    /** @var array<string, mixed>|null */
    #[LiveProp(writable: true, url: true)]
    public ?array $criteria = null;

    /** @var array<string, string>|null */
    #[LiveProp(writable: true, url: true)]
    public ?array $sorting = null;

    #[LiveProp(writable: true, url: true)]
    public ?int $limit = null;

    /**
     * The variables of the page (e.g. its route parameters), to pass when mounting the component, as the request
     * they come from is not available anymore during the live re-renders. They resolve the "$name" values and are
     * available as "variables" in the "@=" expressions of the grid configuration, as for the regular grids
     * (see VariablesAwareGridViewInterface).
     *
     * @var array<string, mixed>
     */
    #[LiveProp]
    public array $gridVariables = [];

    private GridProviderInterface $gridProvider;

    private GridViewFactoryInterface $gridViewFactory;

    public function getFilters(): array
    {
        return $this->getGridDefinition()->getEnabledFilters();
    }

    public function getGridView(): GridViewInterface
    {
        $gridDefinition = $this->getGridDefinition();

        $config = $this->getGridConfig();

        $gridView = [] !== $this->gridVariables && $this->gridViewFactory instanceof VariablesAwareGridViewFactoryInterface
            ? $this->gridViewFactory->createWithVariables($gridDefinition, new Parameters($config), $this->gridVariables)
            : $this->gridViewFactory->create($gridDefinition, new Parameters($config))
        ;

        $data = $gridView->getData();

        if ($data instanceof PagerfantaInterface) {
            if (null !== $this->limit) {
                $data->setMaxPerPage($this->limit);
            }

            $data->setCurrentPage($this->page ?? 1);
        }

        return $gridView;
    }

    /**
     * @return array<string, mixed>
     */
    public function getGridConfig(): array
    {
        $config = [
            'page' => $this->page,
        ];

        if (null !== $this->criteria) {
            $config['criteria'] = $this->criteria;
        }

        if (null !== $this->sorting) {
            $config['sorting'] = $this->sorting;
        }

        if (null !== $this->limit) {
            $config['limit'] = $this->limit;
        }

        return $config;
    }

    #[LiveAction]
    public function resetFilters(): void
    {
        $this->page = null;
        $this->criteria = null;

        $this->emit('filtersReset');
    }

    #[LiveAction]
    public function changePage(#[LiveArg] ?int $page): void
    {
        $this->page = $page;
    }

    #[LiveAction]
    public function previousPage(): void
    {
        // Prevent setting zero as page
        if (null === $this->page || 1 === $this->page) {
            return;
        }

        --$this->page;
    }

    #[LiveAction]
    public function nextPage(): void
    {
        if (null === $this->page) {
            $this->page = 1;
        }

        ++$this->page;
    }

    /**
     * Changes the criteria from the grid itself (e.g. when removing an active filter),
     * the filter components are notified so they can update their value.
     */
    #[LiveAction]
    public function changeCriteria(#[LiveArg] ?array $criteria): void
    {
        $this->applyCriteria($criteria);

        $this->emit('criteriaChanged', ['criteria' => $this->criteria]);
    }

    /**
     * Changes the criteria from a filter component. The filter components are not notified,
     * as the filter that emitted the change is already up-to-date (and may still be edited).
     */
    #[LiveListener('filterChanged')]
    public function filterChanged(#[LiveArg] array $newCriteria): void
    {
        $this->applyCriteria(array_merge($this->criteria ?? [], $newCriteria));
    }

    #[LiveAction]
    public function changeSorting(#[LiveArg] ?array $sorting): void
    {
        $this->sorting = $sorting;
    }

    #[LiveAction]
    public function changeLimit(#[LiveArg] ?int $limit): void
    {
        $this->page = 1; // Cause the number of pages will change
        $this->limit = $limit;
    }

    /**
     * @internal
     */
    #[Required]
    public function setGridViewFactory(GridViewFactoryInterface $gridViewFactory): void
    {
        $this->gridViewFactory = $gridViewFactory;
    }

    /**
     * @internal
     */
    #[Required]
    public function setGridProvider(GridProviderInterface $gridProvider): void
    {
        $this->gridProvider = $gridProvider;
    }

    abstract protected function getGridName(): string;

    /**
     * @param array<string, mixed>|null $criteria
     */
    private function applyCriteria(?array $criteria): void
    {
        $this->page = 1; // Cause the number of results will change
        $this->criteria = $criteria;
    }

    private function getGridDefinition(): Grid
    {
        return $this->gridProvider->get($this->getGridName());
    }
}
