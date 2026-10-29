# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [1.1.1] - 2026-10-20

This release fixes a bug that was new in 1.1.0. Everyone using 1.1.0 should
upgrade.

It fixes this advisory:

- [GHSA-f7cw-vfxq-7ph2](https://github.com/darylldoyle/svg-sanitizer/security/advisories/GHSA-f7cw-vfxq-7ph2):
  `sanitize()` could throw on input that ends inside an unclosed tag.

### Fixed

- `sanitize()` no longer throws a `ValueError` on PHP 8, or raises a warning on
  PHP 7, when the input ends inside a tag that is never closed. A single `<`
  or a truncated upload was enough. It now returns `false`, as 1.0.0 did. This
  bug was new in 1.1.0.
- The checks that run before parsing now fail closed too. If one of them ever
  throws, `sanitize()` returns `false` and reports the error in
  `getXmlIssues()`.

## [1.1.0] - 2026-10-19

This is a security release. Everyone using 1.0.0 or earlier should upgrade.
The public API has not changed, but some input is now cleaned differently. See
"Changed" below before you upgrade.

It fixes these advisories:

- [GHSA-q546-85r6-f39q](https://github.com/darylldoyle/svg-sanitizer/security/advisories/GHSA-q546-85r6-f39q):
  in Safari, SMIL animations could change a link's `href` after sanitizing.
- [GHSA-3vh3-jf65-656w](https://github.com/darylldoyle/svg-sanitizer/security/advisories/GHSA-3vh3-jf65-656w):
  remote references could get past `removeRemoteReferences(true)`.
- [GHSA-vjg7-f2wj-gqqh](https://github.com/darylldoyle/svg-sanitizer/security/advisories/GHSA-vjg7-f2wj-gqqh):
  the 1.0.0 DOCTYPE stripping could be avoided, which brought back the
  DTD-entity XSS.
- [GHSA-94x8-pw3c-6qcq](https://github.com/darylldoyle/svg-sanitizer/security/advisories/GHSA-94x8-pw3c-6qcq):
  CSS handling under `removeRemoteReferences(true)` could be slow, and could
  throw.
- [GHSA-hrwm-vcc9-hcm4](https://github.com/darylldoyle/svg-sanitizer/security/advisories/GHSA-hrwm-vcc9-hcm4):
  the `<use>` reference graph could take exponential time, and its limits could
  be avoided.
- [GHSA-cgcm-4g72-hfgw](https://github.com/darylldoyle/svg-sanitizer/security/advisories/GHSA-cgcm-4g72-hfgw):
  `sanitize()` could throw instead of returning `false`.
- [GHSA-2v7c-jx29-jprp](https://github.com/darylldoyle/svg-sanitizer/security/advisories/GHSA-2v7c-jx29-jprp):
  stripping PHP tags took quadratic time.

### Security

- Animation elements such as `<animateTransform>` can no longer change a link's
  `href` after sanitizing. Any `attributeName` that targets an `href` is removed.
- A document whose DTD would still reach the XML parser is now rejected. That
  covers DTDs in encodings the pre-parse stripper cannot read, such as UTF-16,
  and DOCTYPEs hidden inside other DOCTYPEs. An internal DTD in UTF-16 or UTF-32
  input is now rejected before parsing, so older libxml can no longer expand
  its entities first, even with `setAllowHugeFiles(true)`.
- Input that is unsafe to hand to the XML parser is rejected before parsing:
  - an element with more than 4,096 attributes, which libxml before 2.10
    parses in quadratic time
  - encodings whose markup is not plain ASCII and cannot be checked first,
    such as EBCDIC and UTF-7

  The parser also now runs with `LIBXML_NONET`.
- The `<use>` limits (`useThreshold()`, `setUseNestingLimit()` and loop
  detection) now measure the document a browser actually renders, both as XML
  and when the output is inlined in an HTML page. They are applied after tags
  and attributes are cleaned. They also now cover `<use>` elements that are
  nested inside other elements, that carry their own `id`, or that point at a
  duplicated `id`. They read references the way browsers do, including letter
  case, namespaces, whitespace and percent-encoding. `useThreshold()` now
  counts every `<use>` a reference renders, at every level of a chain, not
  only the deepest ones.
- Attributes that could hold a URL can no longer hold `javascript:`,
  `vbscript:` or non-image `data:` URLs, in every configuration. When the output
  is inlined in HTML, an `<image>` inside `<title>` or `<desc>` becomes an HTML
  `<img>` that uses `src` and `srcset`. The check covers every attribute except
  inert ones such as `title`, `class` and `id`, and `data-*` and `aria-*`, as
  in DOMPurify.
- `removeRemoteReferences(true)` now catches many more ways of writing a remote
  URL, in attributes and in `<style>`. These include CSS escapes, comments,
  line continuations and control characters inside URLs, backslashes in place
  of slashes, and schemes with no slashes. It also covers `image-set()`
  arguments and quoted strings, which closes the old gap for `image()` and
  `src()`. The stripped CSS is checked once more the way a browser reads it.
- Several inputs that took seconds of CPU, or over 100 MB of memory, now take
  milliseconds. Each affected part of the pipeline now scales roughly
  linearly with the input:
  - stripping PHP tags and the `DOCTYPE`
  - the remote-reference CSS checks
  - attribute cleaning
  - the `<use>` reference graph
  - walking the document, which no longer holds a PHP object for every node at
    once (costly before PHP 8.4)
  - removing nodes, which was quadratic on PHP before 8.1 when many children
    of one element were removed
  - the list of issues, which is now capped
- `sanitize()` no longer throws. Input it cannot process safely now returns
  `false`. That includes input that is empty once PHP tags or the `DOCTYPE` are
  removed, a root element that is not `<svg>`, and a failed regular expression.
- With `minify(true)`, whitespace is collapsed before the security checks run,
  so minifying can no longer change a value that was already checked.

### Changed

- **`<use>` limits.** `useThreshold()` now counts every `<use>` a reference
  renders: those inside plain groups, and those at every level of a chain.
  Very large legitimate artwork, such as a halftone pattern with more than
  1,000 nested copies, can lose the `<use>` elements that reference it. Raise
  the limit with `useThreshold()` if you need to.
- **Duplicate ids.** When an `id` appears more than once and a `<use>`
  references it, the later elements lose their `id`. Browsers only ever
  resolve the first one. Duplicates that no `<use>` references keep their `id`,
  so CSS `#id` rules still apply.
- **Removed spellings.** These are now removed, because an HTML parser turns
  them into live SVG when the output is inlined:
  - `<use>` written in another letter case, such as `<USE>`
  - `<use>` in another namespace
  - `id` written in another letter case, such as `ID`
  - any `xlink:href` outside the XLink namespace
- **Self-references.** An `href` on an element other than `<use>` no longer
  counts as a self-reference.
- **Unsafe URLs.** An attribute that starts with a `javascript:`, `vbscript:`
  or non-image `data:` URL is removed, in every configuration. For `srcset`,
  each candidate is checked. Local paths, `http(s)` URLs and `data:image/…`
  URLs stay. Inert attributes (`alt`, `class`, `for`, `id`, `label`, `name`,
  `pattern`, `placeholder`, `role`, `summary`, `title`, `value`, `style`,
  `xmlns`), `data-*` and `aria-*` are not checked.
- **Rejected documents.** `sanitize()` now returns `false` in these cases:
  - a UTF-16 or UTF-32 document that carries an internal DTD subset
  - an element with more than 4,096 attributes
  - an EBCDIC document, or one that declares an encoding not known to be
    ASCII-compatible, such as UTF-7, or that declares UTF-16 while its bytes
    are ASCII. UTF-8, UTF-16, UTF-32, ISO-8859-*, windows-125x and the common
    Chinese, Japanese, Korean and Cyrillic encodings are still accepted.
  - an `<svg>` root in a namespace other than SVG. A root with no namespace
    is still accepted.
  - input that is empty once PHP tags or the `DOCTYPE` are removed
  - the string `'0'`, which used to return `''` as if it were empty input.
    Only `''` counts as empty input now.
- **`removeRemoteReferences(true)`.** This option now removes more:
  - every `srcset` attribute, because it is a list of URLs
  - any attribute holding a quoted remote URL, such as an `aria-label` that
    quotes a link
  - a whole `<style>` block when a remote reference would otherwise survive
- **New issue messages.** `getXmlIssues()` can now report:
  - `Duplicate id '…'`
  - `Remote reference removed from 'style'`
  - `Document is empty`
  - `Internal DTD subset is not allowed`
  - `Nested DOCTYPE declaration`
  - `Too many attributes on one element`
  - `Unsupported document encoding`
  - `Root element is not in the SVG namespace`
  - a message for a failed regular expression

  The CLI scanner therefore now reports a file whose only problem was remote
  CSS in a `<style>` element.
- **Issue list.** `getXmlIssues()` lists at most 1,000 issues. When there are
  more, its last entry says how many were left out. Every call to `sanitize()`
  now starts with an empty list, including a call with empty input.

### Fixed

- A CSS escape for a UTF-16 surrogate, such as `\D800`, now decodes to U+FFFD
  instead of producing invalid UTF-8.

## [1.0.0] - 2026-09-01

### Security

- Strip the `DOCTYPE`/DTD from the input before parsing. This prevents custom
  entity definitions (which can collide with HTML5 named character references,
  e.g. `<!ENTITY Tab "#">` resolving `&Tab;` to U+0009 once inlined into HTML)
  and DTD-defaulted attributes from ever reaching the XML parser.
- Broaden the remote-reference detection used by `removeRemoteReferences(true)`
  so previously-missed remote references are stripped.
- Strip remote references from inline `<style>` content and `style` attributes
  more thoroughly under `removeRemoteReferences(true)`: CSS escapes and comments
  are resolved before matching (so `\75 rl(…)` or `@\69 mport "…"` cannot hide),
  escaped or whitespace-separated `@import` is handled, and the bare-string form
  of `image-set()` / `-webkit-image-set()` is covered. This remains best-effort —
  a regex-based stripper cannot see through every CSS construct (the bare-string
  forms of `image()` / `src()` are not handled), so untrusted CSS should still be
  isolated at the embedding boundary. Unclosed `url(` / `image-set(` (which a CSS
  tokenizer closes implicitly and still fetches) are stripped too. Note: when a
  `<style>` / `style` block contains a stripped remote reference, its CSS escapes
  are normalised (decoded) in the output — semantically equivalent, but it
  rewrites author escapes in that block.
- Harden DTD stripping to skip comments and quoted strings while scanning the
  internal subset, so a `]` inside a DTD comment or entity value can no longer
  truncate the scan early and leave a DTD fragment behind. The `DOCTYPE` is now
  removed correctly by construction.

### Changed

- **Potentially breaking:** an SVG/XML document that references a **custom DTD
  entity** is now rejected — `sanitize()` returns `false` — instead of being
  cleaned. Because the DTD is stripped before parsing, the entity reference
  becomes undefined and libxml rejects the document (fail-safe). A `PUBLIC`
  `DOCTYPE` with no entity references (e.g. a typical Illustrator/Inkscape
  export) still sanitizes normally.
- **Behaviour change (opt-in only):** with `removeRemoteReferences(true)`, remote
  references are now detected and removed in more places — bare remote
  `href`/`src` values, unquoted `url(...)` references, `url(...)` embedded among
  other declarations in a `style` attribute, and remote `@import`/`url(...)`
  inside `<style>` element text. Previously only a whole-value, quoted `url(...)`
  was matched. Local (`/path`) and fragment (`#id`) references are still
  preserved.
