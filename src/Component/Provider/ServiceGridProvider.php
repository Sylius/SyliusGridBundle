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

namespace Sylius\Component\Grid\Provider;

use App\Bridge\Grid\GridRegistry;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ServiceGridProvider implements GridProviderInterface
{
    private $gridRegistry;

    public const EVENT_NAME = 'sylius.grid.%s';

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher, GridRegistry $gridRegistry)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->gridRegistry = $gridRegistry;
    }

    public function get(string $code): Grid
    {
        $grid = $this->gridRegistry->getGrid($code);

        if (null === $grid) {
            throw new UndefinedGridException($code);
        }

        $gridDefinition = $grid->getDefinition();
        $this->eventDispatcher->dispatch(new GridDefinitionConverterEvent($gridDefinition), $this->getEventName($code));

        return $gridDefinition;
    }

    private function getEventName(string $code): string
    {
        return sprintf(self::EVENT_NAME, str_replace('sylius_', '', $code));
    }
}
