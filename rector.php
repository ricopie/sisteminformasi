<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\FuncCall\CompactToVariablesRector;
use Rector\CodeQuality\Rector\Identical\StrlenZeroToIdenticalEmptyStringRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRootFiles()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/app',
        __DIR__.'/tests',
    ])
    ->withCache(__DIR__.'/storage/framework/cache/rector')
    ->withSkip([])
    ->withRules([
        CompactToVariablesRector::class,
        StrlenZeroToIdenticalEmptyStringRector::class,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: false,
        codingStyle: true,
        typeDeclarations: false,
        typeDeclarationDocblocks: false,
        privatization: false,
        naming: false,
        instanceOf: false,
        earlyReturn: false,
    )
    ->withPhpSets(php82: true);
