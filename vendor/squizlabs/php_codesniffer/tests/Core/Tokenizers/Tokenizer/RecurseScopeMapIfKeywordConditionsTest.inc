<?php

// Test for https://github.com/squizlabs/PHP_CodeSniffer/issues/497
/* testIfElseWithNestedCaseMissingBreak */
if ($foo === true):
    switch ($bar):
        case 1:
            echo 'one';
    endswitch;
/* testIfElseWithNestedCaseMissingBreakCloser */
else:
    echo 'other';
endif;
