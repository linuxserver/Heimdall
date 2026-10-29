# svg-sanitizer

[![Build Status](https://github.com/darylldoyle/svg-sanitizer/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/darylldoyle/svg-sanitizer/actions/workflows/tests.yml) [![Code Coverage](https://qlty.sh/gh/darylldoyle/projects/svg-sanitizer/coverage.svg)](https://qlty.sh/gh/darylldoyle/projects/svg-sanitizer)

This is my attempt at building a decent SVG sanitizer in PHP. The work is largely borrowed from [DOMPurify](https://github.com/cure53/DOMPurify).

## Installation

Either require `enshrined/svg-sanitize` through composer or download the repo and include the old way!

## Usage

Using this is fairly easy. Create a new instance of `enshrined\svgSanitize\Sanitizer` and then call the `sanitize` whilst passing in your dirty SVG/XML

**Basic Example**

```php
use enshrined\svgSanitize\Sanitizer;

// Create a new sanitizer instance
$sanitizer = new Sanitizer();

// Load the dirty svg
$dirtySVG = file_get_contents('filthy.svg');

// Pass it to the sanitizer and get it back clean
$cleanSVG = $sanitizer->sanitize($dirtySVG);

// Now do what you want with your clean SVG/XML data

```

## Output

This will either return a sanitized SVG/XML string or boolean `false` if XML parsing failed (usually due to a badly formatted file).

It also returns `false` for input it will not hand to the XML parser:

- an element with more than 4,096 attributes
- an internal DTD in UTF-16 or UTF-32 input
- an encoding whose markup is not plain ASCII, such as EBCDIC or UTF-7
- an `<svg>` root in a namespace other than SVG

`getXmlIssues()` says why.

## Options

You may pass your own whitelist of tags and attributes by using the `Sanitizer::setAllowedTags` and `Sanitizer::setAllowedAttrs` methods respectively.

These methods require that you implement the `enshrined\svgSanitize\data\TagInterface` or `enshrined\svgSanitize\data\AttributeInterface`.

`<use>` is allowed by default, behind limits on how far references can expand (`useThreshold()`) and nest (`setUseNestingLimit()`). If you don't need it, for example because you inline untrusted SVG into your pages, leave `use` out of your tag allowlist to remove that whole area.

## Remove remote references

You have the option to remove attributes that reference remote files, this will stop HTTP leaks but will add an overhead to the sanitizer.

This defaults to false, set to true to remove references.

`$sanitizer->removeRemoteReferences(true);`

When enabled, remote references are also stripped from CSS inside `<style>` elements and from CSS in attributes (`url()`, `@import`, `image-set()`, and quoted strings that start with a remote URL). `srcset` attributes are removed entirely, because they hold a list of URLs. This CSS handling is best-effort: it is not a full CSS parser. Treat it as defence-in-depth rather than a guarantee, and isolate untrusted CSS at the point where the SVG is embedded.

Note: if a `<style>` element or `style` attribute contains a remote reference that gets stripped, any CSS escape sequences in that same block are normalised (decoded) in the output. This is semantically identical CSS but will rewrite escapes an author wrote by hand.

## Viewing Sanitization Issues

You may use the `getXmlIssues()` method to return an array of issues that occurred during sanitization.

This may be useful for logging or providing feedback to the user on why an SVG was refused.

`$issues = $sanitizer->getXmlIssues();`

The list holds up to 1,000 issues. If there are more, the last entry says how many were left out.

## Minification

You can minify the XML output by calling `$sanitizer->minify(true);`.

## Demo
There is a demo available at: [http://svg.enshrined.co.uk/](http://svg.enshrined.co.uk/)

## WordPress

I've just released a WordPress plugin containing this code so you can sanitize your WordPress uploads. It's available from the WordPress plugin directory: [https://wordpress.org/plugins/safe-svg/](https://wordpress.org/plugins/safe-svg/)

## Drupal

[Michael Potter](https://github.com/heyMP) has kindly created a Drupal module for this library which is available at: [https://www.drupal.org/project/svg_sanitizer](https://www.drupal.org/project/svg_sanitizer)

## TYPO3

This SVG sanitizer library is used per default in the core of TYPO3 v9 and later versions.
See [corresponding changelog entry](https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/9.5.x/Important-94492-IntroduceSVGSanitizer.html) for more details.

## Tests

You can run these by running `vendor/bin/phpunit` from the base directory of this package.

## Standalone scanning of files via CLI

Thanks to the work by [gudmdharalds](https://github.com/gudmdharalds) there's now a standalone scanner that can be used via the CLI.

Any errors will be output in JSON format. See [the PR](https://github.com/darylldoyle/svg-sanitizer/pull/25) for an example.

Use it as follows: `php svg-scanner.php ~/svgs/myfile.svg`

## To-Do

More extensive testing for the SVGs/XML would be lovely, I'll try and add these soon. If you feel like doing it for me, please do and make a PR!
