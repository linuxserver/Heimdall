<?php

/* testExitAsConstant */
exit;

/* testDieAsConstant */
die;

/* testExitAsConstantMixedCase */
Exit;

/* testDieAsConstantUppercase */
DIE;

/* testExitAsFunctionCallNoParam */
exit();

/* testDieAsFunctionCallNoParam */
die();

/* testExitAsFunctionCallWithParam */
exit($exitcode);

/* testDieAsFunctionCallWithParam */
die($status);

/* testExitAsFunctionCallUppercase */
EXIT($exitcode);

/* testDieAsFunctionCallMixedCase */
dIE($status);

/* testExitAsFQFunctionCallWithParam */
\exit($exitcode);

/* testDieAsFQFunctionCallNoParam */
\die($status);


/* testNotExitOOConstantAccess */
$obj::exit;

/* testNotDieOOConstantAccess */
$obj::die;

/* testNotExitOOPropertyAccess */
$obj->exit;

/* testNotDieOOPropertyAccess */
$obj->DIE;

/* testNotExitOOMethodCall */
$obj->exit();

/* testNotDieOOMethodCall */
$obj->die();

class NotReserved {
    /* testNotExitOOConstDeclaration */
    const exit = 10;

    /* testNotDieOOConstDeclaration */
    const die = 'status';

    /* testNotExitOOMethodDeclaration */
    function Exit() {}

    /* testNotDieOOMethodDeclaration */
    function die() {}
}

/* testNotExitParamName */
callMe(exit: 10);

/* testNotDieParamName */
callMe(die: 'status');

/* testNotExitNamespacedName */
use My\exit\NameA;

/* testNotDieNamespacedName */
use My\die\NameB;

/* testExitAsFQConstant */
// Intentional parse error. This is not allowed in PHP, but that's not the concern of the tokenizer. Should still be handled correctly.
\exit;

/* testDieAsFQConstant */
// Intentional parse error. This is not allowed in PHP, but that's not the concern of the tokenizer. Should still be handled correctly.
\die;

/* testNotExitConstantDeclaration */
// Intentional parse error. This is not allowed in PHP, but that's not the concern of the tokenizer. Should still be handled correctly.
const exit = 10;

/* testNotDieConstantDeclaration */
// Intentional parse error. This is not allowed in PHP, but that's not the concern of the tokenizer. Should still be handled correctly.
const die = 'status';

/* testNotExitFunctionDeclaration */
// Intentional parse error. This is not allowed in PHP, but that's not the concern of the tokenizer. Should still be handled correctly.
function exit() {}

/* testNotDieFunctionDeclaration */
// Intentional parse error. This is not allowed in PHP, but that's not the concern of the tokenizer. Should still be handled correctly.
function die() {}
