<!--
Test file safeguarding that if a file only contains single line embedded statements with a complete set of short open tags,
these will always be examined and that the special casing of the first/last PHP block does not apply.
-->
<?=   $paragraph1;   ?>
    <?=   $paragraph2 ?>
<?=   $paragraph3;   ?>
