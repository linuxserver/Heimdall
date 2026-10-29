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

namespace League\Uri\UriTemplate;

enum ExtractionErrorReason
{
    // Matching
    case LiteralMismatch;
    case PrefixMismatch;
    case VariableMismatch;
    case TypeMismatch;
    case StringMismatch;
    case ListMismatch;

    // Extraction
    case MalformedValue;
    case PrefixLengthExceeded;
    case UnmatchedContent;
    case UndeterminedDelimiter;
    case UnsupportedOperation;

    // Reconciliation
    case ReconciliationFailed;
    case MissingVariables;
}
