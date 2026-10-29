# Input Contract

This document defines the template syntax and PHP value shapes accepted by
`UriTemplate::expand()`. Use it when variables come from application data,
request data, or another system and you need predictable validation behavior.

`UriTemplate::expand()` expects an RFC 6570 URI template and an array of
variables. Invalid templates or unsupported referenced variable values throw
`InvalidArgumentException`.

## Variables

Template variable names may contain ASCII letters, ASCII digits, `_`, valid
percent-encoded triplets, and dot separators. Percent-encoded triplets are part
of the variable name and are not decoded for variable lookup. Dot separators may
separate name parts, but names cannot start with a dot, end with a dot, or
contain empty dot-separated parts.

Array keys in the variables argument must match template variable names exactly:

```php
UriTemplate::expand('/users/{user.id}', [
    'user.id' => 123,
]);
```

RFC 6570 variable names may consist entirely of digits, such as `{0}`. PHP
coerces array keys that are canonical decimal integer strings to integers, so
`['0' => 'x']` and `[0 => 'x']` are the same key. An all-numeric variable name
is matched against that integer key and expands normally. A name that is not
a canonical decimal integer string, such as `{01}`, is not coerced; it must
appear under the exact string key `'01'`. Coercion follows the platform
integer range: a numeric name beyond `PHP_INT_MAX`, such as `{2147483648}` on
32-bit PHP, stays a string key and must match exactly.

## Values

Supported variable values are:

- `null`, which is treated as undefined and omitted
- scalars, which are cast to strings before expansion
- objects with `__toString()`
- dense zero-indexed lists containing scalar or stringable values
- maps containing scalar or stringable values
- nested arrays in maps for exploded query-style expansions, with scalar leaves

Referenced variable values are detached from the variables array and formed
before expansion begins. Definedness is bound and raw values are read before
any `__toString()` method runs, so an object cannot change another referenced
variable through a PHP reference. Stringable objects are then converted to
strings once per value position, in template order, and scalars are converted
to their expansion strings, so a repeated variable keeps a static value even
when a `__toString()` method changes the float precision or the locale
mid-expansion. String values and map keys are validated as UTF-8 while values
are formed, so value errors surface in member order. Exceptions thrown by
`__toString()` propagate unchanged; they surface while values are formed, after
template syntax validation and before any part of the URI is produced, and may
preempt validation errors for values formed later.

Booleans expand as `1` and `0` at every nesting level.

Floats expand using PHP's float-to-string conversion with the decimal separator
normalized to `.`, so output does not depend on the process locale, including on
PHP 7.4 where the plain cast honors `LC_NUMERIC`. The conversion rounds to the
`precision` ini setting (`14` by default), so `0.1 + 0.2` expands as `0.3`, and
uses scientific notation, such as `1.0E+20`, when fixed notation would need more
significant digits than `precision` allows or the magnitude is below `0.0001`.
The `+` in an exponent is percent-encoded as `%2B` except under reserved and
fragment expansion. Non-finite floats are not supported: `INF`, `-INF`, and
`NAN` throw `InvalidArgumentException`. Format floats yourself, for example
with `number_format()` or `sprintf()`, when a specific decimal representation
is required.

An empty string is a defined value and is expanded. An empty array is treated as
undefined and omitted. Missing variables and variables set to `null` are treated
as undefined and omitted.

