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

namespace Sylius\Component\Grid\Tests\Dummy;

use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class ClassAsParameterGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    private string $authorClass;

    public function __construct(string $authorClass)
    {
        $this->authorClass = $authorClass;
    }

    public static function getName(): string
    {
        return 'app_class_as_parameter';
    }

    public function getResourceClass(): string
    {
        return $this->authorClass;
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
    }
}
