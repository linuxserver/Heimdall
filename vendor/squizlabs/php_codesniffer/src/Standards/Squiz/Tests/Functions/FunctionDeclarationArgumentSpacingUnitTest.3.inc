<?php

// Intentional parse error/live coding.
// Ensuring that the sniff ignores unfinished function declarations (missing close parenthesis).
// This must be the only test in this file.
$closure = function (string $paramA, int &...$paramB,
