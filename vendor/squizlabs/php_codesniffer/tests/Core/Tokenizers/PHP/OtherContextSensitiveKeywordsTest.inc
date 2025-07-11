<?php

class OtherContextSensitiveKeywords
{
    const /* testParent */ PARENT = 'PARENT';
    const /* testSelf */ SELF = 'SELF';

    const /* testFalse */ FALSE = 'FALSE',
    const /* testTrue */ TRUE = 'TRUE',
    const /* testNull */ NULL = 'NULL',
}

abstract class SomeClass
{
    final function someFunction(
        /* testSelfIsKeyword */
        self $self,
        /* testParentIsKeyword */
        parent $parent
    ) {
        return $this;
    }
}

$instantiated1 = new /* testClassInstantiationParentIsKeyword */ parent();
$instantiated2 = new /* testClassInstantiationSelfIsKeyword */ SELF;

function /* testKeywordSelfAfterFunctionByRefShouldBeString */ &self() {}
function /* testKeywordParentAfterFunctionByRefShouldBeString */ &parent() {}
function /* testKeywordFalseAfterFunctionByRefShouldBeString */ &false() {}
function /* testKeywordTrueAfterFunctionByRefShouldBeString */ & true () {}
function /* testKeywordNullAfterFunctionByRefShouldBeString */ &NULL() {}

/* testKeywordAsFunctionCallNameShouldBeStringSelf */ self();
/* testKeywordAsFunctionCallNameShouldBeStringParent */ parent();
/* testKeywordAsFunctionCallNameShouldBeStringFalse */ false();
/* testKeywordAsFunctionCallNameShouldBeStringTrue */ True ();
/* testKeywordAsFunctionCallNameShouldBeStringNull */ null /*comment*/ ();

$instantiated4 = new /* testClassInstantiationFalseIsString */ False();
$instantiated5 = new /* testClassInstantiationTrueIsString */ true ();
$instantiated6 = new /* testClassInstantiationNullIsString */ null();

function standAloneFalseTrueNullTypesAndMore(
    /* testFalseIsKeywordAsParamType */ false $paramA,
    /* testTrueIsKeywordAsParamType */ true $paramB,
    /* testNullIsKeywordAsParamType */ null $paramC,
): /* testFalseIsKeywordAsReturnType */ false | /* testTrueIsKeywordAsReturnType */ true | /* testNullIsKeywordAsReturnType */ null {
    if ($a === /* testFalseIsKeywordInComparison */ false
    || $a === /* testTrueIsKeywordInComparison */ true
    || $a === /* testNullIsKeywordInComparison */ null
    ) {}
}

class TypedConstProp {
    const /* testFalseIsKeywordAsConstType */ false /* testFalseIsNameForTypedConstant */ FALSE = /* testFalseIsKeywordAsConstDefault */ false;
    const /* testTrueIsKeywordAsConstType */ true /* testTrueIsNameForTypedConstant */ TRUE = /* testTrueIsKeywordAsConstDefault */ true;
    const /* testNullIsKeywordAsConstType */ null /* testNullIsNameForTypedConstant */ NULL = /* testNullIsKeywordAsConstDefault */ null;
    const /* testSelfIsKeywordAsConstType */ self /* testSelfIsNameForTypedConstant */ SELF = new /* testSelfIsKeywordAsConstDefault */ self;
    const /* testParentIsKeywordAsConstType */ parent /* testParentIsNameForTypedConstant */ PARENT = new /* testParentIsKeywordAsConstDefault */ parent;

    public /* testFalseIsKeywordAsPropertyType */ false $false = /* testFalseIsKeywordAsPropertyDefault */ false;
    protected readonly /* testTrueIsKeywordAsPropertyType */ true $true = /* testTrueIsKeywordAsPropertyDefault */ true;
    static private /* testNullIsKeywordAsPropertyType */ null $null = /* testNullIsKeywordAsPropertyDefault */ null;
    var /* testSelfIsKeywordAsPropertyType */ self $self = new /* testSelfIsKeywordAsPropertyDefault */ self;
    protected /* testParentIsKeywordAsPropertyType */ parent $parent = new /* testParentIsKeywordAsPropertyDefault */ parent;
}

