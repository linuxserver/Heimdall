<?php
// This test case file MUST always start with a long open PHP tag set (with this comment) to prevent
// the tests running into the "first PHP open tag excepted" condition breaking the tests.
// Tests related to that "first PHP open tag excepted" condition should go in separate files.

// This test case file only deals with SHORT OPEN ECHO TAGS.
?>
<html>
<head>
<title><?= $title ?></title>
<script><?= $script; ?></script>
</head>
<body>
    <?=
        $body;
    ?>
    hello
    <?=
    $moreBody;
    ?>
    <?=  'one'; ?>
    <?=  'two';   ?>
    <?= 'three;' ?>
    <?= 'fourA'; echo 'fourB;'; ?>
    <?=  'fiveA'; echo 'fiveB;';   ?>

    <?=

        $start - $indent + $end;

    ?>
    <?=

    $blankLines;

        ?>

    <?=
    $closerNeedsOwnLine; ?>
    <?= $openerNeedsOwnLine;
    ?>

            <?=
        'hi';
            ?>

    <?=
'hi';
    ?>

    <a onClick="Javascript: set_hidden_field('<?= $link_offset - $num_per_page; ?>'); set_hidden_field('process_form', '0'); submit_form(); return false;">

            <strong><?=
            'foo';
            ?></strong>

</body>
</html>

<?= 'okay'; // Something. ?>
<?= 'too much space before close'; // Something.      ?>
<?= 'no space before close'; // Something.?>

<?= 'okay'; /* translators: okay */ ?>
<?= 'oops' /* translators: no space before close */?>
<?= 'oops' /* translators: too much space before close */      ?>

<?= 'okay'; // phpcs:ignore Standard.Category.Sniff -- reason. ?>
<?= 'too much space before close'; // phpcs:ignore Standard.Category.Sniff -- reason.    ?>
<?= 'no space before close'; // phpcs:ignore Standard.Category.Sniff -- reason.?>

<!--
Make sure the "content after opener" fixer does not leave trailing space behind.
-->
    <?=   $openerNeedsOwnLine;
    ?>

<!--
Make sure the "content before closer" fixer does not leave trailing space behind.
-->
    <?=
    $closerNeedsOwnLine;   ?>

<!--
Make sure the "content after closer" fixer does not leave trailing space behind.
-->
    <?=
    $closerNeedsOwnLine;
    ?>  </div>

    <?=
    $closerNeedsOwnLine;
    ?>  <?= $i; ?>

<!--
Safeguard fixing when there is no whitespace between the open tag and the contents.
-->
<?=$var ?>
    <?=$var;
    ?>

// Safeguard fixing when there is no whitespace between the close tag and the contents.
<?= $var?>
    <?=
    $var;?>

<!--
Make sure the fixer does not add stray new lines when there are consecutive PHP blocks.
-->
<?=
    'something'
    ?><?=
    'embedded';
    ?>  <?=
        'embedded';
    ?><?=
'embedded';
    ?>

<?php
// This test case file MUST always end with an unclosed long open PHP tag (with this comment) to prevent
// the tests running into the "last PHP closing tag excepted" condition breaking tests.
// Tests related to that "last PHP closing tag excepted" condition should go in separate files.
