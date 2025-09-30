<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PhpSpecToPHPUnit\Set\MigrationSetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);

    $rectorConfig->ruleWithConfiguration(TypedPropertyFromAssignsRector::class, [
        'inline_public' => false,
    ]);

    $rectorConfig->sets([MigrationSetList::PHPSPEC_TO_PHPUNIT]);
};
