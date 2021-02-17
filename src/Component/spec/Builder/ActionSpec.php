<?php

namespace spec\Sylius\Component\Grid\Builder;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Builder\Action;
use Sylius\Component\Grid\Definition\Action as ActionDefinition;

class ActionSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('create', 'create');
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(Action::class);
    }

    function it_returns_its_definition(): void
    {
        $this->getDefinition()->shouldHaveType(ActionDefinition::class);
    }

    function it_sets_label(): void
    {
        $field = $this->setLabel('Create');

        $field->getDefinition()->getLabel()->shouldReturn('Create');
    }

    function it_enables_actions(): void
    {
        $field = $this->setEnabled(true);

        $field->getDefinition()->isEnabled()->shouldReturn(true);
    }

    function it_disables_actions(): void
    {
        $field = $this->setEnabled(false);

        $field->getDefinition()->isEnabled()->shouldReturn(false);
    }

    function it_sets_icon(): void
    {
        $field = $this->setIcon('cogs');

        $field->getDefinition()->getIcon()->shouldReturn('cogs');
    }

    function it_sets_options(): void
    {
        $field = $this->setOptions(['custom' => true]);

        $field->getDefinition()->getOptions()->shouldReturn(['custom' => true]);
    }

    function it_sets_position(): void
    {
        $field = $this->setPosition(42);

        $field->getDefinition()->getPosition()->shouldReturn(42);
    }
}