class UseInUnionTypes {
    /* testFalseIsKeywordAsConstUnionTypeFirst */
    const false|Foo UNION_WITH_FALSE_FIRST = SOMETHING;
    /* testTrueIsKeywordAsConstUnionTypeFirst */
    const true|string UNION_WITH_TRUE_FIRST = SOMETHING;
    /* testNullIsKeywordAsConstUnionTypeFirst */
    const null|int UNION_WITH_NULL_FIRST = SOMETHING;
    /* testSelfIsKeywordAsConstUnionTypeFirst */
    const self|Foo UNION_WITH_SELF_FIRST = SOMETHING;
    /* testParentIsKeywordAsConstUnionTypeFirst */
    const parent|Bar UNION_WITH_PARENT_FIRST = SOMETHING;

    const int|/* testFalseIsKeywordAsConstUnionTypeMiddle */false|string UNION_WITH_FALSE_MIDDLE = SOMETHING;
    const bool|/* testTrueIsKeywordAsConstUnionTypeMiddle */ true|Stringable UNION_WITH_TRUE_MIDDLE = SOMETHING;
    const object|/* testNullIsKeywordAsConstUnionTypeMiddle */null|iterable UNION_WITH_NULL_MIDDLE = SOMETHING;
    const array | /* testSelfIsKeywordAsConstUnionTypeMiddle */ self | /*comment*/  string UNION_WITH_SELF_MIDDLE = SOMETHING;
    const Foo|/* testParentIsKeywordAsConstUnionTypeMiddle */parent|float UNION_WITH_PARENT_MIDDLE = SOMETHING;

    const string|/* testFalseIsKeywordAsConstUnionTypeLast */false UNION_WITH_FALSE_LAST = SOMETHING;
    const float|/* testTrueIsKeywordAsConstUnionTypeLast */true UNION_WITH_TRUE_LAST = SOMETHING;
    const Something|/* testNullIsKeywordAsConstUnionTypeLast */null UNION_WITH_NULL_LAST = SOMETHING;
    const Foo|/* testSelfIsKeywordAsConstUnionTypeLast */self UNION_WITH_SELF_LAST = SOMETHING;
    const Bar|/* testParentIsKeywordAsConstUnionTypeLast */parent UNION_WITH_PARENT_LAST = SOMETHING;

    /* testFalseIsKeywordAsPropertyUnionTypeFirst */
    public false|string $false = SOMETHING;
    /* testTrueIsKeywordAsPropertyUnionTypeFirst */
    protected readonly true|int $true = SOMETHING;
    /* testNullIsKeywordAsPropertyUnionTypeFirst */
    static private null|object $null = SOMETHING;
    /* testSelfIsKeywordAsPropertyUnionTypeFirst */
    var self|iterable $self = SOMETHING;
    /* testParentIsKeywordAsPropertyUnionTypeFirst */
    protected parent|array $parent = SOMETHING;

    public Foo|/* testFalseIsKeywordAsPropertyUnionTypeMiddle */false|Bar $false = SOMETHING;
    protected readonly Foo|/* testTrueIsKeywordAsPropertyUnionTypeMiddle */true|Bar $true = SOMETHING;
    static private Foo/* testNullIsKeywordAsPropertyUnionTypeMiddle */|null|Bar $null = SOMETHING;
    var Foo|/* testSelfIsKeywordAsPropertyUnionTypeMiddle */self|Bar $self = SOMETHING;
    protected Foo/* testParentIsKeywordAsPropertyUnionTypeMiddle */|parent|Bar $parent = SOMETHING;

    public array|/* testFalseIsKeywordAsPropertyUnionTypeLast */false $false = SOMETHING;
    protected readonly string /* testTrueIsKeywordAsPropertyUnionTypeLast */| true $true = SOMETHING;
    static private int|/* testNullIsKeywordAsPropertyUnionTypeLast */null $null = SOMETHING;
    var object|/* testSelfIsKeywordAsPropertyUnionTypeLast */self $self = SOMETHING;
    protected Foo/* testParentIsKeywordAsPropertyUnionTypeLast */|parent $parent = SOMETHING;

    function KeywordsInParamUnionTypeFirst(
        /* testFalseIsKeywordAsParamUnionTypeFirst */ false|Foo $paramA,
        /* testTrueIsKeywordAsParamUnionTypeFirst */ true|string $paramB,
        /* testNullIsKeywordAsParamUnionTypeFirst */ null|Bar $paramC,
        /* testSelfIsKeywordAsParamUnionTypeFirst */ self|float $paramD,
        /* testParentIsKeywordAsParamUnionTypeFirst */ parent|object $paramE,
    ) {}

