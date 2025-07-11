// Test warning for when short_open_tag is off.

Some content <? echo $var; ?> Some more content

// Test multi-line.
Some content <?
echo $var;
?> Some more content

// Make sure skipping works.
Some content <?
echo '<?';
?> Some more content

// Test snippet clipping with a line that has more than 40 characters after the PHP open tag.
Some content <? echo $var; ?> Some longer content to trigger snippet clipping

// Only recognize closing tag after opener.
// The test below must be the last test in the file because there must be no PHP close tag after it.
Some?> content <?
