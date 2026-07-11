<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRootFiles()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/app',
    ])
    ->withCache(__DIR__.'/storage/framework/cache/rector')
    ->withPreparedSets(
        deadCode: true,
        typeDeclarations: true,
    )
    ->withPhpSets(php82: true);