    function KeywordsInParamUnionTypeMiddle(
        Foo/* testFalseIsKeywordAsParamUnionTypeMiddle */|false|Bar $paramA,
        Foo|/* testTrueIsKeywordAsParamUnionTypeMiddle */ true|Bar $paramB,
        Foo/* testNullIsKeywordAsParamUnionTypeMiddle */ |null|Bar $paramC,
        Foo|/* testSelfIsKeywordAsParamUnionTypeMiddle */ self|Bar $paramD,
        Foo/* testParentIsKeywordAsParamUnionTypeMiddle */ |parent|Bar $paramE
    ) {}

    function KeywordsInParamUnionTypeLast (
        string|/* testFalseIsKeywordAsParamUnionTypeLast */ false $paramA,
        Something/* testTrueIsKeywordAsParamUnionTypeLast */ |true $paramB,
        bool|/* testNullIsKeywordAsParamUnionTypeLast */ null $paramC,
        MeMe/* testSelfIsKeywordAsParamUnionTypeLast */ |self $paramD,
        int|/* testParentIsKeywordAsParamUnionTypeLast */ parent $paramE,
    ) {}

    function FalseIsKeywordInReturnTypeFirst(): /* testFalseIsKeywordAsReturnUnionTypeFirst */ false|int {}
    function TrueIsKeywordInReturnTypeFirst(): /* testTrueIsKeywordAsReturnUnionTypeFirst */ true|bool {}
    function NullIsKeywordInReturnTypeFirst(): /* testNullIsKeywordAsReturnUnionTypeFirst */ null|float {}
    function SelfIsKeywordInReturnTypeFirst(): /* testSelfIsKeywordAsReturnUnionTypeFirst */ self|object {}
    function ParentIsKeywordInReturnTypeFirst(): /* testParentIsKeywordAsReturnUnionTypeFirst */ parent|iterable {}

    function FalseIsKeywordInReturnTypeMiddle(): Foo|/* testFalseIsKeywordAsReturnUnionTypeMiddle */ false|Bar {}
    function TrueIsKeywordInReturnTypeMiddle(): Foo/* testTrueIsKeywordAsReturnUnionTypeMiddle */ |true|Bar {}
    function NullIsKeywordInReturnTypeMiddle(): Foo|/* testNullIsKeywordAsReturnUnionTypeMiddle */null|Bar {}
    function SelfIsKeywordInReturnTypeMiddle(): Foo /* testSelfIsKeywordAsReturnUnionTypeMiddle */ |self|Bar {}
    function ParentIsKeywordInReturnTypeMiddle(): Foo| /* testParentIsKeywordAsReturnUnionTypeMiddle */ parent|Bar {}

    function FalseIsKeywordInReturnTypeLast(): void|/* testFalseIsKeywordAsReturnUnionTypeLast */false {}
    function TrueIsKeywordInReturnTypeLast(): Bar|/* testTrueIsKeywordAsReturnUnionTypeLast */true {}
    function NullIsKeywordInReturnTypeLast(): string/* testNullIsKeywordAsReturnUnionTypeLast */|null {}
    function SelfIsKeywordInReturnTypeLast(): Foo|/* testSelfIsKeywordAsReturnUnionTypeLast */self {}
    function ParentIsKeywordInReturnTypeLast(): bool/* testParentIsKeywordAsReturnUnionTypeLast */|parent {}
}

class UseInIntersectionTypes {
    /* testSelfIsKeywordAsConstIntersectionTypeFirst */
    const self&Foo INTERSECTION_SELF_FIRST = SOMETHING;
    /* testParentIsKeywordAsConstIntersectionTypeFirst */
    const parent&Bar INTERSECTION_PARENT_FIRST = SOMETHING;

    const Foo&/* testSelfIsKeywordAsConstIntersectionTypeMiddle */self&Bar INTERSECTION_SELF_MIDDLE = SOMETHING;
    const Foo/* testParentIsKeywordAsConstIntersectionTypeMiddle */&parent&Bar INTERSECTION_PARENT_MIDDLE = SOMETHING;

    const Foo&/* testSelfIsKeywordAsConstIntersectionTypeLast */self INTERSECTION_SELF_LAST = SOMETHING;
    const Bar/* testParentIsKeywordAsConstIntersectionTypeLast */&parent INTERSECTION_PARENT_LAST = SOMETHING;

    /* testSelfIsKeywordAsPropertyIntersectionTypeFirst */
    var self&Countable $self = SOMETHING;
    /* testParentIsKeywordAsPropertyIntersectionTypeFirst */
    protected parent&DateTime $parent = SOMETHING;

    var Foo/* testSelfIsKeywordAsPropertyIntersectionTypeMiddle */&self&Bar $self = SOMETHING;
    protected Foo&/* testParentIsKeywordAsPropertyIntersectionTypeMiddle */parent&Bar $parent = SOMETHING;

