<?php

/*
 * Type intersection or bitwise and.
 */

/* testBitwiseAnd1 */
$result = $value & $test /* testBitwiseAnd2 */ & $another;

abstract class TypeIntersection
{
    /* testTypeIntersectionOOConstSimple */
    public const Foo&Bar SIMPLE = new Foo();

    /* testTypeIntersectionOOConstReverseModifierOrder */
    protected final const Something&Nothing MODIFIERS_REVERSED /* testBitwiseAndOOConstDefaultValue */ = E_WARNING & E_NOTICE;

    const
        /* testTypeIntersectionOOConstMulti1 */
        Foo &
        /* testTypeIntersectionOOConstMulti2 */
        Traversable & // phpcs:ignore Stnd.Cat.Sniff
        Boo
        /* testTypeIntersectionOOConstMulti3 */
        & Bar MULTI_INTERSECTION = false;

    /* testTypeIntersectionOOConstNamespaceRelative */
    const namespace\Sub\NameA&namespace\Sub\NameB NAMESPACE_RELATIVE = new namespace\Sub\NameB;

    /* testTypeIntersectionOOConstPartiallyQualified */
    const Partially\Qualified\NameA&Partially\Qualified\NameB PARTIALLY_QUALIFIED = new Partially\Qualified\NameA;

    /* testTypeIntersectionOOConstFullyQualified */
    const \Fully\Qualified\NameA&\Fully\Qualified\NameB FULLY_QUALIFIED = new \Fully\Qualified\NameB();

    /* testTypeIntersectionPropertySimple */
    public static Foo&Bar $obj;

    /* testTypeIntersectionPropertyReverseModifierOrder */
    static protected Something&Nothing $somethingNothing /* testBitwiseAndPropertyDefaultValue */ = E_WARNING & E_NOTICE;

    private
        /* testTypeIntersectionPropertyMulti1 */
        Foo &
        /* testTypeIntersectionPropertyMulti2 */
        Traversable & // phpcs:ignore Stnd.Cat.Sniff
        Boo
        /* testTypeIntersectionPropertyMulti3 */
        & Bar $multi;

    /* testTypeIntersectionPropertyNamespaceRelative */
    public namespace\Sub\NameA&namespace\Sub\NameB $namespaceRelative;

    /* testTypeIntersectionPropertyPartiallyQualified */
    public Partially\Qualified\NameA&Partially\Qualified\NameB $partiallyQual;

    /* testTypeIntersectionPropertyFullyQualified */
    public \Fully\Qualified\NameA&\Fully\Qualified\NameB $fullyQual;

    /* testTypeIntersectionPropertyWithReadOnlyKeyword */
    protected readonly Foo&Bar $fooBar;

    /* testTypeIntersectionPropertyWithStaticKeyword */
    static Foo&Bar $obj;

    /* testTypeIntersectionWithPHP84FinalKeyword */
    final className&InterfaceName $finalKeywordA;

    /* testTypeIntersectionWithPHP84FinalKeywordFirst */
    final private \className&InterfaceName $finalKeywordB;

    /* testTypeIntersectionPropertyWithPrivateSet */
    private(set) Foo&Bar $asym1;

    /* testTypeIntersectionPropertyWithPublicPrivateSet */
    public private(set) Foo&Bar $asym2;

    /* testTypeIntersectionPropertyWithProtectedSet */
    protected(set) Foo&Bar $asym3;

    /* testTypeIntersectionPropertyWithPublicProtectedSet */
    public protected(set) Foo&Bar $asym4;

    /* testTypeIntersectionWithPHP84AbstractKeyword */
    abstract className&InterfaceName $abstractKeywordA { get; }

    /* testTypeIntersectionWithPHP84AbstractKeywordFirst */
    abstract protected \className&InterfaceName $abstractKeywordB { set; }

    public function paramTypes(
        /* testTypeIntersectionParam1 */
        Foo&Bar $paramA /* testBitwiseAndParamDefaultValue */ = CONSTANT_A & CONSTANT_B,

        /* testTypeIntersectionParam2 */
        Foo&\Bar /* testTypeIntersectionParam3 */ &Baz /* testBitwiseAnd3 */ &...$paramB = null,
    ) {
        /* testBitwiseAnd4 */
        return (($a1 ^ $b1) &($a2 ^ $b2)) + $c;
    }

