<?php

// Intentional parse error/live coding.
// Ensuring that the sniff ignores unfinished function declarations (missing close parenthesis for import use).
// This must be the only test in this file.
$closure = function (string $param) use ($var
