<?php

/* testTernaryIsTernaryAfterConst */
$a = $this->const ? 0 : 1;

/* testGlobalConstantCannotBeTyped */
const GLOBAL_UNTYPED = true;

/* testGlobalConstantTypedShouldStillBeHandled */
const ?int GLOBAL_TYPED = true;

class ClassWithPlainTypedConstants {
    /* testClassConstFinalUntyped */
    final const FINAL_UNTYPED = true;

    /* testClassConstVisibilityUntyped */
    public const /*comment*/VISIBLE_UNTYPED = true;

    /* testClassConstTypedTrue */
    const true TYPED_TRUE = true;
    /* testClassConstTypedFalse */
    final const false TYPED_FALSE = false;
    /* testClassConstTypedNull */
    public const null TYPED_NULL = null;
    /* testClassConstTypedBool */
    final protected const/*comment*/bool TYPED_BOOL = false;
    /* testClassConstTypedInt */
    private const int TYPED_INT = 0;
    /* testClassConstTypedFloat */
    const float TYPED_FLOAT = 0.5;
    /* testClassConstTypedString */
    public final const string/*comment*/TYPED_STRING = 'string';
    /* testClassConstTypedArray */
    private final const array TYPED_ARRAY = [];
    /* testClassConstTypedObject */
    const
        object
        TYPED_OBJECT = MyClass::getInstance();
    /* testClassConstTypedIterable */
    const iterable typed_iterable = [];
    /* testClassConstTypedMixed */
    const mixed TYPED_MIXED = 'string';

    /* testClassConstTypedClassUnqualified */
    const MyClass TYPED_UNQUALIFIED_CLASSNAME = MyClass::getInstance();
    /* testClassConstTypedClassFullyQualified */
    public const \MyClass TYPED_FULLY_QUALIFIED_CLASSNAME = MyClass::getInstance();
    /* testClassConstTypedClassNamespaceRelative */
    protected const namespace\MyClass TYPED_NAMESPACE_RELATIVE_CLASSNAME = MyClass::getInstance();
    /* testClassConstTypedClassPartiallyQualified */
    private const Partial\MyClass TYPED_PARTIALLY_QUALIFIED_CLASSNAME = MyClass::getInstance();
    /* testClassConstTypedParent */
    const parent TYPED_PARENT = parent::getInstance();

    // Illegal types - the fact that these are not allowed in PHP is not the concern of the PHPCS tokenizer.
    /* testClassConstTypedCallable */
    protected const callable TYPED_CALLABLE = 'function_name';
    /* testClassConstTypedVoid */
    protected const void TYPED_VOID = null;
    /* testClassConstTypedNever */
    protected const NEVER TYPED_NEVER = null;
}

trait TraitWithNullableTypedConstants {
    /* testTraitConstTypedNullableTrue */
    const ?true TYPED_TRUE = true;
    /* testTraitConstTypedNullableFalse */
    final const ?false TYPED_FALSE = false;
    /* testTraitConstTypedNullableNull */
    public const ?null TYPED_NULL = null;
    /* testTraitConstTypedNullableBool */
    final protected const ?bool TYPED_BOOL = false;
    /* testTraitConstTypedNullableInt */
    private const ?int TYPED_INT = 0;
    /* testTraitConstTypedNullableFloat */
    const ? /*comment*/ float TYPED_FLOAT = 0.5;
    /* testTraitConstTypedNullableString */
    public final const ?string TYPED_STRING = 'string';
    /* testTraitConstTypedNullableArray */
    private final const ?  array TYPED_ARRAY = [];
    /* testTraitConstTypedNullableObject */
    const ?object TYPED_OBJECT = MyClass::getInstance();
    /* testTraitConstTypedNullableIterable */
    const ?iterable TYPED_ITERABLE = [];
    /* testTraitConstTypedNullableMixed */
    const ?mixed TYPED_MIXED = 'string';