    public function identifierNames(
        /* testTypeIntersectionParamNamespaceRelative */
        namespace\Sub\NameA&namespace\Sub\NameB $paramA,
        /* testTypeIntersectionParamPartiallyQualified */
        Partially\Qualified\NameA&Partially\Qualified\NameB $paramB,
        /* testTypeIntersectionParamFullyQualified */
        \Fully\Qualified\NameA&\Fully\Qualified\NameB $paramC,
    ) {}

    /* testTypeIntersectionConstructorPropertyPromotion */
    public function __construct( public Foo&Bar $property) {}

    /* testTypeIntersectionReturnType */
    public function returnType() : Foo&Bar {}

    /* testTypeIntersectionAbstractMethodReturnType1 */
    abstract public function abstractMethod(): Foo&Bar /* testTypeIntersectionAbstractMethodReturnType2 */ &Baz;

    /* testTypeIntersectionReturnTypeNamespaceRelative */
    public function identifierNamesReturnRelative() : namespace\Sub\NameA&namespace\Sub\NameB {}

    /* testTypeIntersectionReturnPartiallyQualified */
    public function identifierNamesReturnPQ() : Partially\Qualified\NameA&Partially\Qualified\NameB {}

    /* testTypeIntersectionReturnFullyQualified */
    public function identifierNamesReturnFQ() : \Fully\Qualified\NameA&\Fully\Qualified\NameB {}
}

function globalFunctionWithSpreadAndReference(
    /* testTypeIntersectionWithReference */
    Foo&Bar /* testBitwiseAnd5 */ &$paramA,
    /* testTypeIntersectionWithSpreadOperator */
    Foo&Bar ...$paramB
) {}


$dnfTypes = new class {
    /* testTypeIntersectionConstantTypeUnionBeforeDNF */
    const Foo|(A&B) UNION_BEFORE = /* testBitwiseAndOOConstDefaultValueDNF */ Foo|(A&B);

    /* testTypeIntersectionPropertyTypeUnionAfterDNF */
    protected (\FQN&namespace\Relative)|Partially\Qualified $union_after = /* testBitwiseAndPropertyDefaultValueDNF */ (A&B)|Foo;

    public function unionBeforeAndAfter(
        /* testTypeIntersectionParamUnionBeforeAndAfterDNF */
        string|(Stringable&\Countable)|int $param = /* testBitwiseAndParamDefaultValueDNF */ ( CONST_A & CONST_B) | CONST_C
    ): /* testTypeIntersectionReturnTypeUnionAfterDNF */ (A&B)|null {}
};

/* testTypeIntersectionClosureParamIllegalNullable */
$closureWithParamType = function (?Foo&Bar $string) {};

/* testBitwiseAndClosureParamDefault */
$closureWithReturnType = function ($string = NONSENSE & FAKE)/* testTypeIntersectionClosureReturn */ : \Package\MyA&PackageB {};

/* testTypeIntersectionArrowParam */
$arrowWithParamType = fn (Foo&Bar $param, /* testBitwiseAndArrowParamDefault */ ?int $int = CONSTA & CONSTB )
    /* testBitwiseAndArrowExpression */
    => $param & $int;

/* testTypeIntersectionArrowReturnType */
$arrowWithReturnType = fn ($param) : Foo&Bar => $param * 10;

/* testBitwiseAndInArrayKey */
$array = array(
    A & B => /* testBitwiseAndInArrayValue */ B & C
);

/* testBitwiseAndInShortArrayKey */
$array = [
    A & B => /* testBitwiseAndInShortArrayValue */ B & C
];

/* testBitwiseAndNonArrowFnFunctionCall */
$obj->fn($something & $else);

/* testBitwiseAnd6 */
function &fn(/* testTypeIntersectionNonArrowFunctionDeclaration */ Foo&Bar $something) {}

/* testTypeIntersectionWithInvalidTypes */
function (int&string $var) {};

/* testLiveCoding */
// Intentional parse error. This has to be the last test in the file.
return function( Foo&
