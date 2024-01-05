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

namespace spec\Sylius\Bundle\GridBundle\Builder\Field;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Builder\Field\FieldInterface;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;

final class StringFieldSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(StringField::class);
    }

    function it_creates_fields(): void
    {
        $field = $this::create('firstName');

        $field->shouldHaveType(FieldInterface::class);
        $field->getName()->shouldReturn('firstName');
    }
}
