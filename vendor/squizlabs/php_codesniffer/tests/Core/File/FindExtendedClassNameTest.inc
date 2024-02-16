<?php

/* testNotAClass */
function notAClass() {}

/* testNonExtendedClass */
class testFECNNonExtendedClass {}

/* testExtendsUnqualifiedClass */
class testFECNExtendedClass extends testFECNClass {}

/* testExtendsFullyQualifiedClass */
class testFECNNamespacedClass extends \PHP_CodeSniffer\Tests\Core\File\testFECNClass {}

/* testExtendsPartiallyQualifiedClass */
class testFECNQualifiedClass extends Core\File\RelativeClass {}

/* testNonExtendedInterface */
interface testFECNInterface {}

/* testInterfaceExtendsUnqualifiedInterface */
interface testInterfaceThatExtendsInterface extends testFECNInterface{}

/* testInterfaceExtendsFullyQualifiedInterface */
interface testInterfaceThatExtendsFQCNInterface extends \PHP_CodeSniffer\Tests\Core\File\testFECNInterface{}

/* testExtendedAnonClass */
$anon = new class( $a, $b ) extends testFECNExtendedAnonClass {};

/* testNestedExtendedClass */
class testFECNNestedExtendedClass {
	public function someMethod() {
		/* testNestedExtendedAnonClass */
		$anon = new class extends testFECNAnonClass {};
	}
}

/* testClassThatExtendsAndImplements */
class testFECNClassThatExtendsAndImplements extends testFECNClass implements InterfaceA, InterfaceB {}

/* testClassThatImplementsAndExtends */
class testFECNClassThatImplementsAndExtends implements InterfaceA, InterfaceB extends testFECNClass {}

/* testInterfaceMultiExtends */
interface Multi extends \Package\FooInterface, \BarInterface {};

/* testMissingExtendsName */
class testMissingExtendsName extends { /* missing classname */ } // Intentional parse error.

// Intentional parse error. Has to be the last test in the file.
/* testParseError */
class testParseError extends testFECNClass
