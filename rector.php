<?php

/** @noinspection DevelopmentDependenciesUsageInspection */

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        typeDeclarations: true,
        # typeDeclarationDocblocks: true, # Adds some incorrect doc blocks in interface implementation
        privatization: true,
        earlyReturn: true,
        phpunitCodeQuality: true,
    )
    ->withImportNames(importShortClasses: false, removeUnusedImports: true)
;
