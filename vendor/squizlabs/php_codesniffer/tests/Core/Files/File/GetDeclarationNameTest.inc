<?php

/* testInvalidTokenPassed */
echo MY_CONSTANT;

/* testClosure */
$closure = function() {};

/* testAnonClassWithParens */
$anonClass = new class() {};

/* testAnonClassWithParens2 */
$class = new class() {
    private $property = 'test';
    public function test() {}
};

/* testAnonClassWithoutParens */
$anonClass = new class {};

/* testAnonClassExtendsWithoutParens */
$class = new class extends SomeClass {
    private $property = 'test';
    public function test() {}
};

/* testFunction */
function functionName() {}

/* testFunctionReturnByRef */
function & functionNameByRef() {}

/* testClass */
abstract class ClassName {
    /* testMethod */
    public function methodName() {}

    /* testAbstractMethod */
    abstract protected function abstractMethodName();

    /* testMethodReturnByRef */
    private function &MethodNameByRef();
}

/* testExtendedClass */
class ExtendedClass extends Foo {}

/* testInterface */
interface InterfaceName {}

/* testTrait */
trait TraitName {
    /* testFunctionEndingWithNumber */
    function ValidNameEndingWithNumber5(){}
}

/* testClassWithNumber */
class ClassWith1Number implements SomeInterface {}

/* testInterfaceWithNumbers */
interface InterfaceWith12345Numbers extends AnotherInterface {}

/* testClassWithCommentsAndNewLines */
class /* comment */

// phpcs:ignore Standard.Cat.SniffName -- for reasons
    ClassWithCommentsAndNewLines {}

/* testFunctionFn */
function fn() {}

/* testPureEnum */
enum Foo
{
    case SOME_CASE;
}

/* testBackedEnumSpaceBetweenNameAndColon */
enum Hoo : string
{
    case ONE = 'one';
    case TWO = 'two';
}

/* testBackedEnumNoSpaceBetweenNameAndColon */
enum Suit: int implements Colorful, CardGame {}

/* testFunctionReturnByRefWithReservedKeywordEach */
function &each() {}

/* testFunctionReturnByRefWithReservedKeywordParent */
function &parent() {}

/* testFunctionReturnByRefWithReservedKeywordSelf */
function &self() {}

/* testFunctionReturnByRefWithReservedKeywordStatic */
function &static() {}
