# Security policy

`enshrined/svg-sanitize` cleans untrusted SVG and XML so it can be shown safely. A lot of projects depend on it, so I take security reports seriously. This page explains how to report a problem, and what does and doesn't count as a vulnerability.

## This is a side project

I maintain this library in my spare time. I have a full-time job, and this repo isn't part of it. I'll always try to deal with reports as quickly as I can, but it can sometimes take me a while to reply. If you haven't heard back after a couple of weeks, feel free to add a comment to your report.

## Which versions get fixes

Only the latest `1.x` release gets security fixes. Older releases, including every `0.x` release, don't. Please upgrade to the latest release and check that the problem still happens there before you report it.

| Version | Gets security fixes |
| ------- | ------------------- |
| 1.x (latest) | Yes |
| 0.x and older | No |

## How to report a problem

Please report it privately through GitHub:

**[Report a vulnerability](https://github.com/darylldoyle/svg-sanitizer/security/advisories/new)**

Please don't open a public issue, pull request or discussion about a security problem. Until a fix is out, a public report puts every site that uses the library at risk.

These things help me fix a problem quickly:

- **Something I can run.** A short SVG file and the few lines of PHP that sanitize it. Run it against the current `master` branch or the latest release.
- **What went wrong.** What you expected the output to be, what it actually was, and which part of it is unsafe.
- **How the output is used.** Is it served as an `.svg` file, loaded with `<img>`, or pasted into an HTML page? Many SVG attacks only work in one of these.
- **Your settings.** Any options you changed, such as `removeRemoteReferences(true)`, `setAllowHugeFiles(true)`, `useThreshold()` or a custom allowlist.
- **Your PHP and libxml versions**, if the problem seems to depend on them. This matters most for slow inputs and character encodings.

You're welcome to use AI tools to find problems. Before you send a report, please run the example yourself and include what it printed. I'll close reports I can't reproduce.

## What happens after you report

1. I check that I can reproduce the problem.
2. If I can, I accept your report as a draft advisory and work on a fix in private.
3. I release the fix and publish the advisory, with a CVE where one applies. Fixes for several reports often go out together in one release.
4. I credit you in the advisory, unless you'd rather I didn't.

Several people often find the same problem around the same time. When that happens, I keep the first report open and close the later ones as duplicates. I add everyone who reported it to the credits on the report I keep. Before you report, please look through the [published advisories](https://github.com/darylldoyle/svg-sanitizer/security/advisories) and the [CHANGELOG](CHANGELOG.md) in case it's already been fixed.

Please keep the details to yourself until the advisory is published.

## What counts as a vulnerability

Please report these:

- **Script that runs.** Sanitized output that can still run script, whether it's served as a file, loaded with `<img>` or pasted into an HTML page.
- **A way round a defence.** Something the library is meant to remove that gets through. Examples include a DOCTYPE, an entity, an event handler, an unsafe `href`, or an animation that rewrites a link.
- **Remote references with `removeRemoteReferences(true)` on.** A link to another site that's still in an attribute or a `<style>` element.
- **Too many `<use>` copies.** A way round the limit on how far `<use>` elements can expand, or how deeply they can nest.
- **Slow input.** An input that makes `sanitize()` far slower than its size suggests. As a rough guide, report anything under 1 MB that takes more than a few seconds, or where the time grows faster than the input does.
- **Errors.** `sanitize()` should only ever return a string or `false`. If it throws an exception or an error, or raises a PHP warning, please report it.

These aren't vulnerabilities:

- **Remote URLs with the default settings.** By default the library allows links to other sites (`http://`, `https://` and `//`) on purpose. Turn on `removeRemoteReferences(true)` if you want them removed.
- **Problems in the code that uses the library.** For example, changing the output after it's been sanitized, or serving it from a page with no other protection.
- **A custom allowlist that adds unsafe tags or attributes.**
- **Problems that only affect versions that don't get fixes.**
- **A valid file that gets rejected.** If `sanitize()` returns `false` for a normal SVG, please open an ordinary issue instead.

If you're not sure whether something counts, report it privately anyway and I'll let you know.
