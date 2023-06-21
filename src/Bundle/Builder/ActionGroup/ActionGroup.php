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

namespace Sylius\Bundle\GridBundle\Builder\ActionGroup;

use Sylius\Bundle\GridBundle\Builder\Action\ActionInterface;

final class ActionGroup implements ActionGroupInterface
{
    private string $name;

    private array $actions = [];

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function create(string $name, ActionInterface ...$actions): ActionGroupInterface
    {
        $actionGroup = new self($name);

        array_map(function (ActionInterface $action) use ($actionGroup) {
            $actionGroup->addAction($action);
        }, $actions);

        return $actionGroup;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addAction(ActionInterface $action): ActionGroupInterface
    {
        $this->actions[$action->getName()] = $action;

        return $this;
    }

    public function removeAction(string $name): ActionGroupInterface
    {
        unset($this->actions[$name]);

        return $this;
    }

    public function toArray(): array
    {
        $output = [];

        if (count($this->actions) > 0) {
            $output = array_map(function (ActionInterface $action) { return $action->toArray(); }, $this->actions);
        }

        return $output;
    }
}
