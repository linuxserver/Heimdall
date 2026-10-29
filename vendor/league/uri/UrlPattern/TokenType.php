<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\UrlPattern;

enum TokenType
{
    case Open;
    case Close;
    case Regex;
    case Name;
    case Char;
    case EscapedChar;
    case OtherModifier;
    case InvalidChar;
    case Asterisk;
    case End;
}
