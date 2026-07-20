<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Assign\CombinedAssignRector;
// ── Naming ─────────────────────────────────────────────
use Rector\CodeQuality\Rector\AssignOp\NewArrayItemConcatAssignToAssignRector;
use Rector\CodeQuality\Rector\Attribute\ExplicitAttributeNamedArgsRector;
use Rector\CodeQuality\Rector\Attribute\SortAttributeNamedArgsRector;
use Rector\CodeQuality\Rector\BooleanAnd\RemoveUselessIsObjectCheckRector;
use Rector\CodeQuality\Rector\BooleanAnd\RepeatedAndNotEqualToNotInArrayRector;
use Rector\CodeQuality\Rector\BooleanAnd\SimplifyEmptyArrayCheckRector;
// ── Privatization ──────────────────────────────────────
use Rector\CodeQuality\Rector\BooleanNot\NegatedAndsToPositiveOrsRector;
use Rector\CodeQuality\Rector\BooleanNot\ReplaceConstantBooleanNotRector;
use Rector\CodeQuality\Rector\BooleanNot\ReplaceMultipleBooleanNotRector;
use Rector\CodeQuality\Rector\BooleanNot\SimplifyDeMorganBinaryRector;
// ── Early Return ───────────────────────────────────────
use Rector\CodeQuality\Rector\BooleanOr\RepeatedOrEqualToInArrayRector;
use Rector\CodeQuality\Rector\CallLike\AddNameToBooleanArgumentRector;
use Rector\CodeQuality\Rector\CallLike\AddNameToNullArgumentRector;
use Rector\CodeQuality\Rector\Catch_\ThrowWithPreviousExceptionRector;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\Class_\ConvertStaticToSelfRector;
use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\CodeQuality\Rector\Class_\InnerFunctionToPrivateMethodRector;
// ── InstanceOf ─────────────────────────────────────────
use Rector\CodeQuality\Rector\Class_\RemoveReadonlyPropertyVisibilityOnReadonlyClassRector;
// ── Named Args ─────────────────────────────────────────
use Rector\CodeQuality\Rector\ClassConstFetch\VariableConstFetchToClassConstFetchRector;
use Rector\CodeQuality\Rector\ClassMethod\ExplicitReturnNullRector;
use Rector\CodeQuality\Rector\ClassMethod\InlineArrayReturnAssignRector;
use Rector\CodeQuality\Rector\ClassMethod\LocallyCalledStaticMethodToNonStaticRector;
// ── Code Quality ───────────────────────────────────────
use Rector\CodeQuality\Rector\ClassMethod\OptionalParametersAfterRequiredRector;
use Rector\CodeQuality\Rector\Empty_\SimplifyEmptyCheckOnEmptyArrayRector;
use Rector\CodeQuality\Rector\Equal\UseIdenticalOverEqualWithSameTypeRector;
use Rector\CodeQuality\Rector\Expression\InlineIfToExplicitIfRector;
use Rector\CodeQuality\Rector\Expression\TernaryFalseExpressionToIfRector;
use Rector\CodeQuality\Rector\For_\ForRepeatedCountToOwnVariableRector;
use Rector\CodeQuality\Rector\Foreach_\ForeachItemsAssignToEmptyArrayToAssignRector;
use Rector\CodeQuality\Rector\Foreach_\ForeachToInArrayRector;
use Rector\CodeQuality\Rector\Foreach_\SimplifyForeachToCoalescingRector;
use Rector\CodeQuality\Rector\Foreach_\UnusedForeachValueToArrayKeysRector;
use Rector\CodeQuality\Rector\FuncCall\ArrayMergeOfNonArraysToSimpleArrayRector;
use Rector\CodeQuality\Rector\FuncCall\CallUserFuncWithArrowFunctionToInlineRector;
use Rector\CodeQuality\Rector\FuncCall\ChangeArrayPushToArrayAssignRector;
use Rector\CodeQuality\Rector\FuncCall\CompactToVariablesRector;
use Rector\CodeQuality\Rector\FuncCall\InlineIsAInstanceOfRector;
use Rector\CodeQuality\Rector\FuncCall\IsAWithStringWithThirdArgumentRector;
use Rector\CodeQuality\Rector\FuncCall\RemoveSoleValueSprintfRector;
use Rector\CodeQuality\Rector\FuncCall\SetTypeToCastRector;
use Rector\CodeQuality\Rector\FuncCall\SimplifyFuncGetArgsCountRector;
use Rector\CodeQuality\Rector\FuncCall\SimplifyInArrayValuesRector;
use Rector\CodeQuality\Rector\FuncCall\SimplifyRegexPatternRector;
use Rector\CodeQuality\Rector\FuncCall\SimplifyStrposLowerRector;
use Rector\CodeQuality\Rector\FuncCall\SingleInArrayToCompareRector;
use Rector\CodeQuality\Rector\FuncCall\SortCallLikeNamedArgsRector;
use Rector\CodeQuality\Rector\FuncCall\UnwrapSprintfOneArgumentRector;
use Rector\CodeQuality\Rector\Identical\BooleanNotIdenticalToNotIdenticalRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodeQuality\Rector\Identical\SimplifyArraySearchRector;
use Rector\CodeQuality\Rector\Identical\SimplifyBoolIdenticalTrueRector;
use Rector\CodeQuality\Rector\Identical\SimplifyConditionsRector;
use Rector\CodeQuality\Rector\Identical\StrlenZeroToIdenticalEmptyStringRector;
use Rector\CodeQuality\Rector\If_\ArrayExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\If_\CombineIfRector;
use Rector\CodeQuality\Rector\If_\CompleteMissingIfElseBracketRector;
use Rector\CodeQuality\Rector\If_\ConsecutiveNullCompareReturnsToNullCoalesceQueueRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\If_\ObjectExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\If_\ShortenElseIfRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfNotNullReturnRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfNullableReturnRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector;
use Rector\CodeQuality\Rector\Include_\AbsolutizeRequireAndIncludePathRector;
use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\CodeQuality\Rector\LogicalAnd\AndAssignsToSeparateLinesRector;
use Rector\CodeQuality\Rector\LogicalAnd\LogicalToBooleanRector;
use Rector\CodeQuality\Rector\New_\NewStaticToNewSelfRector;
use Rector\CodeQuality\Rector\NotEqual\CommonNotEqualRector;
use Rector\CodeQuality\Rector\NullsafeMethodCall\CleanupUnneededNullsafeOperatorRector;
use Rector\CodeQuality\Rector\Property\FixClassCaseSensitivityVarDocblockRector;
use Rector\CodeQuality\Rector\StmtsAwareInterface\MoveInnerFunctionToTopLevelRector;
use Rector\CodeQuality\Rector\Switch_\SingularSwitchToIfRector;
use Rector\CodeQuality\Rector\Switch_\SwitchTrueToMatchRector;
use Rector\CodeQuality\Rector\Ternary\ArrayKeyExistsTernaryThenValueToCoalescingRector;
use Rector\CodeQuality\Rector\Ternary\NumberCompareToMaxFuncCallRector;
use Rector\CodeQuality\Rector\Ternary\SimplifyTautologyTernaryRector;
use Rector\CodeQuality\Rector\Ternary\SwitchNegatedTernaryRector;
use Rector\CodeQuality\Rector\Ternary\TernaryEmptyArrayArrayDimFetchToCoalesceRector;
use Rector\CodeQuality\Rector\Ternary\TernaryImplodeToImplodeRector;
use Rector\CodeQuality\Rector\Ternary\UnnecessaryTernaryExpressionRector;
use Rector\CodingStyle\Rector\If_\AlternativeIfToBracketRector;
use Rector\Config\RectorConfig;
use Rector\EarlyReturn\Rector\Foreach_\ChangeNestedForeachIfsToEarlyContinueRector;
use Rector\EarlyReturn\Rector\If_\ChangeIfElseValueAssignToEarlyReturnRector;
use Rector\EarlyReturn\Rector\If_\ChangeNestedIfsToEarlyReturnRector;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfContinueToMultiContinueRector;
use Rector\EarlyReturn\Rector\If_\RemoveAlwaysElseRector;
use Rector\EarlyReturn\Rector\Return_\PreparedValueToEarlyReturnRector;
use Rector\EarlyReturn\Rector\Return_\ReturnBinaryOrToEarlyReturnRector;
use Rector\EarlyReturn\Rector\StmtsAwareInterface\ReturnEarlyIfVariableRector;
use Rector\Instanceof_\Rector\Ternary\FlipNegatedTernaryInstanceofRector;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameVariableToMatchNewTypeRector;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchExprVariableRector;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchMethodCallReturnTypeRector;
use Rector\NetteUtils\Rector\StaticCall\UtilsJsonStaticCallNamedArgRector;
use Rector\Privatization\Rector\ClassConst\PrivatizeFinalClassConstantRector;
use Rector\Privatization\Rector\ClassMethod\PrivatizeFinalClassMethodRector;
use Rector\Privatization\Rector\MethodCall\PrivatizeLocalGetterToPropertyRector;
use Rector\Privatization\Rector\Property\PrivatizeFinalClassPropertyRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;

