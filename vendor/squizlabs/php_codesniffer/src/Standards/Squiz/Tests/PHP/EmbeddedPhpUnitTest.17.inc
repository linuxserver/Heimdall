<?php
// This test case file MUST always start with a long open PHP tag set (with this comment) to prevent
// the tests running into the "first PHP open tag excepted" condition breaking the tests.
// Tests related to that "first PHP open tag excepted" condition should go in separate files.
?>
<!-- The complete tag block will be ignored when it is the last (closed) tag block in a file, even when it would otherwise trigger errors. -->
<?=
    'too much indent and close tag not on own line'; ?>
