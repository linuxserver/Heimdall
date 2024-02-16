<?php
// This test case file MUST always start with a long open PHP tag set (with this comment) to prevent
// the tests running into the "first PHP open tag excepted" condition breaking the tests.
// Tests related to that "first PHP open tag excepted" condition should go in separate files.
?>
<!--
The below test safeguards that if there is just an unclosed PHP open tag at the end of a file,
the sniff bows out as there is nothing to do, even when there are multiple new lines after the open tag.
-->
<?=




