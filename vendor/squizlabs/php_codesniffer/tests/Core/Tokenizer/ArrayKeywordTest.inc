<?php

/* testEmptyArray */
$var = array();

/* testArrayWithSpace */
$var = array    (1 => 10);

/* testArrayWithComment */
$var = Array /*comment*/ (1 => 10);

/* testNestingArray */
$var = array(
    /* testNestedArray */
    array(
        'key' => 'value',

        /* testClosureReturnType */
        'closure' => function($a) use($global) : Array {},
    ),
);

/* testFunctionDeclarationParamType */
function typedParam(array $a) {}

/* testFunctionDeclarationReturnType */
function returnType($a) : int|array|null {}

class Bar {
    /* testClassConst */
    const ARRAY = [];

    /* testClassMethod */
    public function array() {}

    /* testOOConstType */
    const array /* testTypedOOConstName */ ARRAY = /* testOOConstDefault */ array();

    /* testOOPropertyType */
    protected array $property;
}
