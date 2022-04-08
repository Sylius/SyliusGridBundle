<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Builder\Field;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Builder\Field\FieldInterface;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;

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

    function it_creates_fields_with_vars(): void
    {
        $field = $this::create('enabled', '@SyliusUi/Grid/Field/enabled.html.twig')
            ->setOption('vars', ['labels' => 'path/to/label'])
        ;

        $field->shouldHaveType(FieldInterface::class);
        $field->getName()->shouldReturn('enabled');
    }
}
