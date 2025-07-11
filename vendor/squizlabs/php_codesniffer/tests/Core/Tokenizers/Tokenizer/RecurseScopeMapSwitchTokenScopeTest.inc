<?php

/* testSwitchNormalSyntax */
switch ($value) {
    case 1:
        echo 'one';
        break;
    default :
        echo 'other';
        break;
/* testSwitchNormalSyntaxScopeCloser */
}

// Test for https://github.com/squizlabs/PHP_CodeSniffer/issues/497
/* testSwitchAlternativeSyntax */
switch ($value) :
    /* testSwitchAlternativeSyntaxEnsureTestWillNotPickUpWrongColon */
    case 1:
        echo 'one';
        break;
    default:
        echo 'other';
        break;
/* testSwitchAlternativeSyntaxScopeCloser */
endswitch;

// Test for https://github.com/squizlabs/PHP_CodeSniffer/issues/543
/* testSwitchClosureWithinCondition */
switch((function () {
    return 'bar';
})()) /* testSwitchClosureWithinConditionScopeOpener */ {
    case 1:
        return 'test';
/* testSwitchClosureWithinConditionScopeCloser */
}

/* testSwitchClosureWithReturnTypeWithinCondition */
switch((function (): string {
    return 'bar';
})()) /* testSwitchClosureWithReturnTypeWithinConditionScopeOpener */ :
    /* testSwitchClosureWithReturnTypeWithinConditionEnsureTestWillNotPickUpWrongColon */
    case 1:
        return 'test';
/* testSwitchClosureWithReturnTypeWithinConditionScopeCloser */
endswitch;

/* testSwitchArrowFunctionWithinCondition */
switch((fn() => $obj->{$foo . $bar})()) /* testSwitchArrowFunctionWithinConditionScopeOpener */ {
    case 1:
        return 'test';
/* testSwitchArrowFunctionWithinConditionScopeCloser */
}

/* testSwitchArrowFunctionWithReturnTypeWithinCondition */
switch((fn(): string => $condition ? 'foo' : 'bar')()) /* testSwitchArrowFunctionWithReturnTypeWithinConditionScopeOpener */ :
    /* testSwitchArrowFunctionWithReturnTypeWithinConditionEnsureTestWillNotPickUpWrongColon */
    case 1:
        return 'test';
/* testSwitchArrowFunctionWithReturnTypeWithinConditionScopeCloser */
endswitch;
