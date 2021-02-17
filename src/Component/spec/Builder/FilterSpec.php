<?php

namespace spec\Sylius\Component\Grid\Builder;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Builder\Filter;
use Sylius\Component\Grid\Definition\Filter as FilterDefinition;

class FilterSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('search', 'string');
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(Filter::class);
    }

    function it_returns_its_definition(): void
    {
        $this->getDefinition()->shouldHaveType(FilterDefinition::class);
    }

    function it_sets_label(): void
    {
        $field = $this->setLabel('Search');

        $field->getDefinition()->getLabel()->shouldReturn('Search');
    }

    function it_enables_filters(): void
    {
        $field = $this->setEnabled(true);

        $field->getDefinition()->isEnabled()->shouldReturn(true);
    }

    function it_disables_filters(): void
    {
        $field = $this->setEnabled(false);

        $field->getDefinition()->isEnabled()->shouldReturn(false);
    }

    function it_sets_template(): void
    {
        $field = $this->setTemplate('/path/to/template');

        $field->getDefinition()->getTemplate()->shouldReturn('/path/to/template');
    }

    function it_sets_options(): void
    {
        $field = $this->setOptions(['fields' => ['name', 'code']]);

        $field->getDefinition()->getOptions()->shouldReturn(['fields' => ['name', 'code']]);
    }

    function it_sets_form_options(): void
    {
        $field = $this->setFormOptions(['multiple' => true]);

        $field->getDefinition()->getFormOptions()->shouldReturn(['multiple' => true]);
    }

    function it_sets_criteria(): void
    {
        $field = $this->setCriteria(['name' => 'test']);

        $field->getDefinition()->getCriteria()->shouldReturn(['name' => 'test']);
    }
}
