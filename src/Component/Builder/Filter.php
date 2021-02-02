<?php


declare(strict_types=1);

namespace Sylius\Component\Grid\Builder;

use Sylius\Component\Grid\Definition\Filter as FilterDefinition;

class Filter implements FilterInterface
{
    private FilterDefinition $filterDefinition;

    public function __construct(string $name, string $type)
    {
        $this->filterDefinition = FilterDefinition::fromNameAndType($name, $type);
    }

    public static function create(string $name, string $type): FilterInterface
    {
        return new self($name, $type);
    }

    public function getDefinition(): FilterDefinition
    {
        return $this->filterDefinition;
    }

    public function setLabel(?string $label): FilterInterface
    {
        $this->filterDefinition->setLabel($label);

        return $this;
    }

    public function setEnabled(bool $enabled): FilterInterface
    {
        $this->filterDefinition->setEnabled($enabled);

        return $this;
    }

    public function setTemplate(string $template): FilterInterface
    {
        $this->filterDefinition->setTemplate($template);

        return $this;
    }

    public function setOptions(array $options): FilterInterface
    {
        $this->filterDefinition->setOptions($options);

        return $this;
    }

    public function setFormOptions(array $formOptions): FilterInterface
    {
        $this->filterDefinition->setFormOptions($formOptions);

        return $this;
    }

    public function setCriteria(array $criteria): FilterInterface
    {
        $this->filterDefinition->setCriteria($criteria);

        return $this;
    }
}
