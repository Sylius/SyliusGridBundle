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

use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Symfony\Form\Registry\FormTypeRegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

/**
 * @experimental
 */
trait ComponentWithFilterTrait
{
    use ComponentToolsTrait;
    use ComponentWithFormTrait;

    /**
     * Not writable: the filter definition (type, form options...) must never be altered from the browser.
     */
    #[LiveProp(hydrateWith: 'hydrateFilter', dehydrateWith: 'dehydrateFilter')]
    public Filter $filter;

    /**
     * Criteria of the grid when the filter is mounted, used to initialize the filter value.
     *
     * @var array<string, mixed>|null
     */
    #[LiveProp]
    public ?array $criteria = null;

    private FormFactoryInterface $formFactory;

    private FormTypeRegistryInterface $filterFormTypeRegistry;

    /**
     * @return array<string, mixed>
     */
    public function dehydrateFilter(Filter $filter): array
    {
        return $filter->toArray();
    }

    /**
     * @param array<string, mixed> $filterData
     */
    public function hydrateFilter(array $filterData): Filter
    {
        /** @phpstan-ignore argument.type */
        return Filter::fromArray($filterData);
    }

    /**
     * @internal
     */
    #[Required]
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @internal
     */
    #[Required]
    public function setFilterFormTypeRegistry(FormTypeRegistryInterface $filterFormTypeRegistry): void
    {
        $this->filterFormTypeRegistry = $filterFormTypeRegistry;
    }

    #[LiveAction]
    public function onChange(): void
    {
        // Only notify the parent grid component, so that several grids can live on the same page.
        $this->emitUp('filterChanged', [
            'newCriteria' => $this->toArray(),
        ]);
    }

    #[LiveListener('filtersReset')]
    public function onFiltersReset(): void
    {
        $this->criteria = null;
        $this->reset();
    }

    /**
     * Keeps the filter value in sync when the criteria are changed by the grid (e.g. when removing an active filter).
     *
     * @param array<string, mixed>|null $criteria
     */
    #[LiveListener('criteriaChanged')]
    public function onCriteriaChanged(#[LiveArg] ?array $criteria = null): void
    {
        $this->criteria = $criteria;
        $this->initializeFilterValue();
    }

    protected function instantiateForm(): FormInterface
    {
        $formType = $this->filterFormTypeRegistry->get($this->filter->getType(), 'default');

        if (null === $formType) {
            throw new \InvalidArgumentException(sprintf('No form type registered for filter type "%s".', $this->filter->getType()));
        }

        // Same options as the ones used by the TwigGridRenderer to render filters.
        $options = [
            'allow_extra_fields' => true,
            'csrf_protection' => false,
            'required' => false,
        ];

        // No initial data: it would lock the form data and make the setData() call below a no-op.
        $form = $this->formFactory->createNamed('criteria', FormType::class, null, $options);
        $form->add($this->filter->getName(), $formType, $this->filter->getFormOptions());

        // The filter value is stored as view data (e.g. an entity identifier), but the form expects model data
        // (e.g. the entity itself). Submitting the form runs its view transformers to convert one into the other.
        // A clone is submitted because a form can only be submitted once and ComponentWithFormTrait may submit
        // the returned form later (e.g. on re-render). The original form is then initialized with the model data,
        // which is what selects the right choice when rendering an entity filter.
        // See ComponentWithEntityFilterTraitTest::testHydrateFormDataWithTheEntityFromCriteria().
        $clone = clone $form;
        $clone->submit($this->toArray());

        $form->setData($clone->getData());

        return $form->get($this->filter->getName());
    }

    /**
     * Returns the grid criteria of this filter, or null when there are none.
     */
    protected function getFilterCriteria(): mixed
    {
        return $this->criteria[$this->filter->getName()] ?? null;
    }

    /**
     * Initializes the filter value from the grid criteria.
     */
    abstract public function initializeFilterValue(): void;

    abstract protected function reset(): void;

    /**
     * @return array<string, mixed>
     */
    abstract protected function toArray(): array;
}
