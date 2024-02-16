<?php

// Issue PHPCSStandards/PHP_CodeSniffer#226
// Intentional parse error (missing close parenthesis). Testing that the sniff is *not* triggered
// in this case and that no PHP 8.3+ deprecation notice is thrown.
for ($i = 0; $i < 10; $i++
