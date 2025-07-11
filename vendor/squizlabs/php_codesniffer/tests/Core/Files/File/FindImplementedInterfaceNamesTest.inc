<?php

/* testNotAClass */
function notAClass() {}

/* testPlainInterface */
interface testFIINInterface {}

/* testNonImplementedClass */
class testFIINNonImplementedClass {}

/* testClassImplementsSingle */
class testFIINImplementedClass implements testFIINInterface {}

/* testClassImplementsMultiple */
class testFIINMultiImplementedClass implements testFIINInterface, testFIINInterface2 {}

/* testImplementsFullyQualified */
class testFIINNamespacedClass implements \PHP_CodeSniffer\Tests\Core\File\testFIINInterface {}

/* testImplementsPartiallyQualified */
class testFIINQualifiedClass implements Core\File\RelativeInterface {}

/* testClassThatExtendsAndImplements */
class testFECNClassThatExtendsAndImplements extends testFECNClass implements InterfaceA, \NameSpaced\Cat\InterfaceB {}

/* testClassThatImplementsAndExtends */
class testFECNClassThatImplementsAndExtends implements \InterfaceA, InterfaceB extends testFECNClass {}

/* testBackedEnumWithoutImplements */
enum Suit:string {}

/* testEnumImplementsSingle */
enum Suit implements Colorful {}

/* testBackedEnumImplementsMulti */
enum Suit: string implements Colorful, \Deck {}

/* testAnonClassImplementsSingle */
$anon = class() implements testFIINInterface {}

/* testMissingImplementsName */
class testMissingExtendsName implements { /* missing interface name */  } // Intentional parse error.

// Intentional parse error. Has to be the last test in the file.
/* testParseError */
class testParseError implements testInterface
