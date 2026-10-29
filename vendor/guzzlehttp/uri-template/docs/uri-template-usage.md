# URI Template Usage

This guide shows the supported URI template expansion forms and common value
shapes for `UriTemplate::expand()`. For the complete rules for variable names,
accepted PHP values, empty values, and validation errors, see the
[input contract](input-contract.md).

```php
use GuzzleHttp\UriTemplate\UriTemplate;

$uri = UriTemplate::expand('/users/{id}{?tab}', [
    'id' => 123,
    'tab' => 'settings',
]);

// /users/123?tab=settings
```

The first argument is an RFC 6570 URI template. The second argument is an array
of [variables](input-contract.md#variables) to use during expansion. Variable
map keys must match template variable names exactly.

This package supports RFC 6570 levels 1 through 4 for the standard operators
listed below, including prefix and explode modifiers. RFC 6570 reserved
extension operators are not supported and are rejected.

| Operator | Expansion | Status |
|----------|-----------|--------|
| none | Simple string expansion | Supported |
| `+` | Reserved string expansion | Supported |
| `#` | Fragment expansion | Supported |
| `.` | Label expansion | Supported |
| `/` | Path segment expansion | Supported |
| `;` | Path-style parameter expansion | Supported |
| `?` | Form-style query expansion | Supported |
| `&` | Form-style query continuation | Supported |
| `=`, `,`, `!`, `@`, `\|` | Reserved extension operators | Unsupported, rejected |

Simple expansion encodes reserved URI delimiters in variable values:

```php
UriTemplate::expand('/files/{path}', ['path' => 'a/b']);

// /files/a%2Fb
```

Use reserved expansion (`{+var}`) when the variable value intentionally contains
URI delimiters that should remain delimiters in the expanded URI:

```php
UriTemplate::expand('/files/{+path}', ['path' => 'a/b']);

// /files/a/b
```

Values expanded with `{+var}` and `{#var}` keep the URI meaning of reserved
characters, so untrusted values can inject scheme, authority, path, query, and
fragment structure into the expanded URI. Use simple expansion for untrusted
values, or validate them before expansion.

Fragment expansion (`{#var}`) prefixes the expanded value with `#` when the
variable is defined:

```php
UriTemplate::expand('/docs{#section}', ['section' => 'part 1']);

// /docs#part%201
```

Other operators help build common URI components:

```php
UriTemplate::expand('www{.domain*}', [
    'domain' => ['example', 'com'],
]);

// www.example.com

UriTemplate::expand('/users{/id}', ['id' => 123]);

// /users/123

UriTemplate::expand('/users{;role}', ['role' => 'admin']);

// /users;role=admin

UriTemplate::expand('/search{?q,page}', [
    'q' => 'uri templates',
    'page' => 2,
]);

// /search?q=uri%20templates&page=2

UriTemplate::expand('/search?fixed=yes{&page}', ['page' => 2]);

// /search?fixed=yes&page=2
```

Prefix modifiers select a prefix of a scalar value:

```php
UriTemplate::expand('/dictionary/{term:1}/{term}', ['term' => 'cat']);

// /dictionary/c/cat
```

Explode modifiers expand lists and maps item by item:

```php
UriTemplate::expand('/tags{/tag*}', [
    'tag' => ['red', 'green', 'blue'],
]);

// /tags/red/green/blue

UriTemplate::expand('/tags{?tag*}', [
    'tag' => ['red', 'green'],
]);

// /tags?tag=red&tag=green
```

[Arrays whose keys are exactly `0` through `n-1`](input-contract.md#values) in
ascending insertion order expand as lists. All other arrays, including
reordered, sparse, and mixed-key arrays, expand as maps. Map order follows PHP
array insertion order:

```php
UriTemplate::expand('/search{?filter*}', [
    'filter' => [
        'status' => 'open',
        'sort' => 'created',
    ],
]);

// /search?status=open&sort=created
```

If a sparse array is intended to expand as a list, reindex it before expansion:

```php
$tag = [1 => 'red', 2 => 'green'];

UriTemplate::expand('/tags{/tag*}', ['tag' => array_values($tag)]);

// /tags/red/green
```

Nested arrays are supported only for exploded query-style map expansions, such
as `{?var*}` and `{&var*}`. They use RFC 3986 query encoding with PHP bracket
syntax. See [nested query arrays](input-contract.md#nested-query-arrays) for the
full contract:

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

Empty nested arrays are omitted from exploded query expansions. `null` members
inside lists and maps are treated as undefined members and omitted, like
top-level `null`. A map whose members are all `null` is treated as undefined,
while a non-empty list whose members are all `null` is a defined variable with
no defined members, as described in the [specification conformance
notes](#specification-conformance-notes).

Variable values are encoded during expansion according to the expression type.
Existing percent-encoded triplets in reserved and fragment expansions are
preserved, while simple expansion encodes `%` as `%25`:

```php
UriTemplate::expand('{id}', ['id' => 'admin%2F']);

// admin%252F

UriTemplate::expand('{+id}', ['id' => 'admin%2F']);

// admin%2F
```

## Specification Conformance Notes

Prefix modifiers count existing percent-encoded characters as one character.
RFC 6570 section 3.2.1 defines a prefix as the "first max-length characters of
the decoded value" and forbids splitting a multi-octet or percent-encoded
sequence, so a run of consecutive percent-encoded triplets that encodes one
Unicode code point in UTF-8 also counts as one character. For example, `{id:1}`
with the value `admin%2F` selects `a`, and `{+id:1}` with the value
`%C3%A9clair` selects `%C3%A9`. Triplets that do not encode a single code
point, such as a lone lead octet, count as one character each. Several other
implementations count raw characters instead and can split percent-encoded
triplets, so prefixed expansions of values containing triplets can differ
between libraries.

Invalid templates and unsupported variable values throw
`InvalidArgumentException`. RFC 6570 section 3 allows a template processor to
recover from an error by copying the offending expression into the result, but
describes such output as "only intended for diagnostic use". This library treats
these conditions as errors instead of producing diagnostic output.

Booleans expand as `1` and `0` at every nesting level, as described in the
[input contract](input-contract.md#values).

Floats are converted to strings by the library because RFC 6570 defines only
string, list, and associative-array values. The conversion always uses `.` as
the decimal separator, regardless of the process locale, and follows PHP's
`precision` setting. Non-finite floats are rejected, as described in the
[input contract](input-contract.md#values).

Variable values must be valid UTF-8, per RFC 6570 section 1.6. Invalid byte
sequences throw `InvalidArgumentException`, as described in the [input
contract](input-contract.md#values).

A defined composite value is empty in named non-exploded expansions only when
it contains no defined members. RFC 6570 section 2.3 treats a list as undefined
only when it contains zero members, and the appendix A algorithm tests the
value for emptiness before its members are joined, so `{;l}` expanded with
`['l' => ['']]` produces `;l=` rather than `;l`, while `{;l}` expanded with
`['l' => [null]]` produces `;l`. Several other implementations test the
comma-joined member string instead and omit the `=`, so path-style expansions
of composite values whose members all expand empty can differ between
libraries.

Exploded map members with empty-string values render as the bare name under the
simple, reserved (`+`), fragment (`#`), label (`.`), and path segment (`/`)
operators. RFC 6570 contradicts itself for these operators: the normative prose
in section 3.2.1 appends each exploded pair as "name=value" or, "if the value is
the empty string and the expression type does not indicate form-style
parameters", simply "name", while the non-normative appendix A algorithm appends
every pair with a defined value as "name=value", and no erratum resolves the
conflict. This library follows the normative section 3.2.1 rendering, so other
implementations that follow appendix A may render `name=` where this library
renders the bare name. The named operators agree under both readings: `;`
applies the same rule through its ifemp string, so `{;x*}` with an empty-string
member produces a bare name, while the form-style `{?x*}` and `{&x*}` keep
`name=`.

A map whose members are all `null` is treated as a wholly undefined variable,
so it is omitted together with the operator's first character unless another
variable in the expression is defined. RFC 6570 section 2.3 considers an
associative array undefined "if the array contains zero members or if all
member names in the array are associated with undefined values". A list whose
members are all `null` is instead a defined variable with no defined members:
RFC 6570 calls a list undefined only when it contains zero members and reserves
the all-members-undefined rule for associative arrays. Such a list expands to
an empty member list, so the operator first string is still emitted, named
forms render the name with the operator's empty-value form, and prefix
modifiers are rejected as for any other list. This is a revised conformance
decision where the RFC is ambiguous; there is no ecosystem consensus for these
inputs, and some implementations, such as std-uritemplate, reject them
outright.

No Unicode normalization is applied to templates or variable values. RFC 6570
section 1.6 states that a value provided by a user "SHOULD be normalized as
Normalization Form C" (NFC) prior to expansion, while a server-provided value
can be assumed to already be in the form the server expects. This library
cannot know where a value came from, so normalization is left to the caller,
and canonically equivalent inputs expand to different URIs: the decomposed
value `"e\u{0301}"` expands to `e%CC%81`, while the precomposed value
`"\u{00E9}"` expands to `%C3%A9`. Normalize user-entered text, for example
with ext-intl's `Normalizer::normalize($value, Normalizer::FORM_C)`, before
expansion.

## Related

- [Input Contract](input-contract.md)
- [Upgrade Guide](../UPGRADING.md)
- [Changelog](../CHANGELOG.md)
