<?php
// A few keywords. Obviously not valid syntax.
class Something extends SomethingElse implements Whatever {}
abstract public private protected function whatever() {}
const array();
foreach ($var as $var) { exit; }
if ($a and $b or $c xor $d) { die; } elseif( $var ) { } else {}
goto a;

Class Something EXTENDS SomethingElse implementS Whatever {}
Abstract Public Private Protected function whatever() {}
CONST array();
ForEach ($var As $var) { Exit; }
If ($a AND $b OR $c XOR $d) { Die; } ElseIf( $var ) { } Else {}
GOTO a;
$b = function (Array $a) {};
const PRIVATE;
HttpStatus::CONTINUE;
$c = Function ($f) {
    Yield $f;
    Yield From fun();
};
class X extends Y {
    public function m() {
        Parent::m();
    }
    public function n() {
        Self::n();
    }
}
FN ($x) => $x;
$r = Match ($x) {
    1 => 1,
    2 => 2,
    DEFAULT, => 3,
};

class Reading {
    Public READOnly int $var;
}

EnuM ENUM: string
{
    Case HEARTS;
}

new Class {};
new clasS extends stdClass {};
new class {};

if (isset($a) && !empty($a)) { unset($a); }
if (ISSET($a) && !Empty($a)) { UnSeT($a); }
eval('foo');
eVaL('foo');

$c = function() {
    Yield /*comment*/ From fun();
    YIELD
    /*comment*/
    FROM fun();
}

class AsymmetricVisibility {
    public(set) int $correctPublicSet;
    protected(set) int $correctProtectedSet;
    private(set) int $correctPrivateSet;

    PubliC(SeT) int $wrongCasePublic;
    PROTECTED(set) array $wrongCaseProtected;
    protected(sEt) int $wrongCaseProtectedSet;
    Private(set) int $wrongCasePrivate;
    private(SET) readonly ?string $wrongCasePrivateSet;
}

__HALT_COMPILER(); // An exception due to phar support.
function
