<?php

/* testNoReplacementNeeded */
    $a = 10; // Comment not containing any tabs.

/* testTabIndentation */
		echo $foo;

/* testMixedIndentation */
	  echo $foo;

/* testInlineSingleTab */
$a = 'tab	separated';

/* testInlineSingleTabBetweenEachWord */
$a = "tab	$between	each	word";

/* testInlineMultiTab */
$a = <<<EOD
tab			separated
EOD;

/* testInlineMultipleTabsBetweenEachWord */
$a = <<<'EOD'
tab		between				each			word
EOD;

/* testInlineMixedSpacesTabs */
$a = 'tab 	  		separated';

/* testInlineMixedSpacesTabsBetweenEachWord */
$a = "tab	 $between  	each	   	word";

/* testInlineSize1 */
// -123	With tabwidth 4, the tab size should be 1.

/* testInlineSize2 */
/* -12	With tabwidth 4, the tab size should be 2. */

/* testInlineSize3 */
/**
 * -1	With tabwidth 4, the tab size should be 3.
 */

/* testInlineSize4 */
// -	With tabwidth 4, the tab size should be 4.
