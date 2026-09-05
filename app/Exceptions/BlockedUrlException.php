<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a server-side fetch is refused by the SSRF guard: the URL uses
 * a scheme other than http(s), its host cannot be resolved, or it (or a
 * redirect hop) resolves to a private or reserved address.
 */
class BlockedUrlException extends RuntimeException
{
}
