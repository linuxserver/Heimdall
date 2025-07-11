<?php
class myclass extends yourclass implements someint {
    function myfunc($var) {
        echo $var;
    }
}

$myvar = true;
myfunc(&$myvar);
myfunc($myvar);

$this->myfunc(&$myvar);
$this->myfunc($myvar);

myclass::myfunc(&$myvar);
myclass::myfunc($myvar);

while(testfunc($var1, &$var2, $var3, &$var4) === false) {
}

sprintf("0%o", 0777 & $p);

$foo(&$myvar);

if (is_array($foo = &$this->bar())) {
}

Hooks::run( 'SecondaryDataUpdates', [ $title, $old, $recursive, $parserOutput, &$updates ] );

$foo = Bar(&$fooBar);

myfunc($myvar&$myvar);
myfunc($myvar[0]&$myvar);

myfunc(myclass::MY_CONST&$myvar);
myfunc(MY_CONST&$myvar);

efg( true == &$b );
efg( true === &$b );

foo($a, bar(&$b));
foo($a, array(&$b));

enum Foo {}
interface Foo {}
trait Foo {}

$instance = new $var($a);
$instance = new MyClass($a);
$instance = new $var(&$a);
$instance = new MyClass(&$a);

$anon = new class($a) {};
$anon = new class(&$a) {};

class Foo extends Bar {
    function myMethod() {
        $a = new static($var);
        $b = new self($var);
        $c = new parent($var);

        $d = new static(&$var);
        $e = new self(&$var);
        $f = new parent(&$var);
    }
}
