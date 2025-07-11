<?php

/* testSimpleAssignment */
$a = false;

/* testFunctionCall */
$a = doSomething();

/* testFunctionCallArgument */
$a = doSomething($a, $b);

/* testControlStructure */
while(true) {}
$a = 1;

/* testClosureAssignment */
$a = function($b=false){};

/* testHeredocFunctionArg */
myFunction(<<<END
Foo
END
, 'bar');

switch ($a) {
    case 1: {break;}
    case 2: $foo = true; break;
    default: {break;}
    /* testSwitch */
}

/* testStatementAsArrayValue */
$a = [new Datetime];
$a = array(new Datetime);
$a = ['a' => $foo + $bar, 'b' => true];

/* testUseGroup */
use Vendor\Package\{ClassA as A, ClassB, ClassC as C};

$a = [
    /* testArrowFunctionArrayValue */
    'a' => fn() => 1,
    'b' => fn() => 1,
];

/* testStaticArrowFunction */
static fn ($a) => $a;

/* testArrowFunctionReturnValue */
fn(): array => [a($a, $b)];

/* testArrowFunctionAsArgument */
$foo = foo(
    fn() => bar()
);

/* testArrowFunctionWithArrayAsArgument */
$foo = foo(
    fn() => [$row[0], $row[3]]
);

$match = match ($a) {
    /* testMatchCase */
    1 => 'foo',
    /* testMatchDefault */
    default => 'bar'
};

$match = match ($a) {
    /* testMatchMultipleCase */
    1, 2, => $a * $b,
    /* testMatchDefaultComma */
    default, => 'something'
};

match ($pressedKey) {
    /* testMatchFunctionCall */
    Key::RETURN_ => save($value, $user)
};

$result = match (true) {
    /* testMatchFunctionCallArm */
    str_contains($text, 'Welcome') || str_contains($text, 'Hello') => 'en',
    str_contains($text, 'Bienvenue') || str_contains($text, 'Bonjour') => 'fr',
    default => 'pl'
};

/* testMatchClosure */
$result = match ($key) {
    1 => function($a, $b) {},
    2 => function($b, $c) {},
};

/* testMatchArray */
$result = match ($key) {
    1 => [1,2,3],
    2 => [1 => one($a, $b), 2 => two($b, $c)],
    3 => [],
};

/* testNestedMatch */
$result = match ($key) {
    1 => match ($key) {
        1 => 'one',
        2 => 'two',
    },
    2 => match ($key) {
        1 => 'two',
        2 => 'one',
    },
};

return 0;

/* testOpenTag */
?>
<h1>Test</h1>
<?php echo '<h2>', foo(), '</h2>';

/* testOpenTagWithEcho */
?>
<h1>Test</h1>
<?= '<h2>', foo(), '</h2>';

$value = [
    /* testPrecededByArrowFunctionInArray - Expected */
    Url::make('View Song', fn($song) => $song->url())
        /* testPrecededByArrowFunctionInArray */
        ->onlyOnDetail(),

    new Panel('Information', [
        Text::make('Title')
    ]),
];

switch ($foo) {
    /* testCaseStatement */
    case 1:
        /* testInsideCaseStatement */
        $var = doSomething();
        /* testInsideCaseBreakStatement */
        break 1;

    case 2:
        /* testInsideCaseContinueStatement */
        continue 1;

    case 3:
        /* testInsideCaseReturnStatement */
        return false;

    case 4:
        /* testInsideCaseExitStatement */
        exit(1);

    case 5:
        /* testInsideCaseThrowStatement */
        throw new Exception();

    case 6:
        $var = doSomething();
        /* testInsideCaseGotoStatement */
        goto myLabel;

    /* testDefaultStatement */
    default:
        /* testInsideDefaultContinueStatement */
        continue $var;
}

myLabel:
do_something();

match ($var) {
    true =>
        /* test437ClosureDeclaration */
        function ($var) {
            /* test437EchoNestedWithinClosureWithinMatch */
            echo $var, 'text', PHP_EOL;
        },
    default => false
};

match ($var) {
    /* test437NestedLongArrayWithinMatch */
    'a' => array( 1, 2.5, $var),
    /* test437NestedFunctionCallWithinMatch */
    'b' => functionCall( 11, $var, 50.50),
    /* test437NestedArrowFunctionWithinMatch */
    'c' => fn($p1, /* test437FnSecondParamWithinMatch */ $p2) => $p1 + $p2,
    default => false
};

callMe($paramA, match ($var) {
    /* test437NestedLongArrayWithinNestedMatch */
    'a' => array( 1, 2.5, $var),
    /* test437NestedFunctionCallWithinNestedMatch */
    'b' => functionCall( 11, $var, 50.50),
    /* test437NestedArrowFunctionWithinNestedMatch */
    'c' => fn($p1, /* test437FnSecondParamWithinNestedMatch */ $p2) => $p1 + $p2,
    default => false
});

match ($var) {
    /* test437NestedShortArrayWithinMatch */
    'a' => [ 1, 2.5, $var],
    default => false
};
