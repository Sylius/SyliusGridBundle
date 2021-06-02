<?php

namespace spec\Sylius\Component\Grid\Config\Builder;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Config\Builder\Filter;

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

    function it_sets_label(): void
    {
        $field = $this->setLabel('Search');

        $field->toArray()['label']->shouldReturn('Search');
    }

    function it_enables_filters(): void
    {
        $field = $this->setEnabled(true);

        $field->toArray()['enabled']->shouldReturn(true);
    }

    function it_disables_filters(): void
    {
        $field = $this->setEnabled(false);

        $field->toArray()['enabled']->shouldReturn(false);
    }

    function it_sets_template(): void
    {
        $field = $this->setTemplate('/path/to/template');

        $field->toArray()['template']->shouldReturn('/path/to/template');
    }

    function it_sets_options(): void
    {
        $field = $this->setOptions(['fields' => ['name', 'code']]);

        $field->toArray()['options']->shouldReturn(['fields' => ['name', 'code']]);
    }

    function it_sets_form_options(): void
    {
        $field = $this->setFormOptions(['multiple' => true]);

        $field->toArray()['form_options']->shouldReturn(['multiple' => true]);
    }

    function it_sets_criteria(): void
    {
        $field = $this->setCriteria(['name' => 'test']);

        $field->toArray()['criteria']->shouldReturn(['name' => 'test']);
    }
}