    var Exception/* testSelfIsKeywordAsPropertyIntersectionTypeLast */&self $self = SOMETHING;
    protected Foo&/* testParentIsKeywordAsPropertyIntersectionTypeLast */parent $parent = SOMETHING;

    function KeywordsInParamIntersectionTypeFirst(
        /* testSelfIsKeywordAsParamIntersectionTypeFirst */ self&float $paramD,
        /* testParentIsKeywordAsParamIntersectionTypeFirst */ parent&object $paramE,
    ) {}

    function KeywordsInParamIntersectionTypeMiddle(
        Foo/* testSelfIsKeywordAsParamIntersectionTypeMiddle */ &self&Bar $paramD,
        Foo&/* testParentIsKeywordAsParamIntersectionTypeMiddle */ parent&Bar $paramE
    ) {}

    function KeywordsInParamIntersectionTypeLast (
        MeMe/* testSelfIsKeywordAsParamIntersectionTypeLast */ &self $paramD,
        int&/* testParentIsKeywordAsParamIntersectionTypeLast */ parent $paramE
    ) {}

    function SelfIsKeywordInReturnTypeFirst()/* testSelfIsKeywordAsReturnIntersectionTypeFirst */: self&string {}
    function ParentIsKeywordInReturnTypeFirst():/* testParentIsKeywordAsReturnIntersectionTypeFirst */ parent&iterable {}

    function SelfIsKeywordInReturnTypeMiddle(): Foo/* testSelfIsKeywordAsReturnIntersectionTypeMiddle */&self&Bar {}
    function ParentIsKeywordInReturnTypeMiddle(): Foo&/* testParentIsKeywordAsReturnIntersectionTypeMiddle */parent&Bar {}

    function SelfIsKeywordInReturnTypeLast(): Foo&/* testSelfIsKeywordAsReturnIntersectionTypeLast */self {}
    function ParentIsKeywordInReturnTypeLast(): bool&/* testParentIsKeywordAsReturnIntersectionTypeLast */parent {}
}

// Note: self/parent/static are rarely allowed in DNF types, but that is not the concern of the tokenizer.
class DNFTypes extends Something {
    const (A&B)/* testFalseIsKeywordAsConstDNFType */|false NAME_FALSE = SOME_CONST;
    const /* testTrueIsKeywordAsConstDNFType */ true|(A&B) NAME_TRUE = SOME_CONST;
    const (A&B)|/* testNullIsKeywordAsConstDNFType */ null|(C&D) NAME_NULL = SOME_CONST;
    const /* testSelfIsKeywordAsConstDNFType */ (self&B)|int NAME_SELF = SOME_CONST;
    const bool|(A&/* testParentIsKeywordAsConstDNFType */ parent) NAME_PARENT = SOME_CONST;

    readonly public (A&B)/* testFalseIsKeywordAsPropertyDNFType */ |false $false;
    protected /* testTrueIsKeywordAsPropertyDNFType */ true|(A&B) $true = SOME_CONST;
    static private (A&B)|/* testNullIsKeywordAsPropertyDNFType */ null|(C&D) $null = SOME_CONST;
    var string|/* testSelfIsKeywordAsPropertyDNFType */ (self&Stringable) $self = SOME_CONST;
    protected (A/* testParentIsKeywordAsPropertyDNFType */ &parent)|float $parent = SOME_CONST;

    public function DNFWithFalse(
        /* testFalseIsKeywordAsParamDNFType */
        false|(A&B) $param
    ) : (A&B)/* testFalseIsKeywordAsReturnDNFType */|false {

        $closure = static function (
            array/* testTrueIsKeywordAsParamDNFType */|true|(A&B) $param
        ) : (A&B)/* testTrueIsKeywordAsReturnDNFType */|true {};
    }


    public function DNFWithNull(
        /* testNullIsKeywordAsParamDNFType */
        null|(A&B) $param
    ) : (A&B)/* testNullIsKeywordAsReturnDNFType */|null|(C&D) {}

    public function DNFWithSelf(
        /* testSelfIsKeywordAsParamDNFType */
        (self&B)|mixed $param
    ) : (A&B)/* testSelfIsKeywordAsReturnDNFType */|self {

        $arrow = fn (
            array|(Iterable /* testParentIsKeywordAsParamDNFType */&parent) $param
        /* testParentIsKeywordAsReturnDNFType */
        ) : parent|(A&B) => $param->get();
    }
}
