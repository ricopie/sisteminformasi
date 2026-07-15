<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameVariableToMatchNewTypeRector;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchExprVariableRector;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchMethodCallReturnTypeRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Privatization\Rector\Class_\FinalizeTestCaseClassRector;
use Rector\Privatization\Rector\ClassConst\PrivatizeFinalClassConstantRector;
use Rector\Privatization\Rector\ClassMethod\PrivatizeFinalClassMethodRector;
use Rector\Privatization\Rector\MethodCall\PrivatizeLocalGetterToPropertyRector;
use Rector\Privatization\Rector\Property\PrivatizeFinalClassPropertyRector;

$skipAppRules = [
    RenameVariableToMatchMethodCallReturnTypeRector::class,
    RenameParamToMatchTypeRector::class,
    RenameVariableToMatchNewTypeRector::class,
    RenamePropertyToMatchTypeRector::class,
    RenameForeachValueVariableToMatchExprVariableRector::class,
    RenameForeachValueVariableToMatchMethodCallReturnTypeRector::class,
    PrivatizeFinalClassMethodRector::class,
    PrivatizeFinalClassPropertyRector::class,
    PrivatizeFinalClassConstantRector::class,
    PrivatizeLocalGetterToPropertyRector::class,
];

return RectorConfig::configure()
    ->withRootFiles()
    // ── Paths ──────────────────────────────────────────────
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/src',
        __DIR__.'/database',
        __DIR__.'/tests',
    ])
    ->withCache(__DIR__.'/storage/framework/cache/data/rector')
    // ── Skip ───────────────────────────────────────────────
    ->withSkip([
        __DIR__.'/vendor',
        __DIR__.'/storage',
        __DIR__.'/bootstrap/cache',

        // Skip naming rules for app/ (Laravel uses its own naming conventions)
        ...array_fill_keys($skipAppRules, [__DIR__.'/app']),

        FinalizeTestCaseClassRector::class => [
            __DIR__.'/tests',
        ],
    ])
    // ── PHP Version Baseline ───────────────────────────────
    ->withPhpSets(
        php82: true
    )
    // ── Prepared Sets ──────────────────────────────────────
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        naming: true,
        namedArgs: true,
        instanceOf: true,
        earlyReturn: true,
    )
    // ── PHPUnit Sets ──────────────────────────────────────
    ->withSets([
        PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,
        PHPUnitSetList::PHPUNIT_110,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::PHPUNIT_MOCK_TO_STUB,
    ])
    // ── Import Names ───────────────────────────────────────
    ->withImportNames(
        importNames: true,
        importDocBlockNames: true,
        importShortClasses: true,
        removeUnusedImports: true,
    )
    // ── Treat classes as final (DDD strictness) ────────────
    ->withTreatClassesAsFinal();
