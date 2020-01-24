<?php

namespace spec\Sylius\Component\Grid\Maker\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Maker\Helper\GridHelper;

final class GridHelperSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
            'string' => "@SyliusUi/Grid/Filter/string.html.twig",
            'boolean' => "@SyliusUi/Grid/Filter/boolean.html.twig",
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GridHelper::class);
    }

    function it_can_get_filer_ids(): void
    {
        $this->getFilterIds()->shouldReturn(['string', 'boolean']);
    }
}
