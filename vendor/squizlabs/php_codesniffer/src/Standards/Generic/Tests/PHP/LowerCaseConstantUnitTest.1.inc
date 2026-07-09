<?php

// True
function myFunction($arg1, $arg2=true)
{
}
function myFunction($arg1, $arg2=TRUE)
{
}
function myFunction($arg1, $arg2=True)
{
}

if ($variable === true) { }
if ($variable === TRUE) { }
if ($variable === True) { }


// False
function myFunction($arg1, $arg2=false)
{
}
function myFunction($arg1, $arg2=FALSE)
{
}
function myFunction($arg1, $arg2=False)
{
}

if ($variable === false) { }
if ($variable === FALSE) { }
if ($variable === False) { }


// Null
function myFunction($arg1, $arg2=null)
{
}
function myFunction($arg1, $arg2=NULL)
{
}
function myFunction($arg1, $arg2=Null)
{
}

if ($variable === null) { }
if ($variable === NULL) { }
if ($variable === Null) { }

$x = new stdClass();
$x->NULL = 7;

use Zend\Log\Writer\NULL as NullWriter;
new \Zend\Log\Writer\NULL();

namespace False;

class True extends Null implements False {}

use True\Something;
use Something\True;
class MyClass
{
    public function myFunction()
    {
        $var = array('foo' => new True());
    }
}

$x = $f?FALSE:true;
$x = $f? FALSE:true;

class MyClass
{
    // Spice things up a little.
    const TRUE = false;
}

var_dump(MyClass::TRUE);

function tRUE() {}

$input->getFilterChain()->attachByName('Null', ['type' => Null::TYPE_STRING]);

// Issue #3332 - ignore type declarations, but not default values.
class TypedThings {
    const MYCONST = FALSE;

    public int|FALSE $int = FALSE;
    public Type|NULL $int = new MyObj(NULL);

    private function typed(int|FALSE $param = NULL, Type|NULL $obj = new MyObj(FALSE)) : string|FALSE|NULL
    {
        if (TRUE === FALSE) {
            return NULL;
        }
    }
}

$cl = function (int|FALSE $param = NULL, Type|NULL $obj = new MyObj(FALSE)) : string|FALSE|NULL {};

// Adding some extra tests to safeguard that function declarations which don't create scope are handled correctly.
interface InterfaceMethodsWithReturnTypeNoScopeOpener {
    private function typed($param = TRUE) : string|FALSE|NULL;
}

abstract class ClassMethodsWithReturnTypeNoScopeOpener {
    abstract public function typed($param = FALSE) : TRUE;
}

// Additional tests to safeguard improved property type skip logic.
readonly class Properties {
    use SomeTrait {
        sayHello as private myPrivateHello;
    }

    public Type|FALSE|NULL $propertyA = array(
        'itemA' => TRUE,
        'itemB' => FALSE,
        'itemC' => NULL,
    ), $propertyB = FALSE;

    protected \FullyQualified&Partially\Qualified&namespace\Relative $propertyC;
    var ?TRUE $propertyD;
    static array|callable|FALSE|self|parent $propertyE = TRUE;
    private
        // phpcs:ignore Stnd.Cat.Sniff -- for reasons.
        TRUE      /*comment*/
        $propertyF = TRUE;

    public function __construct(
        public FALSE|NULL $promotedPropA,
        readonly callable|TRUE $promotedPropB,
    ) {
        static $var;
        echo static::class;
        static::foo();
        $var = $var instanceof static;
        $obj = new static();
    }

    public static function foo(): static|self|FALSE {
        $callable = static function() {};
    }
}

// PHP 8.3 introduces typed constants.
class TypedConstants {
    const MyClass|NULL|TRUE|FALSE MYCONST = FALSE;
}

// Global constants can not be typed.
const MYCONST = TRUE;

class SkipOverPHP82DNFTypes {
    protected (\FullyQualified&Partially\Qualified)|TRUE $propertyC;
}

class SkipOverPHP84FinalProperties {
    final MyType|FALSE $propA;
    private static final NULL|MyClass $propB;
}

// PHP 8.4 asymmetric visibility
class WithAsym {

    private(set) NULL|TRUE $asym1 = TRUE;

    public private(set) ?bool $asym2 = FALSE;

    protected(set) FALSE|string|null $asym3 = NULL;

    public protected(set) Type|NULL|bool $asym4 = TRUE;
}

abstract class SkipOverPHP84AbstractProperties {
    abstract MyType|TRUE $propA {get;}
    protected abstract NULL|MyClass $propB {set;}
}

$a = \NULL;
$a = \falSe;
$a = \True;
