<?=
    'This line has the wrong indent'
    . ' and the close tag is not on its own line, but this will not be fixed'
    . ' as multi-line code blocks for the first PHP open tag will always be ignored'; ?>

<?php
// Placeholder just to ensure the above is ignored solely because it is the first open tag, not
// because the conditions related to the _last_ open tag kick in.
