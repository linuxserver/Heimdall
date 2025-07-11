<?php
$x = 'My '.'string';
$x = 'My '. 1234;
$x = 'My '.$y.' test';

echo $data['my'.'index'];
echo $data['my'. 4];
echo $data['my'.$x];
echo $data[$x.$y.'My'.'String'];

$code = '$actions = array();'."\n";
$code = "$actions = array();"."\n";

// No errors for these because they are needed in some cases.
$code = ' ?'.'>';
$code = '<'.'?php ';

$string = 'This is a really long string. '
        . 'It is being used for errors. '
        . 'The message is not translated.';

$shouldBail = 1 + 1;

$shouldNotTrigger = 'My' . /* comment */ 'string';
$shouldNotTrigger = 'My' /* comment */ . 'string';

// phpcs:set Generic.Strings.UnnecessaryStringConcat allowMultiline true
$string = 'Multiline strings are allowed '
        . 'when setting is enabled.';
// phpcs:set Generic.Strings.UnnecessaryStringConcat allowMultiline false

// phpcs:set Generic.Strings.UnnecessaryStringConcat error false
$throwWarning = 'My' . 'string';
// phpcs:set Generic.Strings.UnnecessaryStringConcat error true
