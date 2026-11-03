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

use Sylius\Component\Grid\Symfony\Form\Registry\FormTypeRegistryInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
use Symfony\UX\TwigComponent\Attribute\PostMount;

/**
 * Live grid whose filters are rendered in a single form (e.g. with the filter templates of the TwigGridRenderer),
 * bound to the "criteria" prop through the names of its fields (e.g. "criteria[search][value]")
 * with data-model="norender|*", and applied with the "applyFilters" action.
 *
 * Use ComponentWithGridTrait alone when each filter is a live component (see ComponentWithFilterTrait).
 *
 * @experimental
 */
trait ComponentWithGridFiltersFormTrait
{
    use ComponentWithGridTrait;

    private FormFactoryInterface $filtersFormFactory;

    private FormTypeRegistryInterface $filterFormTypeRegistry;

    /**
     * The live component JS cannot write a nested value (e.g. "criteria.search.value") if its parents do not exist.
     * The criteria are therefore initialized with the empty values of the filters form, as ComponentWithFormTrait
     * does with its "formValues".
     */
    #[PostMount(priority: -10)]
    #[PreReRender]
    public function initializeCriteria(): void
    {
        $criteria = $this->getEmptyCriteria();

        foreach ($this->criteria ?? [] as $name => $value) {
            $criteria[$name] = is_array($value) && is_array($criteria[$name] ?? null) ? array_replace_recursive($criteria[$name], $value) : $value;
        }

        $this->criteria = $criteria;
    }

    /**
     * The criteria always contain the values of the filters form: a criterion is only active
     * when it is not empty and differs from its initial value (e.g. the "contains" type of a string filter).
     */
    public function hasActiveCriteria(): bool
    {
        $isActive = static function (mixed $value, mixed $initialValue) use (&$isActive): bool {
            if (is_array($value)) {
                foreach ($value as $key => $childValue) {
                    if ($isActive($childValue, is_array($initialValue) ? ($initialValue[$key] ?? null) : null)) {
                        return true;
                    }
                }

                return false;
            }

            return null !== $value && '' !== $value && $value !== $initialValue;
        };

        return $isActive($this->criteria ?? [], $this->getEmptyCriteria());
    }

    /**
     * Applies the criteria bound to the filters form.
     */
    #[LiveAction]
    public function applyFilters(): void
    {
        $this->page = 1; // Cause the number of results will change
    }

    /**
     * @internal
     */
    #[Required]
    public function setFiltersFormFactory(FormFactoryInterface $filtersFormFactory): void
    {
        $this->filtersFormFactory = $filtersFormFactory;
    }

    /**
     * @internal
     */
    #[Required]
    public function setFilterFormTypeRegistry(FormTypeRegistryInterface $filterFormTypeRegistry): void
    {
        $this->filterFormTypeRegistry = $filterFormTypeRegistry;
    }

    /**
     * @return array<string, mixed>
     */
    private function getEmptyCriteria(): array
    {
        // Same form as the one built by the TwigGridRenderer to render each filter
        $form = $this->filtersFormFactory->createNamed('criteria', FormType::class, null, [
            'allow_extra_fields' => true,
            'csrf_protection' => false,
            'required' => false,
        ]);

        foreach ($this->getGridDefinition()->getEnabledFilters() as $filter) {
            $form->add($filter->getName(), $this->filterFormTypeRegistry->get($filter->getType(), 'default'), $filter->getFormOptions());
        }

        return $this->extractFiltersFormValues($form->createView());
    }

    /**
     * Simplified version of ComponentWithFormTrait::extractFormValues().
     *
     * @return array<string, mixed>
     */
    private function extractFiltersFormValues(FormView $formView): array
    {
        $values = [];

        foreach ($formView->children as $name => $child) {
            $name = (string) $name;
            $isCompound = $child->vars['compound_data'] ?? $child->vars['compound'] ?? false;

            if ($isCompound && !($child->vars['expanded'] ?? false)) {
                $values[$name] = $this->extractFiltersFormValues($child);

                continue;
            }

            // A select without placeholder displays its first choice, but the live component JS only writes the value on change
            if ('' === $child->vars['value'] && null !== $firstChoice = $this->getFirstChoiceOfSelectWithoutPlaceholder($child)) {
                $values[$name] = $firstChoice;

                continue;
            }

            if (array_key_exists('checked', $child->vars)) {
                $values[$name] = $child->vars['checked'] ? $child->vars['value'] : null;

                continue;
            }

            $values[$name] = $child->vars['value'];
        }

        return $values;
    }

    /**
     * The value displayed by a select without placeholder (its first choice), null for the other fields.
     */
    private function getFirstChoiceOfSelectWithoutPlaceholder(FormView $formView): ?string
    {
        $choices = $formView->vars['choices'] ?? [];
        $placeholder = $formView->vars['placeholder'] ?? null;

        if (
            !is_array($choices) ||
            [] === $choices ||
            false !== ($formView->vars['expanded'] ?? true) ||
            false !== ($formView->vars['multiple'] ?? true) ||
            is_string($placeholder) ||
            $placeholder instanceof TranslatableInterface
        ) {
            return null;
        }

        $firstChoice = reset($choices);
        if ($firstChoice instanceof ChoiceGroupView) {
            $firstChoice = reset($firstChoice->choices);
        }

        return $firstChoice instanceof ChoiceView ? $firstChoice->value : null;
    }
}
