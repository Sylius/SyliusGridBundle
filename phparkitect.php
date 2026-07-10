<?php

declare(strict_types=1);

use Arkitect\ClassSet;
use Arkitect\CLI\Config;
use Arkitect\RuleBuilders\Architecture\Architecture;

return static function (Config $config): void {
    $classSet = ClassSet::fromDir(__DIR__.'/src')
        ->excludePath('Component/Tests');

    $layeredArchitectureRules = Architecture::withComponents()
        ->component('Bundle')->definedBy('Sylius\Bundle\GridBundle\*')
        ->component('Component')->definedBy('Sylius\Component\Grid\*')

        ->where('Component')->shouldNotDependOnAnyComponent()

        ->rules();

    $config->add(
        $classSet,
        ...$layeredArchitectureRules,
    );
};
