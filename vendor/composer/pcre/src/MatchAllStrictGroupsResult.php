<?php

/*
 * This file is part of composer/pcre.
 *
 * (c) Composer <https://github.com/composer>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Composer\Pcre;

final class MatchAllStrictGroupsResult
{
    /**
     * An array of match group => list of matched strings
     *
     * @readonly
     * @var array<int|string, list<string>>
     */
    public $matches;

    /**
     * @readonly
     * @var 0|positive-int
     */
    public $count;

    /**
     * @readonly
     * @var bool
     */
    public $matched;

    /**
     * @param 0|positive-int $count
     * @param array<array<string>> $matches
     */
    public function __construct(int $count, array $matches)
    {
        $this->matches = $matches;
        $this->matched = (bool) $count;
        $this->count = $count;
    }
}
