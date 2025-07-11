// Although not the focus of this test, this is an intentional parse error when short_open_tag is on.
// This should be the only test in this file.
// Test that the sniff bails when short_open_tag is off and there is a token other than
// T_INLINE_HTML after the short open tag and before the close tag.

<?<?php