// ═══════════════════════════════════════════════════════════
// Paths
// ═══════════════════════════════════════════════════════════

$domainApplicationPaths = [
    __DIR__.'/src/Shared/Domain',
    __DIR__.'/src/Shared/Application',
    __DIR__.'/src/Contexts/*/Domain',
    __DIR__.'/src/Contexts/*/Application',
];

$conservativePaths = [
    ...$domainApplicationPaths,
    __DIR__.'/database',
    __DIR__.'/tests',
];

// ═══════════════════════════════════════════════════════════
// Aggressive rules — skipped from conservative + app paths
// ═══════════════════════════════════════════════════════════

$aggressiveRules = [
    // Naming
    RenameVariableToMatchMethodCallReturnTypeRector::class,
    RenameParamToMatchTypeRector::class,
    RenameVariableToMatchNewTypeRector::class,
    RenamePropertyToMatchTypeRector::class,
    RenameForeachValueVariableToMatchExprVariableRector::class,
    RenameForeachValueVariableToMatchMethodCallReturnTypeRector::class,

    // Privatization
    PrivatizeFinalClassMethodRector::class,
    PrivatizeFinalClassPropertyRector::class,
    PrivatizeFinalClassConstantRector::class,
    PrivatizeLocalGetterToPropertyRector::class,

    // Early Return
    ChangeNestedIfsToEarlyReturnRector::class,
    RemoveAlwaysElseRector::class,
    ReturnEarlyIfVariableRector::class,
    ReturnBinaryOrToEarlyReturnRector::class,
    ChangeNestedForeachIfsToEarlyContinueRector::class,
    ChangeOrIfContinueToMultiContinueRector::class,
    ChangeIfElseValueAssignToEarlyReturnRector::class,
    PreparedValueToEarlyReturnRector::class,

    // InstanceOf
    FlipNegatedTernaryInstanceofRector::class,

    // Named Args
    AddNameToBooleanArgumentRector::class,
    AddNameToNullArgumentRector::class,
    ExplicitAttributeNamedArgsRector::class,
    UtilsJsonStaticCallNamedArgRector::class,

    // Code Quality
    CombinedAssignRector::class,
    NewArrayItemConcatAssignToAssignRector::class,
    SortAttributeNamedArgsRector::class,
    RemoveUselessIsObjectCheckRector::class,
    RepeatedAndNotEqualToNotInArrayRector::class,
    SimplifyEmptyArrayCheckRector::class,
    NegatedAndsToPositiveOrsRector::class,
    ReplaceConstantBooleanNotRector::class,
    ReplaceMultipleBooleanNotRector::class,
    SimplifyDeMorganBinaryRector::class,
    RepeatedOrEqualToInArrayRector::class,
    ThrowWithPreviousExceptionRector::class,
    CompleteDynamicPropertiesRector::class,
    ConvertStaticToSelfRector::class,
    InlineConstructorDefaultToPropertyRector::class,
    InnerFunctionToPrivateMethodRector::class,
    RemoveReadonlyPropertyVisibilityOnReadonlyClassRector::class,
    VariableConstFetchToClassConstFetchRector::class,
    ExplicitReturnNullRector::class,
    InlineArrayReturnAssignRector::class,
    LocallyCalledStaticMethodToNonStaticRector::class,
    OptionalParametersAfterRequiredRector::class,
    SimplifyEmptyCheckOnEmptyArrayRector::class,
    UseIdenticalOverEqualWithSameTypeRector::class,
    InlineIfToExplicitIfRector::class,
    TernaryFalseExpressionToIfRector::class,
    ForRepeatedCountToOwnVariableRector::class,
    ForeachItemsAssignToEmptyArrayToAssignRector::class,
    ForeachToInArrayRector::class,
    SimplifyForeachToCoalescingRector::class,
    UnusedForeachValueToArrayKeysRector::class,
    ArrayMergeOfNonArraysToSimpleArrayRector::class,
    CallUserFuncWithArrowFunctionToInlineRector::class,
    ChangeArrayPushToArrayAssignRector::class,
    CompactToVariablesRector::class,
    InlineIsAInstanceOfRector::class,
    IsAWithStringWithThirdArgumentRector::class,
    RemoveSoleValueSprintfRector::class,
    SetTypeToCastRector::class,
    SimplifyFuncGetArgsCountRector::class,
    SimplifyInArrayValuesRector::class,
    SimplifyRegexPatternRector::class,
    SimplifyStrposLowerRector::class,
    SingleInArrayToCompareRector::class,
    SortCallLikeNamedArgsRector::class,
    UnwrapSprintfOneArgumentRector::class,
    BooleanNotIdenticalToNotIdenticalRector::class,
    FlipTypeControlToUseExclusiveTypeRector::class,
    SimplifyArraySearchRector::class,
    SimplifyBoolIdenticalTrueRector::class,
    SimplifyConditionsRector::class,
    StrlenZeroToIdenticalEmptyStringRector::class,
    ArrayExplicitBoolCompareRector::class,
    CombineIfRector::class,
    CompleteMissingIfElseBracketRector::class,
    ConsecutiveNullCompareReturnsToNullCoalesceQueueRector::class,
    ExplicitBoolCompareRector::class,
    ObjectExplicitBoolCompareRector::class,
    ShortenElseIfRector::class,
    SimplifyIfElseToTernaryRector::class,
    SimplifyIfNotNullReturnRector::class,
    SimplifyIfNullableReturnRector::class,
    SimplifyIfReturnBoolRector::class,
    AbsolutizeRequireAndIncludePathRector::class,
    IssetOnPropertyObjectToPropertyExistsRector::class,
    AndAssignsToSeparateLinesRector::class,
    LogicalToBooleanRector::class,
    NewStaticToNewSelfRector::class,
    CommonNotEqualRector::class,
    CleanupUnneededNullsafeOperatorRector::class,
    FixClassCaseSensitivityVarDocblockRector::class,
    MoveInnerFunctionToTopLevelRector::class,
    SingularSwitchToIfRector::class,
    SwitchTrueToMatchRector::class,
    ArrayKeyExistsTernaryThenValueToCoalescingRector::class,
    NumberCompareToMaxFuncCallRector::class,
    SimplifyTautologyTernaryRector::class,
    SwitchNegatedTernaryRector::class,
    TernaryEmptyArrayArrayDimFetchToCoalesceRector::class,
    TernaryImplodeToImplodeRector::class,
    UnnecessaryTernaryExpressionRector::class,
    AlternativeIfToBracketRector::class,
    DisallowedEmptyRuleFixerRector::class,
    SafeDeclareStrictTypesRector::class,
];

