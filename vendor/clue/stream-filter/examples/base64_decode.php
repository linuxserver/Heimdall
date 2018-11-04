<?php

// $ echo test | php examples/base64_encode.php | php examples/base64_decode.php

require __DIR__ . '/../vendor/autoload.php';

// decoding requires buffering in chunks of 4 bytes each
$buffer = '';
Clue\StreamFilter\append(STDIN, function ($chunk = null) use (&$buffer) {
    if ($chunk === null) {
        if (strlen($buffer) % 4 !== 0) {
            throw new \UnexpectedValueException('Invalid length');
        }
        $chunk = $buffer;
    } else {
        $buffer .= $chunk;
        $len = strlen($buffer) - (strlen($buffer) % 4);
        $chunk = (string)substr($buffer, 0, $len);
        $buffer = (string)substr($buffer, $len);
    }

    $ret = base64_decode($chunk, true);
    if ($ret === false) {
        throw new \UnexpectedValueException('Not a valid base64 encoded chunk');
    }
    return $ret;
}, STREAM_FILTER_READ);

fpassthru(STDIN);
