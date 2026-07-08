<?php

abstract class Nullable
{
    /* testNullableReadonlyOnly */
    readonly ?int $prop;

    /* testNullablePrivateSet */
    private(set) ?int $prop2;

    /* testNullablePublicProtectedSet */
    public protected(set) ?int $prop3;

    /* testNullableFinalOnly */
    final ?bool $prop4;

    /* testNullableAbstractOnly */
    abstract ?string $prop5 { get; }
}

$closure = function (
    /* testClosureParamTypeNullableInt */
    ?Int $a,
    /* testClosureParamTypeNullableCallable */
    ?    Callable $b,
    /* testClosureParamTypeNullableStringWithAttributeAndSlashComment */
    #[AttributeForParam]
    // This must be a slash or hash comment and the next line must **NOT** have any indentation for the PHP 8.5 deprecation notice (issue PHPCSStandards/PHP_CodeSniffer#1216) to occur.
?string $c
/* testClosureReturnTypeNullableInt */
) :?INT{};

/* testFunctionReturnTypeNullableCallable */
function testCallableReturn() : ? callable {}

class InlineThen
{
    /* testInlineThenInPropertyDefaultValue */
    public int $prop = self::SOMECONT ? PHP_CONST ? OTHER_CONST;
}

/* testInlineThenWithArrayDeclaration */
$ternary = true ? array() : null;

/* testInlineThenWithUnqualifiedNameAndNothingElse */
$ternary = true ? CONSTANT_NAME : null;

/* testInlineThenWithUnqualifiedNameAndParens */
$ternary = true ? callMe() : null;

/* testInlineThenWithUnqualifiedNameAndDoubleColon */
$ternary = true ? ClassName::callMe() : null;

/* testInlineThenWithFullyQualifiedNameAndNothingElse */
$ternary = true ? \CONSTANT_NAME : null;

/* testInlineThenWithFullyQualifiedNameAndParens */
$ternary = true ? \Fully\callMe() : null;

/* testInlineThenWithFullyQualifiedNameAndDoubleColon */
$ternary = true ? \Fully\ClassName::callMe() : null;

/* testInlineThenWithPartiallyQualifiedNameAndNothingElse */
$ternary = true ? Partially\CONSTANT_NAME : null;

/* testInlineThenWithPartiallyQualifiedNameAndParens */
$ternary = true ? Partially\callMe() : null;

/* testInlineThenWithPartiallyQualifiedNameAndDoubleColon */
$ternary = true ? Partially\ClassName::callMe() : null;

/* testInlineThenWithNamespaceRelativeNameAndNothingElse */
$ternary = true ? namespace\CONSTANT_NAME : null;

/* testInlineThenWithNamespaceRelativeNameAndParens */
$ternary = true ? namespace\callMe() : null;

/* testInlineThenWithNamespaceRelativeNameAndDoubleColon */
$ternary = true ? namespace\ClassName::callMe() : null;
