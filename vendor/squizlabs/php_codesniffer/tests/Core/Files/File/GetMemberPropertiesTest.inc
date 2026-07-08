<?php

class TestMemberProperties
{
    /* testVar */
    var $varA = true;

    /* testVarType */
    var ?int $varA = true;

    /* testPublic */
    public $varB = true;

    /* testPublicType */
    public string $varB = true;

    /* testProtected */
    protected $varC = true;

    /* testProtectedType */
    protected bool $varC = true;

    /* testPrivate */
    private $varD = true;

    /* testPrivateType */
    private array $varD = true;

    /* testStatic */
    static $varE = true;

    /* testStaticType */
    static ?string $varE = true;

    /* testStaticVar */
    static var $varF = true;

    /* testVarStatic */
    var static $varG = true;

    /* testPublicStatic */
    public // comment
    // phpcs:ignore Stnd.Cat.Sniff -- For reasons.
    static
    $varH = true;

    /* testProtectedStatic */
    static protected $varI = true;

    /* testPrivateStatic */
    private static $varJ = true;

    /* testNoPrefix */
    $varK = true;

    /* testPublicStaticWithDocblock */
    /**
     * Comment here.
     *
     * @phpcs:ignore Standard.Category.Sniff -- because
     * @var boolean
     */
    public static $varH = true;

    /* testProtectedStaticWithDocblock */
    /**
     * Comment here.
     *
     * @phpcs:ignore Standard.Category.Sniff -- because
     * @var boolean
     */
    static protected $varI = true;

    /* testPrivateStaticWithDocblock */
    /**
     * Comment here.
     *
     * @phpcs:ignore Standard.Category.Sniff -- because
     * @var boolean
     */
    private static $varJ = true;

    public float
    /* testGroupType 1 */
    $x,
    /* testGroupType 2 */
    $y;

    public static ?string
    /* testGroupNullableType 1 */
    $x = null,
    /* testGroupNullableType 2 */
    $y = null;

    protected static
        /* testGroupProtectedStatic 1 */
        $varL,
        /* testGroupProtectedStatic 2 */
        $varM,
        /* testGroupProtectedStatic 3 */
        $varN;

    private
        /* testGroupPrivate 1 */
        $varO = true,
        /* testGroupPrivate 2 */
        $varP = array( 'a' => 'a', 'b' => 'b' ),
        /* testGroupPrivate 3 */
        $varQ = 'string',
        /* testGroupPrivate 4 */
        $varR = 123,
        /* testGroupPrivate 5 */
        $varS = ONE / self::THREE,
        /* testGroupPrivate 6 */
        $varT = [
            'a' => 'a',
            'b' => 'b'
        ],
        /* testGroupPrivate 7 */
        $varU = __DIR__ . "/base";


    /* testMethodParam */
    public function methodName($param) {
        /* testImportedGlobal */
        global $importedGlobal = true;

        /* testLocalVariable */
        $localVariable = true;
    }

    /* testPropertyAfterMethod */
    private static $varV = true;

    /* testMessyNullableType */
    public /* comment
         */ ? //comment
        array $foo = [];

    /* testNamespaceType */
    public \MyNamespace\MyClass $foo;

    /* testNullableNamespaceType 1 */
    private ?ClassName $nullableClassType;

    /* testNullableNamespaceType 2 */
    protected ?Folder\ClassName $nullableClassType2;

    /* testMultilineNamespaceType */
    public \MyNamespace /** comment *\/ comment */
           \MyClass /* comment */
           \Foo $foo;

}

interface Base
{
    /* testInterfaceProperty */
    protected $anonymous;
}

/* testGlobalVariable */
$globalVariable = true;

/* testNotAVariable */
return;

$a = ( $foo == $bar ? new stdClass() :
    new class() {
        /* testNestedProperty 1 */
        public $var = true;

        /* testNestedMethodParam 1 */
        public function something($var = false) {}
    }
);

function_call( 'param', new class {
    /* testNestedProperty 2 */
    public $year = 2017;

    /* testNestedMethodParam 2 */
    public function __construct( $open, $post_id ) {}
}, 10, 2 );

class PHP8Mixed {
    /* testPHP8MixedTypeHint */
    public static miXed $mixed;

    /* testPHP8MixedTypeHintNullable */
    // Intentional fatal error - nullability is not allowed with mixed, but that's not the concern of the method.
    private ?mixed $nullableMixed;
}

class NSOperatorInType {
    /* testNamespaceOperatorTypeHint */
    public ?namespace\Name $prop;
}

