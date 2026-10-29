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

enum State: string
{
    case Init = 'init';
    case Scheme = 'scheme';
    case Authority = 'authority';
    case Username = 'user';
    case Password = 'pass';
    case Host = 'host';
    case Port = 'port';
    case Path = 'path';
    case Query = 'query';
    case Fragment = 'fragment';
    case Done = 'done';
}
