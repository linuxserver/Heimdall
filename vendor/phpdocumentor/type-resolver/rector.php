<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests/unit'
    ]);

    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);
    $rectorConfig->rule(Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector::class);
    $rectorConfig->rule(Rector\TypeDeclaration\Rector\Closure\AddClosureReturnTypeRector::class);
    $rectorConfig->rule(Rector\PHPUnit\Rector\Class_\AddProphecyTraitRector::class);
    $rectorConfig->importNames();

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_74
    ]);
};
