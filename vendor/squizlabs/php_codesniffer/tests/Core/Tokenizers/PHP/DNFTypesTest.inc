<?php

/*
 * Non-exhaustive code samples of "normal" parentheses.
 */

/* testParensNoOwner */
$a = ( CONST_A & CONST_B ) | CONST_C === false;

/* testParensNoOwnerInTernary */
$a = $var ? $something : CONST_C | ( CONST_A & CONST_B );

/* testParensNoOwnerInShortTernary */
$a = $var ?: ( CONST_A & CONST_B );

/* testFnCallParensNoOwnerInTernaryA */
$var1 ? \X\call8() : /* testFnCallParensNoOwnerInTernaryB */ \Y\call9();

/* testPFnCallarensNoOwnerInShortTernary */
$var2 ?: \Z\call10();

/* testParensOwnerFunctionAmpersandInDefaultValue */
function defaultValueLooksLikeDNF( mixed $param = (CONST_A&CONST_B) ) {}

/* testParensOwnerClosureAmpersandParamRef */
$closureWithParamRef = function(&$param) {};

/* testParensOwnerIf */
if ( /* testParensNoOwnerInIfCondition */ CONST_C | ( CONST_A & /*comment*/ CONST_B ) > 10 ) {}

/* testParensOwnerFor */
for ($i =0; $i < /* testParensNoOwnerInForCondition */ ( CONST_A & CONST_B ); $i++ );

/* testParensOwnerMatch */
$match = match(CONST_A & CONST_B) {
    default => $a,
};

/* testParensOwnerArray */
$array = array (
    'text',
    \CONST_A & \Fully\Qualified\CONST_B,
    /* testParensNoOwnerFunctionCallWithAmpersandInCallable */
    do_something($a, /* testParensOwnerArrowFn */ fn($b) => $a & $b, $c),
);

/* testParensOwnerListWithRefVars */
list(&$a, &$b) = $array;

/* testParensNoOwnerFunctionCallwithDNFLookALikeParam */
$obj->static((CONST_A&CONST_B)|CONST_C | $var);

/* testParensNoOwnerFunctionCallWithDNFLookALikeNamedParamPlain */
callMe(label: false);

/* testParensNoOwnerFunctionCallWithDNFLookALikeNamedParamUnion */
callMe(label: CONST_A | CONST_B);

/* testParensNoOwnerFunctionCallWithDNFLookALikeNamedParamIntersect */
callMe(label: CONST_A & CONST_B);

\Z1\call11(
    /* testParensNoOwnerFunctionCallInNamedParam */
    param1: \Z2\call12(),
    /* testParensOwnerArrowFunctionInNamedParam */
    param2: fn (): /* testDNFTypeArrowFnReturnInNamedParam */ int|(\Countable&\Iterable)
        /* testParensNoOwnerFunctionCallInArrowFnReturn */
        => \Z3\call13(),
    /* testParensOwnerClosureInNamedParam */
    param3: function (): /* testDNFTypeClosureReturnInNamedParam */ int|(\DateTime&\ArrayObject) {
        /* testParensNoOwnerFunctionCallInClosureReturn */
        return \Z4\call14();
    },
);

/* testSwitchControlStructureCondition */
switch (CONST_A | CONST_B) {
    /* testFunctionCallInSwitchCaseCondition */
    case get_bool():
        /* testFunctionCallInSwitchCaseBody */
        \Name\functionInSwitch();
        break;

    default:
        /* testFunctionCallInSwitchDefaultBody */
        functionInSwitch();
        break;
}

/* testIfAlternativeSyntaxCondition */
if (true):
    /* testFunctionCallInIfBody */
    \B\call();
/* testElseIfAlternativeSyntaxCondition */
elseif (10):
    /* testFunctionCallInElseIfBody */
    C\call();
else:
    /* testFunctionCallInElseBody */
    \C\call3();
endif;

gotolabel:
    /* testFunctionCallInGotoBody */
    \doSomething();

/* testWhileAlternativeSyntaxCondition */
while ($c3):
    /* testFunctionCallInWhileBody */
    \D\call4();
endwhile;

/* testForAlternativeSyntaxCondition */
for ($i = 0; $i < 10; $i++):
    /* testFunctionCallInForBody */
    \F\call5();
