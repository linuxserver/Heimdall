<?php

/* testImportUse */
use Vendor\Package\Sub as Alias;

/* testImportGroupUse */
use Vendor\Package\Sub\{
    ClassA,
    ClassB as BAlias,
};

if ($foo) {}

/* testTraitUse */
class TraitUse {
    use ImportedTrait;

    function methodName() {}
}

/* testNotAFunction */
interface NotAFunction {};

/* testFunctionNoParams */
function noParams() {}

/* testPassByReference */
function passByReference(&$var) {}

/* testArrayHint */
function arrayHint(array $var) {}

/* testVariable */
function variable($var) {}

/* testSingleDefaultValue */
function defaultValue($var1=self::CONSTANT) {}

/* testDefaultValues */
function defaultValues($var1=1, $var2='value') {}

/* testTypeHint */
function typeHint(foo $var1, bar $var2) {}

class MyClass {
    /* testSelfTypeHint */
    function typeSelfHint(self $var) {}
}

/* testNullableTypeHint */
function nullableTypeHint(?int $var1, ?\bar $var2) {}

/* testBitwiseAndConstantExpressionDefaultValue */
function myFunction($a = 10 & 20) {}

/* testArrowFunction */
fn(int $a, ...$b) => $b;

/* testArrowFunctionReturnByRef */
fn&(?string $a) => $b;

/* testArrayDefaultValues */
function arrayDefaultValues($var1 = [], $var2 = array(1, 2, 3) ) {}

/* testConstantDefaultValueSecondParam */
function constantDefaultValueSecondParam($var1, $var2 = M_PI) {}

/* testScalarTernaryExpressionInDefault */
function ternayInDefault( $a = FOO ? 'bar' : 10, ? bool $b ) {}

/* testVariadicFunction */
function variadicFunction( int ... $a ) {}

/* testVariadicByRefFunction */
function variadicByRefFunction( &...$a ) {}

/* testVariadicFunctionClassType */
function variableLengthArgument($unit, DateInterval ...$intervals) {}

/* testNameSpacedTypeDeclaration */
function namespacedClassType( \Package\Sub\ClassName $a, ?Sub\AnotherClass $b ) {}

/* testWithAllTypes */
class testAllTypes {
    function allTypes(
        ?ClassName $a,
        self $b,
        parent $c,
        object $d,
        ?int $e,
        string &$f,
        iterable $g,
        bool $h = true,
        callable $i = 'is_null',
        float $j = 1.1,
        array ...$k
    ) {}
}

/* testArrowFunctionWithAllTypes */
$fn = fn(
    ?ClassName $a,
    self $b,
    parent $c,
    object $d,
    ?int $e,
    string &$f,
    iterable $g,
    bool $h = true,
    callable $i = 'is_null',
    float $j = 1.1,
    array ...$k
) => $something;

/* testMessyDeclaration */
function messyDeclaration(
    // comment
    ?\MyNS /* comment */
        \ SubCat // phpcs:ignore Standard.Cat.Sniff -- for reasons.
            \  MyClass $a,
    $b /* test */ = /* test */ 'default' /* test*/,
    // phpcs:ignore Stnd.Cat.Sniff -- For reasons.
    ? /*comment*/
        bool // phpcs:disable Stnd.Cat.Sniff -- For reasons.
        & /*test*/ ... /* phpcs:ignore */ $c
) {}

/* testPHP8MixedTypeHint */
function mixedTypeHint(mixed &...$var1) {}

/* testPHP8MixedTypeHintNullable */
// Intentional fatal error - nullability is not allowed with mixed, but that's not the concern of the method.
function mixedTypeHintNullable(?Mixed $var1) {}

/* testNamespaceOperatorTypeHint */
function namespaceOperatorTypeHint(?namespace\Name $var1) {}

/* testPHP8UnionTypesSimple */
function unionTypeSimple(int|float $number, self|parent &...$obj) {}

