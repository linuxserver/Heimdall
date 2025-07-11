<?

// This test case file MUST always start with a open PHP tag set (with this comment) to prevent
// the tests running into the "first PHP open tag excepted" condition breaking the tests.
// Tests related to that "first PHP open tag excepted" condition should go in separate files.

// This test case file only deals with SHORT OPEN TAGS.

?>

<?
/* Contrary to the long open tag token, the short open tag token does not contain a space after the
   tag and the sniff should handle it accordingly. The test below protects against regressions
   related to https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/588. */
?>
<? echo 'one space after short open tag'; ?>

<?  echo 'two spaces after short open tag'; ?>

<?echo 'without space after short open tag'; ?>

<?
// This test case file MUST always end with an unclosed open PHP tag (with this comment) to prevent
// the tests running into the "last PHP closing tag excepted" condition breaking tests.
// Tests related to that "last PHP closing tag excepted" condition should go in separate files.
