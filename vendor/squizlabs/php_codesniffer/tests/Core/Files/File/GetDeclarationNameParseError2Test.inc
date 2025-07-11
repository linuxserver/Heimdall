<?php

/* testLiveCoding */
// Intentional parse error/live coding. This must be the only test in the file.
// Safeguarding that the utility method does not confuse the `string` type with a function name.
$closure = function (string $param) use ($var