endfor;

/* testForEachAlternativeSyntaxCondition */
foreach ($array as $key => $value):
    /* testFunctionCallInForeachBody */
    \G\call6();
endforeach;

/*
 * DNF parentheses.
 */

abstract class DNFTypes {
    /* testDNFTypeOOConstUnqualifiedClasses */
    public const (A&B)|D UNQUALIFIED = new Foo;

    /* testDNFTypeOOConstReverseModifierOrder */
    protected final const int|(Foo&Bar)|float MODIFIERS_REVERSED /* testParensNoOwnerOOConstDefaultValue */ = (E_WARNING & E_NOTICE) | E_DEPRECATED;

    const
        /* testDNFTypeOOConstMulti1 */
        (A&B) |
        /* testDNFTypeOOConstMulti2 */
        (C&D) | // phpcs:ignore Stnd.Cat.Sniff
        /* testDNFTypeOOConstMulti3 */
        (Y&D)
        | null MULTI_DNF = null;

    /* testDNFTypeOOConstNamespaceRelative */
    final protected const (namespace\Sub\NameA&namespace\Sub\NameB)|namespace\Sub\NameC NAMESPACE_RELATIVE = new namespace\Sub\NameB;

    /* testDNFTypeOOConstPartiallyQualified */
    const Partially\Qualified\NameC|(Partially\Qualified\NameA&Partially\Qualified\NameB) PARTIALLY_QUALIFIED = new Partially\Qualified\NameA;

    /* testDNFTypeOOConstFullyQualified */
    const (\Fully\Qualified\NameA&\Fully\Qualified\NameB)|\Fully\Qualified\NameC FULLY_QUALIFIED = new \Fully\Qualified\NameB();

    /* testDNFTypePropertyUnqualifiedClasses */
    public static (Foo&Bar)|array $obj;

    /* testDNFTypePropertyReverseModifierOrder */
    static protected string|(A&B)|int $dnf /* testParensNoOwnerPropertyDefaultValue1 */ = ( E_WARNING & E_NOTICE ) | /* testParensNoOwnerPropertyDefaultValue2 */ (E_ALL & E_DEPRECATED);

    private
        /* testDNFTypePropertyMultiNamespaceRelative */
        (namespace\Sub\NameA&namespace\Sub\NameB) |
        /* testDNFTypePropertyMultiPartiallyQualified */
        (Partially\Qualified\NameA&Partially\Qualified\NameB) | // phpcs:ignore Stnd.Cat.Sniff
        false
        /* testDNFTypePropertyMultiFullyQualified */
        | (\Fully\Qualified\NameA&\Fully\Qualified\NameB) $multiDnf;

    /* testDNFTypePropertyWithReadOnlyKeyword1 */
    protected readonly (A&B) | /* testDNFTypePropertyWithReadOnlyKeyword2 */ (C&D) $readonly;

    /* testDNFTypePropertyWithStaticAndReadOnlyKeywords */
    static readonly (A&B&C)|array $staticReadonly;

    /* testDNFTypePropertyWithOnlyStaticKeyword */
    static (A&B&C)|true $onlyStaticModified;

    /* testDNFTypeWithPHP84FinalKeyword */
    final (className&InterfaceName)|false $finalKeywordA;

    /* testDNFTypeWithPHP84FinalKeywordAndStatic */
    final static (\className&\InterfaceName)|false $finalKeywordB;

    /* testDNFTypePropertyWithPrivateSet */
    private(set) (A&B&C)|true $asym1;

    /* testDNFTypePropertyWithPublicPrivateSet */
    public private(set) (A&B&C)|true $asym2;

    /* testDNFTypePropertyWithProtectedSet */
    protected(set) (A&B&C)|true $asym3;

    /* testDNFTypePropertyWithPublicProtectedSet */
    public protected(set) (A&B&C)|true $asym4;

    /* testDNFTypeWithPHP84AbstractKeyword */
    abstract (className&InterfaceName)|false $abstractKeywordA { set; }

    /* testDNFTypeWithPHP84AbstractKeywordAndPublic */
    abstract public (\className&\InterfaceName)|false $abstractKeywordB { get; }