    /* testTraitConstTypedNullableClassUnqualified */
    const ?MyClass TYPED_UNQUALIFIED_CLASSNAME = MyClass::getInstance();
    /* testTraitConstTypedNullableClassFullyQualified */
    public const ?\MyClass TYPED_FULLY_QUALIFIED_CLASSNAME = MyClass::getInstance();
    /* testTraitConstTypedNullableClassNamespaceRelative */
    protected const ?namespace\MyClass TYPED_NAMESPACE_RELATIVE_CLASSNAME = MyClass::getInstance();
    /* testTraitConstTypedNullableClassPartiallyQualified */
    private const ?Partial\MyClass TYPED_PARTIALLY_QUALIFIED_CLASSNAME = MyClass::getInstance();
    /* testTraitConstTypedNullableParent */
    const ?parent TYPED_PARENT = parent::getInstance();
}

interface InterfaceWithUnionTypedConstants {
    /* testInterfaceConstTypedUnionTrueNull */
    const true|null /*comment*/ UNION_TRUE_NULL = true;
    /* testInterfaceConstTypedUnionArrayObject */
    const array|object UNION_ARRAY_OBJECT = [];
    /* testInterfaceConstTypedUnionStringArrayInt */
    const string | array | int UNION_STRING_ARRAY_INT = 'array middle';
    /* testInterfaceConstTypedUnionFloatBoolArray */
    const float /*comment*/| bool|array UNION_FLOAT_BOOL_ARRAY = false;
    /* testInterfaceConstTypedUnionIterableFalse */
    const iterable|false UNION_ITERABLE_FALSE = false;
    /* testInterfaceConstTypedUnionUnqualifiedNamespaceRelative */
    const Unqualified|namespace\Relative UNION_UNQUALIFIED_NSRELATIVE = new Unqualified();
    /* testInterfaceConstTypedUnionFullyQualifiedPartiallyQualified */
    const \Fully\Qualified|Partially\Qualified UNION_FQN_PARTIAL = new Partial\Qualified;
}

enum EnumWithIntersectionTypedConstants {
    // Illegal types in a class, but legal in an enum.
    /* testEnumConstTypedSelf */
    final const self TYPED_SELF = self::getInstance();
    /* testEnumConstTypedStatic */
    const static TYPED_STATIC = static::getInstance();
    /* testEnumConstTypedNullableSelf */
    public const ?self TYPED_SELF = self::getInstance();
    /* testEnumConstTypedNullableStatic */
    const ?static TYPED_STATIC = static::getInstance();

    /* testEnumConstTypedIntersectUnqualifiedNamespaceRelative */
    const Unqualified&namespace\Relative UNION_UNQUALIFIED_NSRELATIVE = new Unqualified();
    /* testEnumConstTypedIntersectFullyQualifiedPartiallyQualified */
    const \Fully\Qualified&Partially\Qualified UNION_FQN_PARTIAL = new Partial\Qualified;
}

$anonClassWithDNFTypes = new class() extends Something {
    /* testAnonClassConstDNFTypeNullAfter */
    const (A&B)|null DNF_OR_NULL_1 = null;
    /* testAnonClassConstDNFTypeNullBefore */
    public final const NULL|(A&B) DNF_OR_NULL_2 = null;
    /* testAnonClassConstDNFTypeFalseBefore */
    final const false|(C&D) DNF_OR_FALSE = false;
    /* testAnonClassConstDNFTypeTrueAfter */
    private final const ( F & G ) | true DNF_OR_ARRAY = true;
    /* testAnonClassConstDNFTypeTrueBeforeFalseAfter */
    public const TRUE|(SplBool&Stringable)|FALSE DNF_OR_BOOL = true;
    /* testAnonClassConstDNFTypeArrayAfter */
    final protected const (Traversable&Countable)|array DNF_OR_ARRAY_1 = [];
    /* testAnonClassConstDNFTypeArrayBefore */
    private const array /*comment*/ | ( Traversable /*comment*/ & Countable ) DNF_OR_ARRAY_2 = new MyClass;
    /* testAnonClassConstDNFTypeInvalidNullable */
    const ? (Invalid&Fatal)|NullableNotAllowed DNF = null;

    /* testAnonClassConstDNFTypeFQNRelativePartiallyQualified */
    const (\FQN&namespace\Relative)|Partially\Qualified DNF_CLASSNAME = MyClass::getInstance();
    /* testAnonClassConstDNFTypeParentSelfStatic */
    const (parent&self)|static DNF_PARENT = parent::getInstance();
};
