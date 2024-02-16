<?php

/* testNoParentheses */
$anonClass = new class {
    function __construct() {}
};

/* testReadonlyNoParentheses */
$anonClass = new readonly class {
    function __construct() {}
};

/* testNoParenthesesAndEmptyTokens */
$anonClass = new class // phpcs:ignore Standard.Cat
{
    function __construct() {}
};

/* testWithParentheses */
$anonClass = new class() {};

/* testReadonlyWithParentheses */
$anonClass = new readonly class() {
    function __construct() {}
};

/* testWithParenthesesAndEmptyTokens */
$anonClass = new class /*comment */
() {};
