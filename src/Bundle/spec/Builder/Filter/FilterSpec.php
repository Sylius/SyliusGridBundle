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

    function it_has_no_label_by_default(): void
    {
        $this->getLabel()->shouldReturn(null);
    }

    function it_sets_label(): void
    {
        $this->setLabel('Search');

        $this->getLabel()->shouldReturn('Search');
    }

    function it_is_enabled_by_default(): void
    {
        $this->isEnabled()->shouldReturn(true);
    }

    function it_enables_filters(): void
    {
        $this->setEnabled(true);

        $this->isEnabled()->shouldReturn(true);
    }

    function it_disables_filters(): void
    {
        $this->setEnabled(false);

        $this->isEnabled()->shouldReturn(false);
    }

    function it_has_no_template_by_default(): void
    {
        $this->getTemplate()->shouldReturn(null);
    }

    function it_sets_template(): void
    {
        $this->setTemplate('/path/to/template');

        $this->getTemplate()->shouldReturn('/path/to/template');
    }

    function it_has_no_options_by_default(): void
    {
        $this->getOptions()->shouldReturn(null);
    }

    function it_sets_options(): void
    {
        $this->setOptions(['fields' => ['name', 'code']]);

        $this->getOptions()->shouldReturn(['fields' => ['name', 'code']]);
    }

    function it_adds_options(): void
    {
        $this->addOption('fields', ['name', 'code']);

        $this->getOptions()->shouldReturn(['fields' => ['name', 'code']]);
    }

    function it_remove_option(): void
    {
        $this->addOption('fields', ['name', 'code']);

        $this->removeOption('fields');

        $this->getOptions()->shouldReturn(null);
    }

    function it_sets_form_options(): void
    {
        $this->setFormOptions(['multiple' => true]);

        $this->getFormOptions()->shouldReturn(['multiple' => true]);
    }

    function it_adds_form_options(): void
    {
        $this
            ->addFormOption('class', Author::class)
            ->addFormOption('multiple', true);

        $this->getFormOptions()->shouldReturn([
            'class' => Author::class,
            'multiple' => true
        ]);
    }

    function it_removes_form_options(): void
    {
        $this
            ->addFormOption('class', Author::class)
            ->addFormOption('multiple', true)
            ->removeFormOption('multiple');

        $this->getFormOptions()->shouldReturn([
            'class' => Author::class,
        ]);
    }

    function is_has_no_criteria_by_default(): void
    {
        $this->getCriteria()->shouldReturn([]);
    }

    function it_sets_criteria(): void
    {
        $this->setCriteria(['name' => 'test']);

        $this->getCriteria()->shouldReturn(['name' => 'test']);
    }
}
