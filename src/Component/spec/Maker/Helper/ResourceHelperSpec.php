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

namespace spec\Sylius\Component\Grid\Maker\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Maker\Helper\ResourceHelper;

class ResourceHelperSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
            'sylius.shop_user' => [
                'classes' => [
                    'model' => 'App\Entity\User\ShopUser',
                ],
            ],
            'sylius.admin_user' => [
                'classes' => [
                    'model' => 'App\Entity\User\AdminUser',
                ],
            ],
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceHelper::class);
    }

    function it_can_get_resources_aliases(): void
    {
        $this->getResourcesAliases()->shouldReturn([
            'sylius.shop_user',
            'sylius.admin_user',
        ]);
    }

    function it_can_get_resource_model_for_an_alias(): void
    {
        $this
            ->getResourceModelFromAlias('sylius.shop_user')
            ->shouldReturn('sylius.model.shop_user.class');

        $this
            ->getResourceModelFromAlias('sylius.admin_user')
            ->shouldReturn('sylius.model.admin_user.class');
    }

    function it_can_get_resource_name_for_an_alias(): void
    {
        $this
            ->getResourceNameFromAlias('sylius.shop_user')
            ->shouldReturn('shop_user');

        $this
            ->getResourceNameFromAlias('sylius.admin_user')
            ->shouldReturn('admin_user');
    }

    function it_can_split_a_resource_alias(): void
    {
        $this
            ->splitResourceAlias('sylius.shop_user')
            ->shouldReturn(['sylius', 'shop_user']);

        $this
            ->splitResourceAlias('sylius.admin_user')
            ->shouldReturn(['sylius', 'admin_user']);
    }
}
