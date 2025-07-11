<?php

/* testEmptyDocblock */
/**
 */

/* testMultilineDocblock */
/**
 * This is a multi-line docblock.
 *
 * With blank lines, stars, tags, and tag descriptions.
 *
 * @tagWithoutDescription
 *
 * @since 10.3
 * @deprecated 11.5
 *
 * @requires PHP 7.1 -- PHPUnit tag.
 *
 * @tag-with-dashes-is-suppported Description.
 * @tag_with_underscores          Description.
 *
 * @param string    $p1 Description 1.
 * @param int|false $p2 Description 2.
 *
 * @return void
 */
function base($p1, $p2) {}

/* testMultilineDocblockNoStars */
/****
    This is a multi-line docblock, but the lines are not marked with stars.
    Then again, the opener and closer have an abundance of stars.

    @since 10.3

    @param string    $p1 Description 1.
    @param int|false $p2 Description 2.

    @return void
 **/
function noStars($p1, $p2) {}

class Spaces {
    /* testMultilineDocblockIndented */
    /**
     * This is a multi-line indented docblock.
     *
     * With blank lines, stars, tags, and tag descriptions.
     *
     * @since 10.3
     * @deprecated 11.5
     *
     * @param string    $p1 Description 1.
     * @param int|false $p2 Description 2.
     *
     * @return void
     */
    function foo($p1, $p2) {}
}

/* testMultilineDocblockOpenerNotOnOwnLine */
/** Start of description
 * description continued.
 */

/* testMultilineDocblockCloserNotOnOwnLine */
/**
 * Start of description
 * description continued. */

/* testMultilineDocblockStarsNotAligned */
/**
* Start of description.
*   Line below this is missing a star.

   Text

    * Star indented.
    * Closer indented.
    */
