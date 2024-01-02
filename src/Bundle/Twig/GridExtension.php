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

namespace Sylius\Bundle\GridBundle\Twig;

use Sylius\Bundle\GridBundle\Templating\Helper\GridHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class GridExtension extends AbstractExtension
{
    private GridHelper $gridHelper;

    public function __construct(GridHelper $gridHelper)
    {
        $this->gridHelper = $gridHelper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_grid_render', [$this->gridHelper, 'renderGrid'], ['is_safe' => ['html']]),
            new TwigFunction('sylius_grid_render_field', [$this->gridHelper, 'renderField'], ['is_safe' => ['html']]),
            new TwigFunction('sylius_grid_render_action', [$this->gridHelper, 'renderAction'], ['is_safe' => ['html']]),
            new TwigFunction('sylius_grid_render_filter', [$this->gridHelper, 'renderFilter'], ['is_safe' => ['html']]),
        ];
    }
}
