<?php
/**
 * Test fixture.
 *
 * Note: yes, there is a parse error in this file (namespace name is invalid).
 * No, that's not a problem.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\ExpandSniffDirectoryTest
 */

namespace MyStandard\.hidden;

use MyStandard\DummySniff;

final class HiddenDirShouldBeIgnoredSniff extends DummySniff {}
