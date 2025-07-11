<?php
Abstract Class MyClass Extends MyClass {}
Final Class MyClass Implements MyInterface {}
Interface MyInterface {}
Trait MyTrait {}
Enum MyEnum IMPLEMENTS Colorful {}

ReadOnly class MyClass
{
    Var $myVar = null;
    Const myConst = true;
}

$a = new CLASS() {};

$anon = new ReadOnly class() {};

class FinalProperties {
    FINAL int $prop = 1;
}
