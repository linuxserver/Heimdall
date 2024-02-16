<?php

class Foo
{
    /* testReadonlyProperty */
    readonly int $readonlyProperty;
    /* testVarReadonlyProperty */
    var readonly int $varReadonlyProperty;
    /* testReadonlyVarProperty */
    readonly var int $testReadonlyVarProperty;
    /* testStaticReadonlyProperty */
    static readonly int $staticReadonlyProperty;
    /* testReadonlyStaticProperty */
    readonly static int $readonlyStaticProperty;
    /* testConstReadonlyProperty */
    public readonly const MYCONSTANT = 'foo';
    /* testReadonlyPropertyWithoutType */
    readonly $propertyWithoutType;
    /* testPublicReadonlyProperty */
    public readonly int $publicReadonlyProperty;
    /* testProtectedReadonlyProperty */
    protected readonly int $protectedReadonlyProperty;
    /* testPrivateReadonlyProperty */
    private readonly int $privateReadonlyProperty;
    /* testPublicReadonlyPropertyWithReadonlyFirst */
    readonly public int $publicReadonlyProperty;
    /* testProtectedReadonlyPropertyWithReadonlyFirst */
    readonly protected int $protectedReadonlyProperty;
    /* testPrivateReadonlyPropertyWithReadonlyFirst */
    readonly private int $privateReadonlyProperty;
    /* testReadonlyWithCommentsInDeclaration */
    private /* Comment */ readonly /* Comment */ int /* Comment */ $readonlyPropertyWithCommentsInDeclaration;
    /* testReadonlyWithNullableProperty */
    private readonly ?int $nullableProperty;
    /* testReadonlyNullablePropertyWithUnionTypeHintAndNullFirst */
    private readonly null|int $nullablePropertyWithUnionTypeHintAndNullFirst;
    /* testReadonlyNullablePropertyWithUnionTypeHintAndNullLast */
    private readonly int|null $nullablePropertyWithUnionTypeHintAndNullLast;
    /* testReadonlyPropertyWithArrayTypeHint */
    private readonly array $arrayProperty;
    /* testReadonlyPropertyWithSelfTypeHint */
    private readonly self $selfProperty;
    /* testReadonlyPropertyWithParentTypeHint */
    private readonly parent $parentProperty;
    /* testReadonlyPropertyWithFullyQualifiedTypeHint */
    private readonly \stdClass $propertyWithFullyQualifiedTypeHint;

    /* testReadonlyIsCaseInsensitive */
    public ReAdOnLy string $caseInsensitiveProperty;

    /* testReadonlyConstructorPropertyPromotion */
    public function __construct(private readonly bool $constructorPropertyPromotion) {}

    /* testReadonlyConstructorPropertyPromotionWithReference */
    public function __construct(private ReadOnly bool &$constructorPropertyPromotion) {}
}

$anonymousClass = new class () {
    /* testReadonlyPropertyInAnonymousClass */
    public readonly int $property;
};

class ClassName {
    /* testReadonlyUsedAsClassConstantName */
    const READONLY = 'readonly';

    /* testReadonlyUsedAsMethodName */
    public function readonly() {
        /* testReadonlyUsedAsPropertyName */
        $this->readonly = 'foo';

        /* testReadonlyPropertyInTernaryOperator */
        $isReadonly = $this->readonly ? true : false;
    }
}

/* testReadonlyUsedAsFunctionName */
function readonly() {}

/* testReadonlyUsedAsFunctionNameWithReturnByRef */
function &readonly() {}

/* testReadonlyUsedAsNamespaceName */
namespace Readonly;
/* testReadonlyUsedAsPartOfNamespaceName */
namespace My\Readonly\Collection;
/* testReadonlyAsFunctionCall */
$var = readonly($a, $b);
/* testReadonlyAsNamespacedFunctionCall */
$var = My\NS\readonly($a, $b);
/* testReadonlyAsNamespaceRelativeFunctionCall */
$var = namespace\ReadOnly($a, $b);
/* testReadonlyAsMethodCall */
$var = $obj->readonly($a, $b);
/* testReadonlyAsNullsafeMethodCall */
$var = $obj?->readOnly($a, $b);
/* testReadonlyAsStaticMethodCallWithSpace */
$var = ClassName::readonly ($a, $b);
/* testClassConstantFetchWithReadonlyAsConstantName */
echo ClassName::READONLY;

/* testReadonlyUsedAsFunctionCallWithSpaceBetweenKeywordAndParens */
$var = readonly /* comment */ ();

// These test cases are inspired by
// https://github.com/php/php-src/commit/08b75395838b4b42a41e3c70684fa6c6b113eee0
class ReadonlyWithDisjunctiveNormalForm
{
    /* testReadonlyPropertyDNFTypeUnqualified */
    readonly (B&C)|A $h;

    /* testReadonlyPropertyDNFTypeFullyQualified */
    public readonly (\Fully\Qualified\B&\Full\C)|\Foo\Bar $j;

    /* testReadonlyPropertyDNFTypePartiallyQualified */
    protected readonly (Partially\Qualified&C)|A $l;

    /* testReadonlyPropertyDNFTypeRelativeName */
    private readonly (namespace\Relative&C)|A $n;

    /* testReadonlyPropertyDNFTypeMultipleSets */
    private readonly (A&C)|(B&C)|(C&D) $m;

    /* testReadonlyPropertyDNFTypeWithArray */
    private readonly (B & C)|array $o;

    /* testReadonlyPropertyDNFTypeWithSpacesAndComments */
    private readonly ( B & C /*something*/) | A $q;

    public function __construct(
        /* testReadonlyConstructorPropertyPromotionWithDNF */
        private readonly (B&C)|A $b1,
        /* testReadonlyConstructorPropertyPromotionWithDNFAndReference */
        readonly (B&C)|A &$b2,
    ) {}

    /* testReadonlyUsedAsMethodNameWithDNFParam */
    public function readonly (A&B $param): void {}
}

/* testReadonlyAnonClassWithParens */
$anon = new readonly class() {};

/* testReadonlyAnonClassWithoutParens */
$anon = new Readonly class {};

/* testReadonlyAnonClassWithCommentsAndWhitespace */
$anon = new
// comment
READONLY
// phpcs:ignore Stnd.Cat.Sniff
class {};

/* testParseErrorLiveCoding */
// This must be the last test in the file.
readonly