$anon = class() {
    /* testPHP8UnionTypesSimple */
    public int|float $unionTypeSimple;

    /* testPHP8UnionTypesTwoClasses */
    private MyClassA|\Package\MyClassB $unionTypesTwoClasses;

    /* testPHP8UnionTypesAllBaseTypes */
    protected array|bool|int|float|NULL|object|string $unionTypesAllBaseTypes;

    /* testPHP8UnionTypesAllPseudoTypes */
    // Intentional fatal error - mixing types which cannot be combined, but that's not the concern of the method.
    var false|mixed|self|parent|iterable|Resource $unionTypesAllPseudoTypes;

    /* testPHP8UnionTypesIllegalTypes */
    // Intentional fatal error - types which are not allowed for properties, but that's not the concern of the method.
    // Note: static is also not allowed as a type, but using static for a property type is not supported by the tokenizer.
    public callable|void $unionTypesIllegalTypes;

    /* testPHP8UnionTypesNullable */
    // Intentional fatal error - nullability is not allowed with union types, but that's not the concern of the method.
    public ?int|float $unionTypesNullable;

    /* testPHP8PseudoTypeNull */
    // PHP 8.0 - 8.1: Intentional fatal error - null pseudotype is only allowed in union types, but that's not the concern of the method.
    public null $pseudoTypeNull;

    /* testPHP8PseudoTypeFalse */
    // PHP 8.0 - 8.1: Intentional fatal error - false pseudotype is only allowed in union types, but that's not the concern of the method.
    public false $pseudoTypeFalse;

    /* testPHP8PseudoTypeFalseAndBool */
    // Intentional fatal error - false pseudotype is not allowed in combination with bool, but that's not the concern of the method.
    public bool|FALSE $pseudoTypeFalseAndBool;

    /* testPHP8ObjectAndClass */
    // Intentional fatal error - object is not allowed in combination with class name, but that's not the concern of the method.
    public object|ClassName $objectAndClass;

    /* testPHP8PseudoTypeIterableAndArray */
    // Intentional fatal error - iterable pseudotype is not allowed in combination with array or Traversable, but that's not the concern of the method.
    public iterable|array|Traversable $pseudoTypeIterableAndArray;

    /* testPHP8DuplicateTypeInUnionWhitespaceAndComment */
    // Intentional fatal error - duplicate types are not allowed in union types, but that's not the concern of the method.
    public int |string| /*comment*/ INT $duplicateTypeInUnion;

    /* testPHP81Readonly */
    public readonly int $readonly;

    /* testPHP81ReadonlyWithNullableType */
    public readonly ?array $readonlyWithNullableType;

    /* testPHP81ReadonlyWithUnionType */
    public readonly string|int $readonlyWithUnionType;

    /* testPHP81ReadonlyWithUnionTypeWithNull */
    protected ReadOnly string|null $readonlyWithUnionTypeWithNull;

    /* testPHP81OnlyReadonlyWithUnionType */
    readonly string|int $onlyReadonly;

    /* testPHP81OnlyReadonlyWithUnionTypeMultiple */
    readonly \InterfaceA|\Sub\InterfaceB|false
        $onlyReadonly;

    /* testPHP81ReadonlyAndStatic */
    readonly private static ?string $readonlyAndStatic;

    /* testPHP81ReadonlyMixedCase */
    public ReadONLY static $readonlyMixedCase;
};

$anon = class {
    /* testPHP8PropertySingleAttribute */
    #[PropertyWithAttribute]
    public string $foo;

    /* testPHP8PropertyMultipleAttributes */
    #[PropertyWithAttribute(foo: 'bar'), MyAttribute]
    protected ?int|float $bar;

    /* testPHP8PropertyMultilineAttribute */
    #[
        PropertyWithAttribute(/* comment */ 'baz')
    ]
    private mixed $baz;
};

enum Suit
{
    /* testEnumProperty */
    protected $anonymous;
}

enum Direction implements ArrayAccess
{
    case Up;
    case Down;

    /* testEnumMethodParamNotProperty */
    public function offsetGet($val) { ... }
}

$anon = class() {
    /* testPHP81IntersectionTypes */
    public Foo&Bar $intersectionType;

    /* testPHP81MoreIntersectionTypes */
    public Foo&Bar&Baz $moreIntersectionTypes;

    /* testPHP81IllegalIntersectionTypes */
    // Intentional fatal error - types which are not allowed for intersection type, but that's not the concern of the method.
    public int&string $illegalIntersectionType;

    /* testPHP81NullableIntersectionType */
    // Intentional fatal error - nullability is not allowed with intersection type, but that's not the concern of the method.
    public ?Foo&Bar $nullableIntersectionType;
};

