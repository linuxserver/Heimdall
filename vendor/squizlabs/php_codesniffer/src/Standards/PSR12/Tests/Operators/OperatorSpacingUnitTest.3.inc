<?php
// There is logic in the sniff which protects itself from reading beyond the end
// of a file when an inline-if (ternary) operator is detected. This test case
// covers that scenario. This is an intentional parse error.
// Note that there is (intentionally) no trailing newline at the end of this file.
$var = true ?