    public function paramTypes(
        /* testDNFTypeParam1WithAttribute */
        #[MyAttribute]
        (\Foo&Bar)|int|float $paramA /* testParensNoOwnerParamDefaultValue */ = SOMETHING | (CONSTANT_A & CONSTANT_B),

        /* testDNFTypeParam2 */
        (Foo&\Bar) /* testDNFTypeParam3 */ |(Baz&Fop) &...$paramB,
    ) {
        /* testParensNoOwnerInReturnValue1 */
        return (
            /* testParensNoOwnerInReturnValue2 */
            ($a1 & $b1) |
            /* testParensNoOwnerInReturnValue3 */
            ($a2 & $b2)
        ) + $c;
    }

    public function identifierNames(
        /* testDNFTypeParamNamespaceRelative */
        (namespace\Sub\NameA&namespace\Sub\NameB)|false $paramA,
        /* testDNFTypeParamPartiallyQualified */
        Partially\Qualified\NameC|(Partially\Qualified\NameA&Partially\Qualified\NameB) $paramB,
        /* testDNFTypeParamFullyQualified */
        name|(\Fully\Qualified\NameA&\Fully\Qualified\NameB) $paramC,
    ) {}

    public function __construct(
        /* testDNFTypeConstructorPropertyPromotion1 */
        public (A&B)| /* testDNFTypeConstructorPropertyPromotion2 */ (A&D) $property
    ) {}

    public function returnType()/* testDNFTypeReturnType1 */ : A|(B&D)|/* testDNFTypeReturnType2 */(B&W)|null {}

    abstract public function abstractMethod(): /* testDNFTypeAbstractMethodReturnType1 */ (X&Y) /* testDNFTypeAbstractMethodReturnType2 */ |(W&Z);

    public function identifierNamesReturnRelative(
    ) : /* testDNFTypeReturnTypeNamespaceRelative */ (namespace\Sub\NameA&namespace\Sub\NameB)|namespace\Sub\NameC {}

    public function identifierNamesReturnPQ(
    ) : /* testDNFTypeReturnPartiallyQualified */Partially\Qualified\NameA|(Partially\Qualified\NameB&Partially\Qualified\NameC) {}

    // Illegal type: segments which are strict subsets of others are disallowed, but that's not the concern of the tokenizer.
    public function identifierNamesReturnFQ(
    ) /* testDNFTypeReturnFullyQualified */ : (\Fully\Qualified\NameA&\Fully\Qualified\NameB)|\Fully\Qualified\NameB {}
}

function globalFunctionWithSpreadAndReference(
    /* testDNFTypeWithReference */
    float|(B&A) &$paramA,
    /* testDNFTypeWithSpreadOperator */
    string|(B&D) ...$paramB
) {}


$closureWithParamType = function ( /* testDNFTypeClosureParamIllegalNullable */ ?(A&B)|bool $string) {};

/* testParensOwnerClosureAmpersandInDefaultValue */
$closureWithReturnType = function ($string = NONSENSE & FAKE) /* testDNFTypeClosureReturn */ : (\Package\MyA&PackageB)|null {};

$closureWithUseAndReturnType = function ($foo) use ($a) /* testDNFTypeClosureWithUseReturn */ : null|(Foo&\Bar)|false {};

/* testParensOwnerArrowDNFUsedWithin */
$arrowWithParamType = fn (
    /* testDNFTypeArrowParam */
    int|(A&B&C)|array $param,
    /* testParensNoOwnerAmpersandInDefaultValue */ ?int $int = (CONSTA & CONSTB )| CONST_C
)
    /* testParensNoOwnerInArrowReturnExpression */
    => ($param & $foo ) | $int;

$arrowWithReturnType = fn ($param) : /* testDNFTypeArrowReturnType */ int|(A&B) => $param * 10;

$arrowWithParamReturnByRef = fn &(
    /* testDNFTypeArrowParamWithReturnByRef */
    (A&B)|null $param
) => $param * 10;

function InvalidSyntaxes(
    /* testDNFTypeParamIllegalUnnecessaryParens */
    (A&B) $parensNotNeeded,

    /* testDNFTypeParamIllegalIntersectUnionReversed */
    A&(B|D) $onlyIntersectAllowedWithinParensAndUnionOutside,

    /* testDNFTypeParamIllegalNestedParens */
    A|(B&(D|W)|null) $nestedParensNotAllowed,
) {}