$anon = class() {
    /* testPHP82PseudoTypeTrue */
    public true $pseudoTypeTrue;

    /* testPHP82NullablePseudoTypeTrue */
    static protected ?true $pseudoTypeNullableTrue;

    /* testPHP82PseudoTypeTrueInUnion */
    private int|string|true $pseudoTypeTrueInUnion;

    /* testPHP82PseudoTypeFalseAndTrue */
    // Intentional fatal error - Type contains both true and false, bool should be used instead, but that's not the concern of the method.
    readonly true|FALSE $pseudoTypeFalseAndTrue;
};

class WhitespaceAndCommentsInTypes {
    /* testUnionTypeWithWhitespaceAndComment */
    public int | /*comment*/ string $hasWhitespaceAndComment;

    /* testIntersectionTypeWithWhitespaceAndComment */
    public \Foo /*comment*/ & Bar $hasWhitespaceAndComment;
}

trait DNFTypes {
    /* testPHP82DNFTypeStatic */
    public static (Foo&\Bar)|bool $propA;

    /* testPHP82DNFTypeReadonlyA */
    protected readonly float|(Partially\Qualified&Traversable) $propB;

    /* testPHP82DNFTypeReadonlyB */
    private readonly (namespace\Foo&Bar)|string $propC;

    /* testPHP82DNFTypeIllegalNullable */
    // Intentional fatal error - nullable operator cannot be combined with DNF.
    var ?(A&\Pck\B)|bool $propD;
}

class WithFinalProperties {
    /* testPHP84FinalPublicTypedProp */
    final public string $val1;
    /* testPHP84FinalProtectedTypedProp */
    final protected string $val2;
    /* testPHP84FinalMiddleTypedProp */
    public final string $val3;
    /* testPHP84FinalMiddleStaticTypedProp */
    public final static string $val4;
    /* testPHP84FinalLastTypedProp */
    public readonly final string $val5;
    /* testPHP84FinalImplicitVisibilityTypedProp */
    final string $val6;
    /* testPHP84FinalImplicitVisibilityProp */
    final $val7;
    /* testPHP84FinalNullableTypedProp */
    final public ?string $val8;
    /* testPHP84FinalComplexTypedProp */
    final public (Foo&\Bar)|bool $val9;
}

class AsymVisibility {
    /* testPHP84AsymPublicSetProperty */
    public(set) mixed $prop1;
    /* testPHP84AsymPublicPublicSetProperty */
    public public(set) (A&B)|null $prop2;
    /* testPHP84AsymPublicSetPublicProperty */
    public(set) public bool $prop3;

    /* testPHP84AsymProtectedSetProperty */
    protected(set) readonly mixed $prop4;
    /* testPHP84AsymPublicProtectedSetProperty */
    public protected(set) string $prop5;
    /* testPHP84AsymProtectedSetPublicProperty */
    protected(set) public ?float $prop6;

    /* testPHP84AsymPrivateSetProperty */
    private(set) string|int $prop7;
    /* testPHP84AsymProtectedPrivateSetProperty */
    final protected private(set) $prop8;
    /* testPHP84AsymPrivateSetPublicProperty */
    private(set) public mixed $prop9;

    /* testPHP84IllegalAsymPublicProtectedSetStaticProperty */
    public protected(set) static mixed $prop10;
}

abstract class WithAbstractProperties {
    /* testPHP84AbstractPublicTypedProp */
    abstract public string $val1 { get; }
    /* testPHP84AbstractProtectedTypedProp */
    abstract protected Union|Type $val2 { set; }
    /* testPHP84AbstractMiddleTypedProp */
    public abstract Intersection&Type $val3 { get; }
    /* testPHP84AbstractImplicitVisibilityTypedProp */
    abstract int $val4 { set; }
    /* testPHP84AbstractImplicitVisibilityProp */
    abstract $val5 { get; }
    /* testPHP84AbstractNullableTypedProp */
    abstract public ?string $val6 { set; }
    /* testPHP84AbstractComplexTypedProp */
    abstract protected (Foo&\Bar)|false $val7 { get; }

    /* testPHP84IllegalAbstractPrivateProp */
    private abstract string $val8 { get; }
    /* testPHP84IllegalAbstractReadonlyProp */
    public readonly abstract string $val9 { get; }
    /* testPHP84IllegalAbstractStaticProp */
    public abstract static string $val10 { get; }
}
