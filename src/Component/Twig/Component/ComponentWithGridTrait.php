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
use Sylius\Component\Grid\Pagination\MaxPerPageResolver;
use Sylius\Component\Grid\Pagination\MaxPerPageResolverInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Component\Grid\Sanitizing\ParametersSanitizer;
use Sylius\Component\Grid\Sanitizing\ParametersSanitizerInterface;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Grid\View\GridViewFactoryInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Sylius\Component\Grid\View\VariablesAwareGridViewFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;

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
     * The variables of the page, as the request they come from is not available anymore during the live re-renders.
     * They default to the route parameters of the page the component is mounted on (see captureGridVariables()),
     * and can be passed when mounting the component instead. They resolve the "$name" values and are available as
     * "variables" in the "@=" expressions of the grid configuration, as for the regular grids
     * (see VariablesAwareGridViewInterface).
     *
     * @var array<string, mixed>
     */
    #[LiveProp]
    public array $gridVariables = [];

    private GridProviderInterface $gridProvider;

    private GridViewFactoryInterface $gridViewFactory;

    private ?ParametersSanitizerInterface $parametersSanitizer = null;

    private ?MaxPerPageResolverInterface $maxPerPageResolver = null;

    private ?RequestStack $requestStack = null;

    private ?GridViewInterface $cachedGridView = null;

    private ?string $cachedGridViewKey = null;

    public function getFilters(): array
    {
        return $this->getGridDefinition()->getEnabledFilters();
    }

    /**
     * Keeps the route parameters of the page as the grid variables, unless they were passed when mounting the component.
     */
    #[PostMount]
    public function captureGridVariables(): void
    {
        if ([] !== $this->gridVariables) {
            return;
        }

        $routeParameters = $this->requestStack?->getCurrentRequest()?->attributes->all('_route_params') ?? [];

        foreach ($routeParameters as $name => $value) {
            // The route parameters also contain the route defaults (e.g. "_controller"), which must not be exposed in the props
            if (is_string($name) && !str_starts_with($name, '_')) {
                $this->gridVariables[$name] = $value;
            }
        }
    }

    public function getGridView(): GridViewInterface
    {
        $gridDefinition = $this->getGridDefinition();

        // Invalid values coming from the URL (or from the browser) must not break the grid or load all the items at once
        $parameters = $this->getParametersSanitizer()->sanitize($gridDefinition, new Parameters($this->getGridConfig()));

        // The grid view is exposed several times per render (grid, filters, pagination...), it is built again only when its parameters change
        $cacheKey = serialize([$gridDefinition->getCode(), $parameters->all(), $this->gridVariables]);
        if (null !== $this->cachedGridView && $cacheKey === $this->cachedGridViewKey) {
            return $this->cachedGridView;
        }

        $gridView = $this->createGridView($gridDefinition, $parameters);

        $data = $gridView->getData();

        if ($data instanceof PagerfantaInterface) {
            /** @var int|null $limit */
            $limit = $parameters->get('limit');
            /** @var int $page */
            $page = $parameters->get('page', 1);

            $data->setMaxPerPage($this->getMaxPerPageResolver()->resolve($limit, $gridDefinition->getLimits()));
            $data->setCurrentPage($page);

            // Loads the results now, rather than from the template
            $data->getCurrentPageResults();
        }

        $this->cachedGridViewKey = $cacheKey;

        return $this->cachedGridView = $gridView;
    }

    /**
     * Whether a filter has a value. The empty values (e.g. an empty search) are not active criteria.
     */
    public function hasActiveCriteria(): bool
    {
        $hasValue = static function (mixed $value) use (&$hasValue): bool {
            if (is_array($value)) {
                return [] !== array_filter($value, $hasValue);
            }

            return null !== $value && '' !== $value;
        };

        return $hasValue($this->criteria ?? []);
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
        if (null === $this->page || $this->page <= 1) {
            return;
        }

        --$this->page;
    }

    #[LiveAction]
    public function nextPage(): void
    {
        $this->page = max(1, $this->page ?? 1) + 1;
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

    /**
     * @internal
     */
    #[Required]
    public function setParametersSanitizer(ParametersSanitizerInterface $parametersSanitizer): void
    {
        $this->parametersSanitizer = $parametersSanitizer;
    }

    /**
     * @internal
     */
    #[Required]
    public function setMaxPerPageResolver(MaxPerPageResolverInterface $maxPerPageResolver): void
    {
        $this->maxPerPageResolver = $maxPerPageResolver;
    }

    /**
     * @internal
     */
    #[Required]
    public function setRequestStack(RequestStack $requestStack): void
    {
        $this->requestStack = $requestStack;
    }

    abstract protected function getGridName(): string;

    /**
     * Builds the grid view from the sanitized parameters. Can be overridden to build it differently
     * (e.g. with a grid view factory which needs more context than the grid variables).
     */
    protected function createGridView(Grid $gridDefinition, Parameters $parameters): GridViewInterface
    {
        if ([] !== $this->gridVariables && $this->gridViewFactory instanceof VariablesAwareGridViewFactoryInterface) {
            return $this->gridViewFactory->createWithVariables($gridDefinition, $parameters, $this->gridVariables);
        }

        return $this->gridViewFactory->create($gridDefinition, $parameters);
    }

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

    private function getParametersSanitizer(): ParametersSanitizerInterface
    {
        return $this->parametersSanitizer ??= new ParametersSanitizer($this->getMaxPerPageResolver());
    }

    private function getMaxPerPageResolver(): MaxPerPageResolverInterface
    {
        return $this->maxPerPageResolver ??= new MaxPerPageResolver();
    }
}
