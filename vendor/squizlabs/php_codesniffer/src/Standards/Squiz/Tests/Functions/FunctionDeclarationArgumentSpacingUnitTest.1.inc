<?php
function myFunc() {}
function myFunc( ) {}
function myFunc($blah) {}
function myFunc( $blah ) {}
function myFunc($blah, $blah2, $blah3) {}
function myFunc($blah , $blah2, $blah3 ) {}
function myFunc($blah,$blah2,$blah3) {}
function myFunc($blah,   $blah2,  $blah3) {}
function myFunc($blah='hello') {}
function myFunc($blah = 'hello') {}
function myFunc(PHP_CodeSniffer $object, array $array=array(), $blah3='yo') {}
function myFunc(  $blah='hello'   , $blah2=   'hi',  $blah3   = 'yo' ) {}
function myFunc(PHP_CodeSniffer   $object, array  $array=array(), $blah3='yo') {}
function myFunc( PHP_CodeSniffer $object,  array $array=array(), $blah3='yo') {}
function myFunc( array  &$one,  array  &$two) {}
function myFunc(&$blah) {}
function myFunc( &$blah ) {}

function multiLineFunction(
    array $flatList,
    $markup,
    array $otherList,
    $lastOffset=0
) {
}

function multiLineFunction(
    $markup,
    array $otherList,
    $lastOffset=0
) {
}

$noArgs_longVars = function ($longVar1,  $longerVar2= false) use (
    $longVar1 ,  $longerVar2,
    $muchLongerVar3
)  {
    // body
};

// phpcs:set Squiz.Functions.FunctionDeclarationArgumentSpacing equalsSpacing 1
function myFunc($blah = 'hello') {}
function myFunc($blah  =  'hello') {}
function myFunc($blah =  'hello') {}
function myFunc($blah  = 'hello') {}
// phpcs:set Squiz.Functions.FunctionDeclarationArgumentSpacing equalsSpacing 0

// phpcs:set Squiz.Functions.FunctionDeclarationArgumentSpacing requiredSpacesAfterOpen 1
// phpcs:set Squiz.Functions.FunctionDeclarationArgumentSpacing requiredSpacesBeforeClose 1
function myFunc($blah) {}
function myFunc( $blah ) {}
function myFunc(  $blah  ) {}
function myFunc( array $blah ) {}
function myFunc(array $blah ) {}
function myFunc(  array $blah ) {}
function myFunc() {}
function myFunc( ) {}

function multiLineFunction(
	array $flatList,
	$markup,
	array $otherList,
	$lastOffset=0
) {
}
// phpcs:set Squiz.Functions.FunctionDeclarationArgumentSpacing requiredSpacesAfterOpen 0
// phpcs:set Squiz.Functions.FunctionDeclarationArgumentSpacing requiredSpacesBeforeClose 0

function myFunc($req, $opt=null, ...$params) {}
function myFunc($param, &...$moreParams) {}

function MissingParamTypeInDocBlock(array$a = null, callable$c, \ArrayObject$o, $foo = []) {}

function myFunc(...$args) {}
function myFunc( ...$args) {}
function myFunc(... $args) {}

function foo( // comment
    $bar,
    \NS\ClassName     $nsTypeHint,
    /* not a type hint */ $baz,
    string $withTypeHint
) { // comment
    // ...
}

function myFunc(/*...*/) {}
function myFunc( /*...*/ ) {}

function x(
    ?array    $a,
    ?MyNamespace\MyClass    $b,
    ?array   $c,
    ?int  $d
) {
}

function functionName(  ?string  $arg1 = 'foo' ,  ?int  & $arg2   ,  $arg3  ) {}
function functionName(string $arg1,  ... $arg2) {}
function functionName(string $arg1, int  ... $arg2) {}
function functionName(string $arg1, & ... $arg2) {}
function functionName(string $arg1,int $arg2) {}

$a = function ($var1, $var2=false) use (
    $longVar1, & $longerVar1,
    $longVar2 ,  &$longerVar2,
    $muchLongerVar3
) {};

fn ($a,$b = null) => $a($b);

function multipleWhitespaceTokensAfterType(int

    $number) {}

function spacingBetweenParenthesesShouldBeFixedInOneGo(


    ) {}

function newlineAfterReferenceShouldBeFlaggedAndFixed(
    &

    $param
) {}

function newlineAfterReferenceFixerRespectsComment(
    &
    // comment
    $param
) {}

function newlineAfterVariadicShouldBeFlaggedAndFixed(
    ...

    $param
) {}

function newlineAfterVariadicFixerRespectsComment(
    ...
    //comment
    $param
) {}

function newlineBeforeAndAfterEqualsSignShouldBeFixedForSpacing0(
    $param

        =

    true
) {}

function commentBeforeOrAfterEqualsSignShouldBeFlaggedNotFixed(
    $param   /*comment*/   =   /*comment*/ true
) {}

function newlineAndCommentBeforeAndAfterEqualsSignShouldBeFlaggedNotFixed(
    $param

    //comment

        =

    //comment

    true
) {}

// phpcs:set Squiz.Functions.FunctionDeclarationArgumentSpacing equalsSpacing 1
function newlineBeforeAndAfterEqualsSignShouldBeFixedForSpacing1(
    $param

        =

    true
) {}
// phpcs:set Squiz.Functions.FunctionDeclarationArgumentSpacing equalsSpacing 0

function newlineBeforeCommaShouldBeFixedInOneGo(
    $paramA
,
    $paramB

    ,
    $paramC
) {}

function newlineBeforeCommaFixerRespectsComments(
    $paramA // comment
    ,
    $paramB=10 /* comment */
,
    $paramC=20 # comment
    , $paramC=30
    , string $paramC='foo'
) {}

class PropertyPromotionSpacingAfterComma {
    public function __construct(private string|int $propA, protected bool $correctSpace,  public MyClass $tooMuchSpace,readonly string $noSpace) {}
}

class PropertyPromotionSpacingAfterModifier {
    public function __construct(
        private$noSpace,
        public    MyClass $tooMuchSpace,
        protected   readonly   string $tooMuchSpaceX2,
        readonly
        public
        string $tooMuchSpaceNewLines,
    ) {}
}

class AsymVisibilityPropertyPromotionSpacingAfterComma {
    public function __construct(private(set) string|int $propA, protected(set) bool $correctSpace,  public(set) MyClass $tooMuchSpace,public(set) string $noSpace) {}
}

class AsymVisibilityPropertyPromotionSpacingAfterModifier {
    public function __construct(
        private(set)$noSpace,
        public(set)    MyClass $tooMuchSpace,
        protected(set)   public   string $tooMuchSpaceX2,
        private
        public(set)
        string $tooMuchSpaceNewLines,
    ) {}
}