`null` members inside lists and maps are treated as undefined members and
omitted, like top-level `null`. A map whose members are all `null` is treated
as undefined and omitted, like an empty array. A non-empty list whose members
are all `null` is a defined variable with no defined members: the operator
first string is still emitted, named forms render the name with the operator's
empty-value form, and prefix modifiers are rejected as for any other list. A
list or map with at least one defined member is a defined, non-empty value: in
named non-exploded expansions, `=` follows the name even when every member
expands to the empty string, so `{;l}` with `['l' => ['']]` expands to `;l=`.
See the [conformance
notes](uri-template-usage.md#specification-conformance-notes) for how the
all-`null` rule maps to RFC 6570.

Arrays whose keys are exactly `0` through `n-1` in ascending insertion order
expand as lists. All other arrays, including reordered, sparse, and mixed-key
arrays, expand as maps. Map and list order follows PHP array insertion order.

Because these arrays always expand as lists, a map whose member names are
exactly the integers `0` through `n-1` cannot be expressed as a single variable
value; such an array expands as a list. Reference each member with its own
numeric variable name, such as `{?0,1}`, when the expanded URI must carry those
names.

Unsupported values include resources, closures, non-stringable objects,
non-finite floats, recursive arrays, arrays nested more than 64 levels deep, and
nested arrays outside exploded query-style expansions. Unsupported values are
validated only when the template references that variable.

Variable values must be valid UTF-8. Invalid byte sequences throw
`InvalidArgumentException`. Encode binary data, for example with base64, before
expansion.

Values are expanded as given, without Unicode normalization. Canonically
equivalent inputs expand to different URIs: the decomposed value `"e\u{0301}"`
expands to `e%CC%81`, while the precomposed value `"\u{00E9}"` expands to
`%C3%A9`. Normalize user-entered text, for example with ext-intl's
`Normalizer::normalize($value, Normalizer::FORM_C)`, before expansion. See the
[conformance notes](uri-template-usage.md#specification-conformance-notes) for
the RFC 6570 section 1.6 background.

## Prefix Modifiers

Prefix modifiers select a character prefix from scalar and stringable values:

```php
UriTemplate::expand('/dictionary/{term:1}/{term}', ['term' => 'cat']);

// /dictionary/c/cat
```

Prefix lengths must be decimal integers from `1` through `9999`. Leading zeroes,
zero, negative numbers, non-numeric values, and values greater than `9999` are
invalid.

Prefix modifiers are not valid on composite values such as lists or maps, and a
varspec cannot combine prefix and explode modifiers.

Prefix length counts Unicode characters and existing percent-encoded
characters, not bytes. For example, `%2F` counts as one character before the
selected prefix is encoded for the expression type, and consecutive
percent-encoded triplets that encode one Unicode code point in UTF-8, such as
`%C3%A9`, also count as one character. A combining mark is its own character,
so a prefix can split a decomposed character sequence: `{x:1}` with the
decomposed value `"e\u{0301}f"` selects only `e`, dropping the combining
accent. Normalize values to NFC before expansion to keep user-perceived
characters intact. Values must be valid UTF-8, as described in
[values](#values).

## Nested Query Arrays

Nested arrays are accepted only as values inside map variables expanded with an
exploded query or query-continuation expression, such as `{?filter*}` or
`{&filter*}`.

Nested query arrays must have scalar leaves and can be nested at most 64 levels
deep below the top-level map. Recursive arrays, arrays nested more deeply, and
nested objects are rejected. Nested `null` values are treated as undefined
members and omitted. Omission happens before member validation, so the keys of
omitted `null` members are not validated. Stringable objects are accepted as
direct map values, but not as leaves inside nested query arrays.

Nested query arrays use RFC 3986 query encoding with PHP bracket syntax:

```php
UriTemplate::expand('/search{?filter*}', [
    'filter' => [
        'author' => [
            'name' => 'Ada Lovelace',
        ],
    ],
]);

// /search?author%5Bname%5D=Ada%20Lovelace
```

Empty nested arrays are omitted from exploded query expansions. Empty scalar
values are preserved.

Empty-string keys at nested levels of a nested query array produce PHP append
syntax (`a%5B%5D=v`), which does not round-trip the key.

A top-level empty-string key whose value is a non-empty nested array produces
bracket syntax with an empty root name (`%5Ba%5D=v`, decoded `[a]=v`), which
drops the root pair name and does not round-trip. Deeper nesting keeps the
empty root name (`%5Ba%5D%5Bb%5D=v`). This applies to `{?var*}` and `{&var*}`
alike. A top-level empty-string key with a scalar value keeps the empty pair
name in the output (`=v`), and one with an empty nested array is omitted, like
other empty nested arrays.

## Validation Errors

Literal text outside expressions must already be valid URI template literal
text. For example, use `/search%20terms/{id}` instead of `/search terms/{id}`.

`InvalidArgumentException` is thrown for invalid template syntax, unsupported
operators, invalid variable names, invalid modifiers, invalid literal text, and
unsupported shapes for variables referenced by the template.

Errors for invalid list and map members identify the member by path, such as
`x[1]` or `filter[author][name]`. Member paths and expression text use
diagnostic escaping. C0, DEL, and C1 controls are rendered as `\xHH`. If
UTF-8-aware diagnostic escaping fails, every byte outside printable ASCII is
rendered in the same form. The result is diagnostic text, not a reversible
encoding.

`RuntimeException` is thrown when the PCRE engine fails while processing a
template, for example when an extremely long variable name or very large
literal text exhausts a PCRE resource limit. This indicates an environment
limit, not invalid input; the threshold depends on the PCRE build and
`pcre.jit` configuration.

Exceptions thrown by a value object's `__toString()` method propagate unchanged;
they are not converted to `InvalidArgumentException`. They surface while values
are formed, once per expansion, after template syntax validation and before any
part of the URI is produced.

Catch `InvalidArgumentException` if templates or values come from outside your
application:

```php
try {
    $uri = UriTemplate::expand($template, $variables);
} catch (\InvalidArgumentException $e) {
    // Reject or log the invalid template input.
}
```

Templates should generally be application-controlled. If templates come from
users or remote systems, treat them as policy input and review them before
expansion.

Values expanded with `{+var}` and `{#var}` keep the URI meaning of reserved
characters, so untrusted values can inject scheme, authority, path, query, and
fragment structure into the expanded URI. Use simple expansion for untrusted
values, or validate them before expansion.

## Related

- [URI Template Usage](uri-template-usage.md)
- [Upgrade Guide](../UPGRADING.md)
- [Changelog](../CHANGELOG.md)
