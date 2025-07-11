<?php

function test()
{
    $string = 'hello';
    $string = 'hello'; // Set string to hello.
    // Valid comment.
}

function testing() // This is a function
{

}//end test()


class TestClass
{
    public $good = true; // Indeed.

}//end class

if (true || -1 == $b) { /* test */
}
$y = 10 + /* test */ -2;

$a = function() {
};//end closure

$array = [
	function($a) {
	}, // phpcs:ignore Standard.Category.SniffName -- for reasons.
	'key' => function($b) {
	}, // comment.
	'key' => 'value', // phpcs:ignore Standard.Category.SniffName -- for reasons.
	'key' => 'value', // comment.
];

// Verify that multi-line control structure with comments and annotations are left alone.
for (
    $i = 0; /* Start */
    $i < 10; /* phpcs:ignore Standard.Category.SniffName -- for reasons. */
    $i++ // comment

) {}

if ( $condition === true // comment
    && $anotherCondition === false
) {}

$match = match($foo // comment
    && $bar
) {
    1 => 1, // comment
};

// Issue #560: Annotations should be reported separately and be non-auto-fixable as their meaning may change when moved.
$a = 1; //@codeCoverageIgnore
$b = 2; // @phpstan-ignore variable.undefined
$c = 3; //  @phpstan-ignore variable.undefined
$d = 4; //	@tabInsteadOfSpace

// Comments that include `@`, but are not recognized as annotations by this sniff.
$a = 1; // @ = add tag.
$b = 2; // Some comment. // @username
