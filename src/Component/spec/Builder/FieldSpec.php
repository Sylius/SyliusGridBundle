<?php

namespace spec\Sylius\Component\Grid\Builder;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Builder\Field;
use Sylius\Component\Grid\Definition\Field as FieldDefinition;

class FieldSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('name', 'string');
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(Field::class);
    }

    function it_returns_its_definition(): void
    {
        $this->getDefinition()->shouldHaveType(FieldDefinition::class);
    }

    function it_sets_path(): void
    {
        $field = $this->setPath('custom_path');

        $field->getDefinition()->getPath()->shouldReturn('custom_path');
    }

    function it_sets_label(): void
    {
        $field = $this->setLabel('Name');

        $field->getDefinition()->getLabel()->shouldReturn('Name');
    }

    function it_enables_fields(): void
    {
        $field = $this->setEnabled(true);

        $field->getDefinition()->isEnabled()->shouldReturn(true);
    }

    function it_disables_fields(): void
    {
        $field = $this->setEnabled(false);

        $field->getDefinition()->isEnabled()->shouldReturn(false);
    }

    function it_makes_fields_sortable(): void
    {
        $field = $this->setSortable(true);

        $field->getDefinition()->isSortable()->shouldReturn(true);
    }

    function it_makes_fields_not_sortable(): void
    {
        $field = $this->setSortable(false);

        $field->getDefinition()->isSortable()->shouldReturn(false);
    }

    function it_sets_position(): void
    {
        $field = $this->setPosition(42);

        $field->getDefinition()->getPosition()->shouldReturn(42);
    }

    function it_sets_options(): void
    {
        $field = $this->setOptions(['template' => '/path/to/template']);

        $field->getDefinition()->getOptions()->shouldReturn(['template' => '/path/to/template']);
    }
}
