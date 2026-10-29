<?php
namespace enshrined\svgSanitize\Exceptions;

/**
 * Thrown internally when a PCRE call fails (for example by hitting the backtrack
 * or recursion limit). `Sanitizer::sanitize()` catches it and returns `false`, so
 * a failed regex can never let half-cleaned output through.
 */
class RegexException extends \RuntimeException
{
}
