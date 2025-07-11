<?php

/* testEmptyBlockCommentNoWhiteSpace */
/**/

/* testEmptyDocblockNoWhiteSpace */
/***/

/* testEmptyDocblockWithWhiteSpace */
/** */

/* testSingleLineDocblockNoTag */
/** Just some text */
$var = doSomething();

/* testSingleLineDocblockWithTag1 */
/** @var \SomeClass[] $var */
$var = doSomething();

/* testSingleLineDocblockWithTag2 */
/** @var $var \SomeClass[] */
$var = doSomething();

/* testSingleLineDocblockWithTag3 */
/** @see Something::Else */
$var = doSomething();
