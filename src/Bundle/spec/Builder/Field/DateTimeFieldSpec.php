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
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\FieldInterface;

final class DateTimeFieldSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(DateTimeField::class);
    }

    function it_creates_fields(): void
    {
        $field = $this::create('createdAt');

        $field->shouldHaveType(FieldInterface::class);
        $field->getName()->shouldReturn('createdAt');
    }
}
