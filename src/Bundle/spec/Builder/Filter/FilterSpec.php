<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Builder\Filter;

use App\Entity\Author;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface;

final class FilterSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedThrough('create', ['search', 'string']);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(Filter::class);
    }

    function it_implements_an_interface(): void
    {
        $this->shouldImplement(FilterInterface::class);
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

    function it_adds_options(): void
    {
        $field = $this->addOption('fields', ['name', 'code']);

        $field->toArray()['options']->shouldReturn(['fields' => ['name', 'code']]);
    }

    function it_remove_option(): void
    {
        $this->addOption('fields', ['name', 'code']);

        $field = $this->removeOption('fields');

        $field->toArray()['options']->shouldReturn(null);
    }

    function it_sets_form_options(): void
    {
        $field = $this->setFormOptions(['multiple' => true]);

        $field->toArray()['form_options']->shouldReturn(['multiple' => true]);
    }

    function it_adds_form_options(): void
    {
        $field = $this
            ->addFormOption('class', Author::class)
            ->addFormOption('multiple', true);

        $field->toArray()['form_options']->shouldReturn([
            'class' => Author::class,
            'multiple' => true
        ]);
    }

    function it_removes_form_options(): void
    {
        $field = $this
            ->addFormOption('class', Author::class)
            ->addFormOption('multiple', true)
            ->removeFormOption('multiple');

        $field->toArray()['form_options']->shouldReturn([
            'class' => Author::class,
        ]);
    }

    function it_sets_criteria(): void
    {
        $field = $this->setCriteria(['name' => 'test']);

        $field->toArray()['criteria']->shouldReturn(['name' => 'test']);
    }
}