/* testPHP8UnionTypesWithSpreadOperatorAndReference */
function globalFunctionWithSpreadAndReference(float|null &$paramA, string|int ...$paramB ) {}

/* testPHP8UnionTypesSimpleWithBitwiseOrInDefault */
$fn = fn(int|float $var = CONSTANT_A | CONSTANT_B) => $var;

/* testPHP8UnionTypesTwoClasses */
function unionTypesTwoClasses(MyClassA|\Package\MyClassB $var) {}

/* testPHP8UnionTypesAllBaseTypes */
function unionTypesAllBaseTypes(array|bool|callable|int|float|null|object|string $var) {}

/* testPHP8UnionTypesAllPseudoTypes */
// Intentional fatal error - mixing types which cannot be combined, but that's not the concern of the method.
function unionTypesAllPseudoTypes(false|mixed|self|parent|iterable|Resource $var) {}

/* testPHP8UnionTypesNullable */
// Intentional fatal error - nullability is not allowed with union types, but that's not the concern of the method.
$closure = function (?int|float $number) {};

/* testPHP8PseudoTypeNull */
// PHP 8.0 - 8.1: Intentional fatal error - null pseudotype is only allowed in union types, but that's not the concern of the method.
function pseudoTypeNull(null $var = null) {}

/* testPHP8PseudoTypeFalse */
// PHP 8.0 - 8.1: Intentional fatal error - false pseudotype is only allowed in union types, but that's not the concern of the method.
function pseudoTypeFalse(false $var = false) {}

/* testPHP8PseudoTypeFalseAndBool */
// Intentional fatal error - false pseudotype is not allowed in combination with bool, but that's not the concern of the method.
function pseudoTypeFalseAndBool(bool|false $var = false) {}

/* testPHP8ObjectAndClass */
// Intentional fatal error - object is not allowed in combination with class name, but that's not the concern of the method.
function objectAndClass(object|ClassName $var) {}

/* testPHP8PseudoTypeIterableAndArray */
// Intentional fatal error - iterable pseudotype is not allowed in combination with array or Traversable, but that's not the concern of the method.
function pseudoTypeIterableAndArray(iterable|array|Traversable $var) {}

/* testPHP8DuplicateTypeInUnionWhitespaceAndComment */
// Intentional fatal error - duplicate types are not allowed in union types, but that's not the concern of the method.
function duplicateTypeInUnion( int | string /*comment*/ | INT $var) {}

class ConstructorPropertyPromotionNoTypes {
    /* testPHP8ConstructorPropertyPromotionNoTypes */
    public function __construct(
        public $x = 0.0,
        protected $y = '',
        private $z = null,
    ) {}
}

class ConstructorPropertyPromotionWithTypes {
    /* testPHP8ConstructorPropertyPromotionWithTypes */
    public function __construct(protected float|int $x, public ?string &$y = 'test', private mixed $z) {}
}

class ConstructorPropertyPromotionAndNormalParams {
    /* testPHP8ConstructorPropertyPromotionAndNormalParam */
    public function __construct(public int $promotedProp, ?int $normalArg) {}
}

class ConstructorPropertyPromotionWithReadOnly {
    /* testPHP81ConstructorPropertyPromotionWithReadOnly */
    public function __construct(public readonly ?int $promotedProp, ReadOnly private string|bool &$promotedToo) {}
}

class ConstructorPropertyPromotionWithReadOnlyNoTypeDeclaration {
    /* testPHP81ConstructorPropertyPromotionWithReadOnlyNoTypeDeclaration */
    // Intentional fatal error. Readonly properties MUST be typed.
    public function __construct(public readonly $promotedProp, ReadOnly private &$promotedToo) {}
}

class ConstructorPropertyPromotionWithOnlyReadOnly {
    /* testPHP81ConstructorPropertyPromotionWithOnlyReadOnly */
    public function __construct(readonly Foo&Bar $promotedProp, readonly ?bool $promotedToo,) {}
}

