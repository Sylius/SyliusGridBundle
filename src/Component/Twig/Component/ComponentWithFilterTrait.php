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
use Sylius\Component\Grid\Symfony\Form\Type\FormTypeRegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
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

    #[LiveProp(writable: true, hydrateWith: 'hydrateFilter', dehydrateWith: 'dehydrateFilter')]
    public Filter $filter;

    #[LiveProp(writable: true)]
    public ?array $criteria = null;

    private FormFactoryInterface $formFactory;

    private FormTypeRegistryInterface $filterFormTypeRegistry;

    public function dehydrateFilter(Filter $filter): array
    {
        return $filter->toArray();
    }

    public function hydrateFilter(array|null $filterData = null): Filter
    {
        return Filter::fromArray($filterData);
    }

    /**
     * @internal
     */
    #[Required]
    public function setFormFactory(FormFactoryInterface $factory): void
    {
        $this->formFactory = $factory;
    }

    /**
     * @internal
     */
    #[Required]
    public function setFilterFormTypeFactory(FormTypeRegistryInterface $filterFormTypeRegistry): void
    {
        $this->filterFormTypeRegistry = $filterFormTypeRegistry;
    }

    #[LiveAction]
    public function onChange(): void
    {
        $this->emit('filterChanged', [
            'newCriteria' => $this->toArray(),
        ]);
    }

    #[LiveListener('filtersReset')]
    public function onFilterReset(): void
    {
        $this->reset();
    }

    protected function instantiateForm(): FormInterface
    {
        $form = $this->formFactory->createNamed('criteria');

        $form->add(
            $this->filter->getName(),
            $this->filterFormTypeRegistry->get($this->filter->getType(), 'default'),
            $this->filter->getFormOptions(),
        );

        // Clone the form to avoid submitting it twice (submit is not idempotent).
        $clone = clone $form;
        $clone->submit($this->toArray());

        $form->setData($clone->getData());

        return $form->get($this->filter->getName());
    }

    abstract protected function reset(): void;

    /**
     * @return array<string, mixed>
     */
    abstract protected function toArray(): array;
}
