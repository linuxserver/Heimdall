<?php
// This test case file MUST always start with a long open PHP tag set (with this comment) to prevent
// the tests running into the "first PHP open tag excepted" condition breaking the tests.
// Tests related to that "first PHP open tag excepted" condition should go in separate files.
?>
<!--
The "blank lines before close tag" check will not be executed when this is the last embedded PHP block.

Note: I am not sure what the reason is for this exception, but the sniff has had this behaviour for the longest time,
so this test documents the behaviour without an opinion on the correctness.
-->
<div>
<?php
echo 'There are a few too many blank lines below this line';





?>

</div>
<p>Some more content after the last PHP tag block.</p>
