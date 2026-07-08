<?php

// This should be the only test in the file.
// Ref: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/1216

/* testLiveCoding */
$ternary = true
    # This must be a slash or hash comment and the next line must **NOT** have any indentation for the PHP 8.5 deprecation notice to occur.
? //comment.
