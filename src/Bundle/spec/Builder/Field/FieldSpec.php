<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Builder\Field;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Builder\Field\Field;
use Sylius\Bundle\GridBundle\Builder\Field\FieldInterface;

final class FieldSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedThrough('create', ['name', 'string']);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(Field::class);
    }

    function it_implements_an_interface(): void
    {
        $this->shouldImplement(FieldInterface::class);
    }

    function it_has_no_path_by_default(): void
    {
        $this->getPath()->shouldReturn(null);
    }

    function it_sets_path(): void
    {
        $this->setPath('custom_path');

        $this->getPath()->shouldReturn('custom_path');
    }

    function it_has_no_label_by_default(): void
    {
        $this->getLabel()->shouldReturn(null);
    }

    function it_sets_label(): void
    {
        $this->setLabel('Name');

        $this->getLabel()->shouldReturn('Name');
    }

    function it_enables_fields(): void
    {
        $this->setEnabled(true);

        $this->isEnabled()->shouldReturn(true);
    }

    function it_disables_fields(): void
    {
        $this->setEnabled(false);

        $this->isEnabled()->shouldReturn(false);
    }

    function it_makes_fields_sortable(): void
    {
        $this->setSortable(true);

        $this->isSortable()->shouldReturn(true);
        $this->toArray()['sortable']->shouldReturn(true);
    }

    function it_makes_fields_sortable_with_path(): void
    {
        $this->setSortable(true, 'path');

        $this->isSortable()->shouldReturn(true);
        $this->toArray()['sortable']->shouldReturn('path');
    }

    function it_makes_fields_not_sortable(): void
    {
        $this->setSortable(false);

        $this->isSortable()->shouldReturn(false);
        $this->toArray()['sortable']->shouldReturn(null);
    }

    function it_sets_position(): void
    {
        $this->setPosition(42);

        $this->getPosition()->shouldReturn(42);
    }

    function it_sets_options(): void
    {
        $this->setOptions(['template' => '/path/to/template']);

        $this->getOptions()->shouldReturn(['template' => '/path/to/template']);
    }

    function it_sets_one_option(): void
    {
        $this->setOptions(['template' => '/path/to/template']);
        $this->setOption('vars', ['labels' => '/path/to/label']);

        $this->getOptions()->shouldReturn([
            'template' => '/path/to/template',
            'vars' => ['labels' => '/path/to/label'],
        ]);
    }

    function it_adds_options(): void
    {
        $this->setOptions(['template' => '/path/to/template']);
        $this->addOptions(['vars' => ['labels' => '/path/to/label']]);

        $this->getOptions()->shouldReturn([
            'template' => '/path/to/template',
            'vars' => ['labels' => '/path/to/label'],
        ]);
    }

    function it_removes_options(): void
    {
        $this->setOptions([
            'template' => '/path/to/template',
            'vars' => [
                'labels' => '/path/to/label',
            ],
        ]);

        $this->removeOption('vars');

        $this->getOptions()->shouldReturn([
            'template' => '/path/to/template',
        ]);
    }
}
