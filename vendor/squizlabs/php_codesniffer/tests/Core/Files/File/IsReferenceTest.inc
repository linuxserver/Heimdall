<?php

/* testTokenizerIssue1971PHPCSlt330gt271A */
// This has to be the first test in the file!
[&$a, [$b, /* testTokenizerIssue1971PHPCSlt330gt271B */ &$c]] = $array;

/* testBitwiseAndA */
error_reporting( E_NOTICE & E_STRICT );

/* testBitwiseAndB */
$a = [ $something & $somethingElse ];

/* testBitwiseAndC */
$a = [ $first, $something & self::$somethingElse ];

/* testBitwiseAndD */
$a = array( $first, $something & $somethingElse );

/* testBitwiseAndE */
$a = [ 'a' => $first, 'b' => $something & $somethingElse ];

/* testBitwiseAndF */
$a = array( 'a' => $first, 'b' => $something & \MyClass::$somethingElse );

/* testBitwiseAndG */
$a = $something & $somethingElse;

/* testBitwiseAndH */
function myFunction($a = 10 & 20) {}

/* testBitwiseAndI */
$closure = function ($a = MY_CONSTANT & parent::OTHER_CONSTANT) {};

/* testFunctionReturnByReference */
function &myFunction() {}

/* testFunctionPassByReferenceA */
function myFunction( &$a ) {}

/* testFunctionPassByReferenceB */
function myFunction( $a, &$b ) {}

/* testFunctionPassByReferenceC */
$closure = function ( &$a ) {};

/* testFunctionPassByReferenceD */
$closure = function ( $a, &$b ) {};

/* testFunctionPassByReferenceE */
function myFunction(array &$one) {}

/* testFunctionPassByReferenceF */
$closure = function (\MyClass &$one) {};

/* testFunctionPassByReferenceG */
$closure = function ($param, &...$moreParams) {};

/* testForeachValueByReference */
foreach( $array as $key => &$value ) {}

/* testForeachKeyByReference */
foreach( $array as &$key => $value ) {}

/* testArrayValueByReferenceA */
$a = [ 'a' => &$something ];

/* testArrayValueByReferenceB */
$a = [ 'a' => $something, 'b' => &$somethingElse ];

/* testArrayValueByReferenceC */
$a = [ &$something ];

/* testArrayValueByReferenceD */
$a = [ $something, &$somethingElse ];

/* testArrayValueByReferenceE */
$a = array( 'a' => &$something );

/* testArrayValueByReferenceF */
$a = array( 'a' => $something, 'b' => &$somethingElse );

/* testArrayValueByReferenceG */
$a = array( &$something );

/* testArrayValueByReferenceH */
$a = array( $something, &$somethingElse );

/* testAssignByReferenceA */
$b = &$something;

/* testAssignByReferenceB */
$b =& $something;

/* testAssignByReferenceC */
$b .= &$something;

/* testAssignByReferenceD */
$myValue = &$obj->getValue();

/* testAssignByReferenceE */
$collection = &collector();

/* testAssignByReferenceF */
$collection ??= &collector();

/* testShortListAssignByReferenceNoKeyA */
[
    &$a,
    /* testShortListAssignByReferenceNoKeyB */
    &$b,
    /* testNestedShortListAssignByReferenceNoKey */
    [$c, &$d]
] = $array;

/* testLongListAssignByReferenceNoKeyA */
list($a, &$b, list(/* testLongListAssignByReferenceNoKeyB */ &$c, /* testLongListAssignByReferenceNoKeyC */ &$d)) = $array;

[
    /* testNestedShortListAssignByReferenceWithKeyA */
    'a' => [&$a, $b],
    /* testNestedShortListAssignByReferenceWithKeyB */
    'b' => [$c, &$d]
] = $array;


/* testLongListAssignByReferenceWithKeyA */
list(get_key()[1] => &$e) = [1, 2, 3];

/* testPassByReferenceA */
functionCall(&$something, $somethingElse);

/* testPassByReferenceB */
functionCall($something, &$somethingElse);

/* testPassByReferenceC */
functionCall($something, &$this->somethingElse);

/* testPassByReferenceD */
functionCall($something, &self::$somethingElse);

/* testPassByReferenceE */
functionCall($something, &parent::$somethingElse);

/* testPassByReferenceF */
functionCall($something, &static::$somethingElse);

/* testPassByReferenceG */
functionCall($something, &SomeClass::$somethingElse);

/* testPassByReferenceH */
functionCall(&\SomeClass::$somethingElse);

/* testPassByReferenceI */
functionCall($something, &\SomeNS\SomeClass::$somethingElse);

/* testPassByReferenceJ */
functionCall($something, &namespace\SomeClass::$somethingElse);

/* testPassByReferencePartiallyQualifiedName */
functionCall($something, &Sub\Level\SomeClass::$somethingElse);

/* testNewByReferenceA */
$foobar2 = &new Foobar();

/* testNewByReferenceB */
functionCall( $something , &new Foobar() );

/* testUseByReference */
$closure = function() use (&$var){};

/* testUseByReferenceWithCommentFirstParam */
$closure = function() use /*comment*/ (&$value){};

/* testUseByReferenceWithCommentSecondParam */
$closure = function() use /*comment*/ ($varA, &$varB){};

/* testArrowFunctionReturnByReference */
fn&($x) => $x;

$closure = function (
    /* testBitwiseAndExactParameterA */
    $a = MY_CONSTANT & parent::OTHER_CONSTANT,
    /* testPassByReferenceExactParameterB */
    &$b,
    /* testPassByReferenceExactParameterC */
    &...$c,
    /* testBitwiseAndExactParameterD */
    $d = E_NOTICE & E_STRICT,
) {};

// Issue PHPCS#3049.
/* testArrowFunctionPassByReferenceA */
$fn = fn(array &$one) => 1;

/* testArrowFunctionPassByReferenceB */
$fn = fn($param, &...$moreParams) => 1;

/* testClosureReturnByReference */
$closure = function &($param) use ($value) {};

/* testBitwiseAndArrowFunctionInDefault */
$fn = fn( $one = E_NOTICE & E_STRICT) => 1;

/* testIntersectionIsNotReference */
function intersect(Foo&Bar $param) {}

/* testDNFTypeIsNotReference */
$fn = fn((Foo&\Bar)|null /* testParamPassByReference */ &$param) => $param;

/* testTokenizerIssue1284PHPCSlt280A */
if ($foo) {}
[&$a, /* testTokenizerIssue1284PHPCSlt280B */ &$b] = $c;

/* testTokenizerIssue1284PHPCSlt280C */
if ($foo) {}
[&$a, $b];
