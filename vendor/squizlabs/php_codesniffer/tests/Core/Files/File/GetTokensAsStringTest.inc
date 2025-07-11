<?php

/* testNamespace */
namespace Foo\Bar\Baz;

/* testUseWithComments */
use Foo /*comment*/ \ Bar
	// phpcs:ignore Stnd.Cat.Sniff --	 For reasons.
	\ Bah;

$cl = function() {
    /* testCalculation */
    return 1 + 2 +
        // Comment.
        3 + 4
        + 5 + 6 + 7 > 20;
}

/* testEchoWithTabs */
echo 'foo',
	'bar'	,
		'baz';

/* testEndOfFile */
echo   $foo;
