<?php

/* testBracesAndColon */
switch ($var[10]) {
    case TEST_COLON:
        break;
}

/* testNamedParamColon */
callMe(name: $var);

/* testReturnTypeColon */
$closure = function(): Type {};

/* testConcat */
echo 'text' . $var;

/* testSimpleMathTokens */
$a = 10 * 3 / 2 + 5 - 4 % 2;

/* testUnaryPlusMinus */
$a = +10 / -1;

/* testBitwiseTokens */
$a = CONST_A ^ CONST_B & CONST_C | CONST_D ~ CONST_E;

try {
/* testBitwiseOrInCatch */
} catch ( Exception_A | Exception_B $e ) {
}

/* testLessThan */
$a = 10 < $var;

/* testGreaterThan */
$a = 10 > $var;

/* testBooleanNot */
$a = ! $var;

/* testComma */
echo $a, $b, $c;

/* testAsperand */
$a = @callMe();

/* testDollarAndCurlies */
echo ${$var};

/* testBacktick */
$a = `ls -e`;
