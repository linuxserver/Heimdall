<?php
/**
 * @see something
 */
if (@in_array($array, $needle))
{
    echo '@';
}

$hasValue = @in_array(haystack: $array, needle: $needle);

// phpcs:set Generic.PHP.NoSilencedErrors error true
$fileContent = @file_get_contents('non_existent_file.txt');
// phpcs:set Generic.PHP.NoSilencedErrors error false

echo @$undefinedVariable;
