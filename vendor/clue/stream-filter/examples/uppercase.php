<?php

// $ echo test | php examples/uppercase.php

require __DIR__ . '/../vendor/autoload.php';

Clue\StreamFilter\append(STDIN, 'strtoupper');

fpassthru(STDIN);
