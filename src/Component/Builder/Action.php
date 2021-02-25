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

namespace Sylius\Component\Grid\Builder;

use Sylius\Component\Grid\Definition\Action as ActionDefinition;

final class Action implements ActionInterface
{
    private ActionDefinition $action;

    public function __construct(string $name, string $type)
    {
        $this->action = ActionDefinition::fromNameAndType($name, $type);
    }

    public static function create(string $name, string $type): ActionInterface
    {
        return new self($name, $type);
    }

    public function getDefinition(): ActionDefinition
    {
        return $this->action;
    }

    public function setLabel(string $label): ActionInterface
    {
        $this->action->setLabel($label);

        return $this;
    }

    public function setEnabled(bool $enabled): ActionInterface
    {
        $this->action->setEnabled($enabled);

        return $this;
    }

    public function setIcon(string $icon): ActionInterface
    {
        $this->action->setIcon($icon);

        return $this;
    }

    public function setOptions(array $options): ActionInterface
    {
        $this->action->setOptions($options);

        return $this;
    }

    public function setPosition(int $position): ActionInterface
    {
        $this->action->setPosition($position);

        return $this;
    }
}
