<?php

abstract class IncorrectName {} // Error.

abstract class AbstractCorrectName {}

abstract class IncorrectNameAbstract {} // Error.

abstract
    /*comment*/
    class
    InvalidNameabstract
    extends
    BarClass {} // Error.

abstract class /*comment*/ IncorrectAbstractName {} // Error.

// Anonymous classes can't be declared as abstract (and don't have a name anyhow).
$anon = new class {};

// Make sure that if the class is not abstract, the sniff does not check the name.
class AbstractClassName {}

// Class name is always checked, doesn't matter if the class is declared conditionally.
if (!class_exists('AbstractClassCorrectName')) {
    abstract class AbstractClassCorrectName {}
}
if (!class_exists('ClassAbstractIncorrectName')) {
    abstract class ClassAbstractIncorrectName implements FooInterface {} // Error.
}

$var = 'abstract class TextStringsAreDisregarded';

class NotAnAbstractClassSoNoPrefixRequired {}

abstract class abstractOkCaseOfPrefixIsNotEnforced {}

final class FinalClassShouldNotTriggerWarning {}

readonly class ReadonlyClassShouldNotTriggerWarning {}

abstract readonly class AbstractReadonlyClassWithPrefixShouldNotTriggerWarning {}

abstract readonly class ReadonlyAbstractClassShouldTriggerWarningWhenPrefixIsMissingA {} // Error.
readonly abstract class ReadonlyAbstractClassShouldTriggerWarningWhenPrefixIsMissingB {} // Error.
