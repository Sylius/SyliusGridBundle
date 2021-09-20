<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Builder\Field;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Config\Builder\Field\FieldInterface;
use Sylius\Component\Grid\Config\Builder\Field\TwigField;

final class TwigFieldSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(TwigField::class);
    }

    function it_creates_fields(): void
    {
        $field = $this::create('enabled', '@SyliusUi/Grid/Field/enabled.html.twig');

        $field->shouldHaveType(FieldInterface::class);
        $field->getName()->shouldReturn('enabled');
    }
}
