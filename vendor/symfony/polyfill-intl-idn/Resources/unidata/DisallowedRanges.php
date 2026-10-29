<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Intl\Idn\Resources\unidata;

/**
 * @internal
 */
final class DisallowedRanges
{
    /**
     * @param int $codePoint
     *
     * @return bool
     */
    public static function inRange($codePoint)
    {
        if ($codePoint >= 128 && $codePoint <= 159) {
            return true;
        }

        if ($codePoint >= 3676 && $codePoint <= 3712) {
            return true;
        }

        if ($codePoint >= 3808 && $codePoint <= 3839) {
            return true;
        }

        if ($codePoint >= 4059 && $codePoint <= 4095) {
            return true;
        }

        if ($codePoint >= 4256 && $codePoint <= 4293) {
            return true;
        }

        if ($codePoint >= 6863 && $codePoint <= 6911) {
            return true;
        }

        if ($codePoint >= 11870 && $codePoint <= 11903) {
            return true;
        }

        if ($codePoint >= 55296 && $codePoint <= 57343) {
            return true;
        }

        if ($codePoint >= 57344 && $codePoint <= 63743) {
            return true;
        }

        if ($codePoint >= 64218 && $codePoint <= 64255) {
            return true;
        }

        if ($codePoint >= 64976 && $codePoint <= 65007) {
            return true;
        }

        if ($codePoint >= 65630 && $codePoint <= 65663) {
            return true;
        }

        if ($codePoint >= 65953 && $codePoint <= 65999) {
            return true;
        }

        if ($codePoint >= 66046 && $codePoint <= 66175) {
            return true;
        }

        if ($codePoint >= 66518 && $codePoint <= 66559) {
            return true;
        }

        if ($codePoint >= 67005 && $codePoint <= 67071) {
            return true;
        }

        if ($codePoint >= 67515 && $codePoint <= 67583) {
            return true;
        }

        if ($codePoint >= 67760 && $codePoint <= 67807) {
            return true;
        }

        if ($codePoint >= 67904 && $codePoint <= 67967) {
            return true;
        }

        if ($codePoint >= 68256 && $codePoint <= 68287) {
            return true;
        }

        if ($codePoint >= 68528 && $codePoint <= 68607) {
            return true;
        }

        if ($codePoint >= 68681 && $codePoint <= 68735) {
            return true;
        }

        if ($codePoint >= 68922 && $codePoint <= 69215) {
            return true;
        }

        if ($codePoint >= 69298 && $codePoint <= 69372) {
            return true;
        }

        if ($codePoint >= 69514 && $codePoint <= 69551) {
            return true;
        }

        if ($codePoint >= 70210 && $codePoint <= 70271) {
            return true;
        }

        if ($codePoint >= 70517 && $codePoint <= 70655) {
            return true;
        }

        if ($codePoint >= 70874 && $codePoint <= 71039) {
            return true;
        }

        if ($codePoint >= 71134 && $codePoint <= 71167) {
            return true;
        }

        if ($codePoint >= 71370 && $codePoint <= 71423) {
            return true;
        }

        if ($codePoint >= 71495 && $codePoint <= 71679) {
            return true;
        }

        if ($codePoint >= 71740 && $codePoint <= 71839) {
            return true;
        }

        if ($codePoint >= 72026 && $codePoint <= 72095) {
            return true;
        }

        if ($codePoint >= 72458 && $codePoint <= 72703) {
            return true;
        }

        if ($codePoint >= 72887 && $codePoint <= 72959) {
            return true;
        }

        if ($codePoint >= 73130 && $codePoint <= 73439) {
            return true;
        }

        if ($codePoint >= 73562 && $codePoint <= 73647) {
            return true;
        }

        if ($codePoint >= 74650 && $codePoint <= 74751) {
            return true;
        }

        if ($codePoint >= 75076 && $codePoint <= 77711) {
            return true;
        }

        if ($codePoint >= 78934 && $codePoint <= 82943) {
            return true;
        }

        if ($codePoint >= 83527 && $codePoint <= 92159) {
            return true;
        }

        if ($codePoint >= 93072 && $codePoint <= 93759) {
            return true;
        }

        if ($codePoint >= 93851 && $codePoint <= 93951) {
            return true;
        }

        if ($codePoint >= 94112 && $codePoint <= 94175) {
            return true;
        }

        if ($codePoint >= 101590 && $codePoint <= 101631) {
            return true;
        }

        if ($codePoint >= 101641 && $codePoint <= 110575) {
            return true;
        }

        if ($codePoint >= 111356 && $codePoint <= 113663) {
            return true;
        }

        if ($codePoint >= 113828 && $codePoint <= 118527) {
            return true;
        }

        if ($codePoint >= 118724 && $codePoint <= 118783) {
            return true;
        }

        if ($codePoint >= 119366 && $codePoint <= 119487) {
            return true;
        }

        if ($codePoint >= 119673 && $codePoint <= 119807) {
            return true;
        }

        if ($codePoint >= 121520 && $codePoint <= 122623) {
            return true;
        }

        if ($codePoint >= 122667 && $codePoint <= 122879) {
            return true;
        }

        if ($codePoint >= 122990 && $codePoint <= 123022) {
            return true;
        }

        if ($codePoint >= 123024 && $codePoint <= 123135) {
            return true;
        }

        if ($codePoint >= 123216 && $codePoint <= 123535) {
            return true;
        }

        if ($codePoint >= 123648 && $codePoint <= 124111) {
            return true;
        }

        if ($codePoint >= 124154 && $codePoint <= 124895) {
            return true;
        }

        if ($codePoint >= 125143 && $codePoint <= 125183) {
            return true;
        }

        if ($codePoint >= 125280 && $codePoint <= 126064) {
            return true;
        }

        if ($codePoint >= 126133 && $codePoint <= 126208) {
            return true;
        }

        if ($codePoint >= 126270 && $codePoint <= 126463) {
            return true;
        }

        if ($codePoint >= 126652 && $codePoint <= 126703) {
            return true;
        }

        if ($codePoint >= 126706 && $codePoint <= 126975) {
            return true;
        }

        if ($codePoint >= 127406 && $codePoint <= 127461) {
            return true;
        }

        if ($codePoint >= 127590 && $codePoint <= 127743) {
            return true;
        }

        if ($codePoint >= 129202 && $codePoint <= 129279) {
            return true;
        }

        if ($codePoint >= 129995 && $codePoint <= 130031) {
            return true;
        }

        if ($codePoint >= 130042 && $codePoint <= 131069) {
            return true;
        }

        if ($codePoint >= 173792 && $codePoint <= 173823) {
            return true;
        }

        if ($codePoint >= 192094 && $codePoint <= 194559) {
            return true;
        }

        if ($codePoint >= 195102 && $codePoint <= 196605) {
            return true;
        }

        if ($codePoint >= 205744 && $codePoint <= 262141) {
            return true;
        }

        if ($codePoint >= 262144 && $codePoint <= 327677) {
            return true;
        }

        if ($codePoint >= 327680 && $codePoint <= 393213) {
            return true;
        }

        if ($codePoint >= 393216 && $codePoint <= 458749) {
            return true;
        }

        if ($codePoint >= 458752 && $codePoint <= 524285) {
            return true;
        }

        if ($codePoint >= 524288 && $codePoint <= 589821) {
            return true;
        }

        if ($codePoint >= 589824 && $codePoint <= 655357) {
            return true;
        }

        if ($codePoint >= 655360 && $codePoint <= 720893) {
            return true;
        }

        if ($codePoint >= 720896 && $codePoint <= 786429) {
            return true;
        }

        if ($codePoint >= 786432 && $codePoint <= 851965) {
            return true;
        }

        if ($codePoint >= 851968 && $codePoint <= 917501) {
            return true;
        }

        if ($codePoint >= 917536 && $codePoint <= 917631) {
            return true;
        }

        if ($codePoint >= 917632 && $codePoint <= 917759) {
            return true;
        }

        if ($codePoint >= 918000 && $codePoint <= 983037) {
            return true;
        }

        if ($codePoint >= 983040 && $codePoint <= 1048573) {
            return true;
        }

        if ($codePoint >= 1048576 && $codePoint <= 1114109) {
            return true;
        }

        return false;
    }
}