$namingRules = [
    RenameVariableToMatchMethodCallReturnTypeRector::class,
    RenameParamToMatchTypeRector::class,
    RenameVariableToMatchNewTypeRector::class,
    RenamePropertyToMatchTypeRector::class,
    RenameForeachValueVariableToMatchExprVariableRector::class,
    RenameForeachValueVariableToMatchMethodCallReturnTypeRector::class,
];

// ═══════════════════════════════════════════════════════════
// Skip map — merge paths per rule (avoids array key overwrite)
// ═══════════════════════════════════════════════════════════

$skipMap = [];
foreach ($aggressiveRules as $rule) {
    $skipMap[$rule] = $conservativePaths;
}

foreach ($namingRules as $rule) {
    $skipMap[$rule] = array_unique(array_merge($skipMap[$rule], [__DIR__.'/app']));
}

// ═══════════════════════════════════════════════════════════
// Rector Configuration
// ═══════════════════════════════════════════════════════════

return RectorConfig::configure()
    ->withRootFiles()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/src',
        __DIR__.'/database',
        __DIR__.'/tests',
    ])
    ->withCache(__DIR__.'/storage/framework/cache/data/rector')
    ->withSkip([
        __DIR__.'/vendor',
        __DIR__.'/storage',
        __DIR__.'/bootstrap/cache',
        ...glob(__DIR__.'/src/Contexts/*/Infrastructure/*/Migrations') ?? [],
        ...$skipMap,
    ])
    ->withPhpSets(php82: true)
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
    ->withImportNames(
        importNames: true,
        importDocBlockNames: true,
        importShortClasses: true,
        removeUnusedImports: true,
    );
