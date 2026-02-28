<?php

/** @noinspection DevelopmentDependenciesUsageInspection */

declare(strict_types=1);

use Rector\CodeQuality\Rector\Catch_\ThrowWithPreviousExceptionRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        # typeDeclarationDocblocks: true, # Adds some incorrect doc blocks in interface implementation
        instanceOf: true,
        earlyReturn: true,
        phpunitCodeQuality: true,
    )
    ->withBootstrapFiles(["tools/.rector/vendor/autoload.php"])
    ->withImportNames(importShortClasses: false, removeUnusedImports: true)
    ->withAttributesSets(phpunit: true)
    ->withComposerBased(phpunit: true)
    ->withTreatClassesAsFinal()
    ->withFluentCallNewLine()
    ->withSkip([
        ThrowWithPreviousExceptionRector::class,
    ])
;
