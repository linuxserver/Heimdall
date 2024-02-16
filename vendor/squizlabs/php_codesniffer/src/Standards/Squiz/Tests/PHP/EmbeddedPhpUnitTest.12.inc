<?php
// This test case file MUST always start with a long open PHP tag set (with this comment) to prevent
// the tests running into the "first PHP open tag excepted" condition breaking the tests.
// Tests related to that "first PHP open tag excepted" condition should go in separate files.
?>
<!--
The below test safeguards that if the last tag block is unclosed and has content, the sniff *will* kick in.
-->
<?php


    echo $incorrect_indent_and_too_many_blank_lines_above;
