<?php

// Intentional parse error (only empty tokens after T_STRING_CONCAT).
// This should be the only test in this file.
// Testing that the sniff is *not* triggered.

$parseError = 'String' .