/* testPHP8ConstructorPropertyPromotionGlobalFunction */
// Intentional fatal error. Property promotion not allowed in non-constructor, but that's not the concern of this method.
function globalFunction(private $x) {}

abstract class ConstructorPropertyPromotionAbstractMethod {
    /* testPHP8ConstructorPropertyPromotionAbstractMethod */
    // Intentional fatal error.
    // 1. Property promotion not allowed in abstract method, but that's not the concern of this method.
    // 2. Variadic arguments not allowed in property promotion, but that's not the concern of this method.
    // 3. The callable type is not supported for properties, but that's not the concern of this method.
    abstract public function __construct(public callable $y, private ...$x);
}

/* testCommentsInParameter */
function commentsInParams(
    // Leading comment.
    ?MyClass /*-*/ & /*-*/.../*-*/ $param /*-*/ = /*-*/ 'default value' . /*-*/ 'second part' // Trailing comment.
) {}

/* testParameterAttributesInFunctionDeclaration */
class ParametersWithAttributes(
    public function __construct(
        #[\MyExample\MyAttribute] private string $constructorPropPromTypedParamSingleAttribute,
        #[MyAttr([1, 2])]
        Type|false
        $typedParamSingleAttribute,
        #[MyAttribute(1234), MyAttribute(5678)] ?int $nullableTypedParamMultiAttribute,
        #[WithoutArgument] #[SingleArgument(0)] $nonTypedParamTwoAttributes,
        #[MyAttribute(array("key" => "value"))]
        &...$otherParam,
    ) {}
}

/* testPHP8IntersectionTypes */
function intersectionTypes(Foo&Bar $obj1, Boo&Bar $obj2) {}

/* testPHP81IntersectionTypesWithSpreadOperatorAndReference */
function globalFunctionWithSpreadAndReference(Boo&Bar &$paramA, Foo&Bar ...$paramB) {}

/* testPHP81MoreIntersectionTypes */
function moreIntersectionTypes(MyClassA&\Package\MyClassB&\Package\MyClassC $var) {}

/* testPHP81IllegalIntersectionTypes */
// Intentional fatal error - simple types are not allowed with intersection types, but that's not the concern of the method.
$closure = function (string&int $numeric_string) {};

/* testPHP81NullableIntersectionTypes */
// Intentional fatal error - nullability is not allowed with intersection types, but that's not the concern of the method.
$closure = function (?Foo&Bar $object) {};

/* testPHP82PseudoTypeTrue */
function pseudoTypeTrue(?true $var = true) {}

/* testPHP82PseudoTypeFalseAndTrue */
// Intentional fatal error - Type contains both true and false, bool should be used instead, but that's not the concern of the method.
function pseudoTypeFalseAndTrue(true|false $var = true) {}

/* testPHP81NewInInitializers */
function newInInitializers(
    TypeA $new = new TypeA(self::CONST_VALUE),
    \Package\TypeB $newToo = new \Package\TypeB(10, 'string'),
) {}

/* testFunctionCallFnPHPCS353-354 */
$value = $obj->fn(true);

/* testClosureNoParams */
function() {};

/* testClosure */
function( $a = 'test' ) {};

/* testClosureUseNoParams */
function() use() {};

/* testClosureUse */
function() use( $foo, $bar ) {};

/* testFunctionParamListWithTrailingComma */
function trailingComma(
    ?string $foo  /*comment*/ ,
    $bar = 0,
) {}

/* testClosureParamListWithTrailingComma */
function(
    $foo,
    $bar,
) {};

/* testArrowFunctionParamListWithTrailingComma */
$fn = fn( ?int $a , ...$b, ) => $b;

/* testClosureUseWithTrailingComma */
function() use(
    $foo  /*comment*/ ,
    $bar,
) {};

/* testArrowFunctionLiveCoding */
// Intentional parse error. This has to be the last test in the file.
$fn = fn
