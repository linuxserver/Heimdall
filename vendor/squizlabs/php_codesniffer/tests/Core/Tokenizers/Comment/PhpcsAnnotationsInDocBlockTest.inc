<?php

/* testSingleLineDocIgnoreFileAnnotation */
/** @phpcs:ignoreFile */
function singleLineIgnoreFile() {}

/* testSingleLineDocIgnoreAnnotation */
/** @phpcs:ignore Stnd.Cat.SniffName -- With reason */
function singleLineIgnore() {}

/* testSingleLineDocDisableAnnotation */
/** @phpcs:disable Stnd.Cat.SniffName,Stnd.Other */
function singleLineDisable() {}

/* testSingleLineDocEnableAnnotationNoWhitespace */
/**@phpcs:enable Stnd.Cat.SniffName*/
function singleLineEnable() {}

/* testMultiLineDocIgnoreFileAnnotationAtStart */
/**
 * @phpcs:ignoreFile
 * Something.
 */
function MLStartIgnoreFile() {}

/* testMultiLineDocIgnoreAnnotationAtStart */
/**
 * @phpcs:ignore Stnd.Cat.SniffName
 * @tag
 */
function MLStartIgnore() {}

/* testMultiLineDocDisableAnnotationAtStart */
/**
 * @phpcs:disable Stnd.Cat.SniffName -- Ensure PHPCS annotations are also retokenized correctly.
 * Something.
 */
function MLStartDisable() {}

/* testMultiLineDocEnableAnnotationAtStart */
/**
 * @phpcs:enable Stnd.Cat,Stnd.Other
 *
 * @tag With description.
 */
function MLStartEnable() {}

/* testMultiLineDocIgnoreFileAnnotationInMiddle */
/**
 * Check tokenization of PHPCS annotations within docblocks.
 * @phpcs:ignoreFile
 *
 * Something.
 */
function MLMiddleIgnoreFile() {}

/* testMultiLineDocIgnoreAnnotationInMiddle */
/**
 * @tagBefore With Description
 *
 * @phpcs:ignore Stnd.Cat.SniffName
 * Something.
 */
function MLMiddleIgnore() {}

/* testMultiLineDocDisableAnnotationInMiddle */
/**
 * Check tokenization of PHPCS annotations within docblocks.
 *
 * @phpcs:disable Stnd.Cat.SniffName -- Ensure PHPCS annotations are also retokenized correctly.
 *
 * @tagAfter With Description
 */
function MLMiddleDisable() {}

/* testMultiLineDocEnableAnnotationInMiddle */
/**
 * Check tokenization of PHPCS annotations within docblocks.
 *
 * @phpcs:enable Stnd.Cat,Stnd.Other
 *
 * @tagAfter
 */
function MLMiddleEnable() {}

/* testMultiLineDocIgnoreFileAnnotationAtEnd */
/**
 * @tagBefore
 *
 * @phpcs:ignoreFile
 */
function MLEndIgnoreFile() {}

/* testMultiLineDocIgnoreAnnotationAtEnd */
/**
 * Check tokenization of PHPCS annotations within docblocks.
 *
 * @phpcs:ignore Stnd.Cat.SniffName
 */
function MLEndIgnore() {}

/* testMultiLineDocDisableAnnotationAtEnd */
/**
 * @tagBefore With Description.
 *
 * @phpcs:disable Stnd.Cat.SniffName -- Ensure PHPCS annotations are also retokenized correctly.
 */
function MLEndDisable() {}

/* testMultiLineDocEnableAnnotationAtEnd */
/**
 * Check tokenization of PHPCS annotations within docblocks.
 *
 * @phpcs:enable Stnd.Cat,Stnd.Other
 */
function MLEndEnable() {}
