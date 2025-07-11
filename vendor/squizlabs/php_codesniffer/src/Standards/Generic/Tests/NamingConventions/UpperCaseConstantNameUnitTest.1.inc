<?php
use Exception as My_Exception, foo\bar, baz;
namespace foo;
namespace foo\bar;
namespace bar\foo\baz;

define('VALID_NAME', true);
DEFINE('invalidName', true);
define("VALID_NAME", true);
define("invalidName", true);
define('bar\foo\baz\VALID_NAME_WITH_NAMESPACE', true);
define('bar\foo\baz\invalidNameWithNamespace', true);
define("bar\foo\baz\VALID_NAME_WITH_NAMESPACE", true);
define("bar\foo\baz\invalidNameWithNamespace", true);

class TestClass extends MyClass implements MyInterface, YourInterface
{

    const const1 = 'hello';
    const CONST2 = 'hello';
}

$foo->define('bar');
$foo->getBar()->define('foo');
Foo::define('bar');

class ClassConstBowOutTest {
    const /* comment */ abc = 1;
    const // phpcs:ignore Standard.Category.Sniff
        some_constant = 2;
}

$foo->getBar()?->define('foo');

// PHP 8.3 introduces typed constants.
class TypedConstants {

    const MyClass MYCONST = new MyClass;
    const int VALID_NAME = 0;
    final public const INT invalid_name = 0;
    const FALSE false = false; // Yes, false can be used as a constant name, don't ask.
    final protected const array ARRAY = array(); // Same goes for array.
}

define /* comment */ ( /* comment */ 'CommentsInUnconventionalPlaces', 'value' );

define
// comment
(
    // phpcs:ignore Stnd.Cat.SniffName -- for reasons.
    'CommentsInUnconventionalPlaces',
    'value'
);

$foo-> /* comment */ define('bar');
$foo?->
// phpcs:ignore Stnd.Cat.SniffName -- for reasons.
define('bar');

const DEFINE = 'value';

#[Define('some param')]
class MyClass {}

#[
    AttributeA,
    define('some param')
]
class MyClass {}

const MixedCase = 1;

define('lower_case_name', 'value');
define($var, 'sniff should bow out');
define(constantName(), 'sniff should bow out');
define($obj->constantName(), 'sniff should bow out');
define(MyClass::constantName(), 'sniff should bow out');
define(condition() ? 'name1' : 'name2', 'sniff should bow out');

$callable = define(...);

// Valid if outside the global namespace. Sniff should bow out.
function define($param) {}

class MyClass {
    public function define($param) {}
}

$a = ($cond) ? DEFINE : SOMETHING_ELSE;

$object = new Define('value');
