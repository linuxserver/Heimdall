<?php

// Intentional parse error/live coding.
// Ensuring that the sniff does not get into a fixer conflict with itself for unfinished closure declarations
// (missing close parenthesis for import use).
// This must be the only test in this file.
$closure = function (string $param) use ($var
