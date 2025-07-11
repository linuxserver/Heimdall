# Changelog

The file documents changes to the PHP_CodeSniffer project.

## [Unreleased]

_Nothing yet._

## [3.13.2] - 2025-06-18

### Changed
- The documentation for the following sniffs has been improved:
    - Squiz.Classes.SelfMemberReference
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Fixed
- Fixed bug [#1135] : Squiz.Functions.FunctionDeclarationArgumentSpacing: typo in new error code `SpacingAfterSetVis\[i\]bility`.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.

[#1135]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/1135


## [3.13.1] - 2025-06-13

### Added
- Added support for PHP 8.4 properties with asymmetric visibility to File::getMemberProperties() through a new `set_scope` array index in the return value. [#1116]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patches.
- Added support for PHP 8.4 (constructor promoted) properties with asymmetric visibility to File::getMethodParameters() through new `set_visibility` and `set_visibility_token` array indexes in the return value. [#1116]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patches.
- Added support for PHP 8.4 asymmetric visibility modifiers to the following sniffs:
    - Generic.PHP.LowerCaseKeyword [#1117]
    - PEAR.NamingConventions.ValidVariableName [#1118]
    - PSR2.Classes.PropertyDeclaration [#1119]
    - Squiz.Commenting.BlockComment [#1120]
    - Squiz.Commenting.DocCommentAlignment [#1120]
    - Squiz.Commenting.VariableComment [#1120]
    - Squiz.Functions.FunctionDeclarationArgumentSpacing [#1121]
    - Squiz.Scope.MemberVarScope [#1122]
    - Squiz.WhiteSpace.MemberVarSpacing [#1123]
    - Squiz.WhiteSpace.ScopeKeywordSpacing [#1124]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patches.

### Changed
- The PSR2.Classes.PropertyDeclaration will now check that a set-visibility modifier keyword is placed after a potential general visibility keyword. [#1119]
    - Errors will be reported via a new `AvizKeywordOrder` error code.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- The Squiz.Functions.FunctionDeclarationArgumentSpacing will now check spacing after a set-visibility modifier keyword. [#1121]
    - Errors will be reported via a new `SpacingAfterSetVisibility` error code.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- The Squiz.Scope.MemberVarScope will now flag missing "read" visibility, when "write" visibility is set, under a separate error code `AsymReadMissing`. [#1122]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- The documentation for the following sniffs has been improved:
    - PEAR.Classes.ClassDeclaration
    - Squiz.WhiteSpace.FunctionOpeningBraceSpace
    - Thanks to [Brian Dunne][@braindawg] and [Rodrigo Primo][@rodrigoprimo] for the patches.
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Dan Wallis][@fredden], [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Other
- The latest PHP_CodeSniffer XSD file is now available via the following permalink: <https://schema.phpcodesniffer.com/phpcs.xsd>. [#1094]
    Older XSD files can be referenced via permalinks based on their minor: `https://schema.phpcodesniffer.com/#.#/phpcs.xsd`.
- The GPG signature for the PHAR files has been rotated. The new fingerprint is: D91D86963AF3A29B6520462297B02DD8E5071466.

[#1094]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/1094
[#1116]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/1116
[#1117]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/1117
[#1118]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/1118
[#1119]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/1119
[#1120]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/1120
[#1121]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/1121
[#1122]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/1122
[#1123]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/1123
[#1124]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/1124


## [3.13.0] - 2025-05-11

### Added
- Added support for PHP 8.4 asymmetric visibility modifiers to the tokenizer. [#871]
    - Thanks to [Daniel Scherzer][@DanielEScherzer] for the patch.
- Added support for PHP 8.4 `final` properties to the following sniffs:
    - PSR2.Classes.PropertyDeclaration [#950]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patches.

### Changed
- Generic.WhiteSpace.LanguageConstructSpacing: will now also check the spacing after the `goto` language construct keyword. [#917]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- The PSR2.Classes.PropertyDeclaration will now check that the `final` modifier keyword is placed before a visibility keyword. [#950]
    - Errors will be reported via a new `FinalAfterVisibility` error code.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Improved Help information about the `--reports` CLI flag. [#1078]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- The documentation for the following sniffs has been improved:
    - PSR1.Files.SideEffects
    - PSR2.ControlStructures.SwitchDeclaration
    - PSR2.Namespaces.NamespaceDeclaration
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patches.
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Deprecated

- Nearly everything which was soft deprecated before is now hard deprecated and will show deprecation notices:
    - This applies to:
        - All sniffs which will be removed in 4.0. [#888]
        - The deprecated Generator methods. [#889]
        - The old array property setting format (via comma separated strings). [#890]
        - Sniffs not implementing the `PHP_CodeSniffer\Sniffs\Sniff` interface. [#891]
        - Sniffs not following the naming conventions. [#892]
        - Standards called Internal. [#893]
        - Sniffs which don't listen for PHP, like JS/CSS specific sniffs. [#894]
    - The deprecation notices can be silenced by using the `-q` (=quiet) CLI flag.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patches.

### Fixed
- Fixed bug [#1040] : Generic.Strings.UnnecessaryHeredoc - false positive for heredocs containing escape sequences.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#1040] : Generic.Strings.UnnecessaryHeredoc - fixer would not clean up escape sequences which aren't necessary in nowdocs.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#1048] : A file under scan would sometimes be updated with partial fixes, even though the file "failed to fix".
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.

### Other
**Calling all testers!**

The first beta release for PHP_CodeSniffer 4.0 has been tagged. Please help by testing the beta release and reporting any issues you run into.
Upgrade guides for both [ruleset maintainers/end-users][wiki-upgrade-guide-users-40], as well as for [sniff developers and integrators][wiki-upgrade-guide-devs-40], have been published to the Wiki to help smooth the transition.

[wiki-upgrade-guide-users-40]: https://github.com/PHPCSStandards/PHP_CodeSniffer/wiki/Version-4.0-User-Upgrade-Guide
[wiki-upgrade-guide-devs-40]:  https://github.com/PHPCSStandards/PHP_CodeSniffer/wiki/Version-4.0-Developer-Upgrade-Guide

[#871]:  https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/871
[#888]:  https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/888
[#889]:  https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/889
[#890]:  https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/890
[#891]:  https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/891
[#892]:  https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/892
[#893]:  https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/893
[#894]:  https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/894
[#917]:  https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/917
[#950]:  https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/950
[#1040]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/1040
[#1048]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/1048
[#1078]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/1078

## [3.12.2] - 2025-04-13

### Added
- Added support for PHP 8.4 `final` properties to the following sniffs:
    - Generic.PHP.LowerCaseConstant [#948]
    - Generic.PHP.UpperCaseConstant [#948]
    - Squiz.Commenting.DocCommentAlignment [#951]
    - Squiz.Commenting.VariableComment [#949]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patches.

### Changed
- Tokenizer/PHP: a PHP open tag at the very end of a file will now always be tokenized as T_OPEN_TAG, independently of the PHP version. [#937]
    - Previously, a PHP open tag at the end of a file was not tokenized as an open tag on PHP < 7.4 and the tokenization would depend on the `short_open_tag` setting.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- PEAR.Commenting.FunctionComment: improved message for "blank lines between docblock and declaration" check. [#830]
- The documentation for the following sniffs has been improved:
    - Generic.Functions.OpeningFunctionBraceBsdAllman
    - Generic.Functions.OpeningFunctionBraceKernighanRitchie
    - Generic.WhiteSpace.LanguageConstructSpacing
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patches.
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Fixed
- Fixed bug [#830] : PEAR.Commenting.FunctionComment will no longer remove blank lines within attributes.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#929] : Generic.PHP.ForbiddenFunctions: prevent false positives/negatives for code interlaced with comments.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#934] : Generic.PHP.LowerCaseConstant and Generic.PHP.UpperCaseConstant will now correctly ignore DNF types for properties.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#936] : Squiz.Commenting.FunctionCommentThrowTag: sniff would bow out when function has attributes attached, leading to false negatives.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#940] : Squiz.Commenting.VariableComment: false positive for missing docblock for properties using DNF types.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#944] : Squiz.Commenting.FunctionComment did not support DNF/intersection types in `@param` tags.
    - Thanks to [Jeffrey Angenent][@devfrey] for the patch.
- Fixed bug [#945] : Squiz.WhiteSpace.FunctionSpacing would get confused when there are two docblocks above a function declaration.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#947] : Squiz.Commenting.FunctionCommentThrowTag: prevent false positives/negatives for code interlaced with comments.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#951] : Squiz.Commenting.DocCommentAlignment did not examine docblocks for `final` classes.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#955] : Potential race condition, leading to a fatal error, when both the `Diff` + the `Code` reports are requested and caching is on.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#956] : Generic.WhiteSpace.ScopeIndent: undefined array index notice when running in debug mode.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.

[#830]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/830
[#929]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/929
[#934]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/934
[#936]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/936
[#937]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/937
[#940]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/940
[#944]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/944
[#945]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/945
[#947]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/947
[#948]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/948
[#949]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/949
[#951]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/951
[#955]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/955
[#956]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/956

## [3.12.1] - 2025-04-04

### Added
- Documentation for the following sniffs:
    - Squiz.Commenting.BlockComment
    - Thanks to [Colin Stewart][@costdev] for the patch.

### Changed
- Generic.WhiteSpace.HereNowdocIdentifierSpacing: improved error message text.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Deprecated
- The `Generic.Functions.CallTimePassByReference` sniff. See [#921].
    - This sniff will be removed in version 4.0.0.

### Fixed
- Fixed bug [#906] : Fixer: prevent `InvalidArgumentException`s when displaying verbose information.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#907] : Tokenizer/PHP: tokenization of tokens related to union, intersection and DNF types in combination with PHP 8.4 final properties.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#908] : Tokenizer/PHP: tokenization of `?` in nullable types for readonly properties.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#916] : Tokenizer/PHP: `goto` was not recognized as a terminating statement for a case/default in a switch control structure.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.

### Other
- PHP_CodeSniffer 4.0 is coming soon! Interested in a sneak peek ? Join the live stream at any time on April 14, 15, 17 or 18.
    Read the open invitation ([#924]) for all the details.

[#906]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/906
[#907]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/907
[#908]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/908
[#916]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/916
[#921]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/921
[#924]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/924

## [3.12.0] - 2025-03-18

### Added
- Added support for PHP 8.4 `final` properties to File::getMemberProperties() through a new `is_final` array index in the return value. [#834]
    - Thanks to [Daniel Scherzer][@DanielEScherzer] for the patch.
- Generators/HTML: each section title now has a unique anchor link, which can be copied when hovering over a title. [#859]
    - This should make sharing a link to a specific section of the documentation more straight-forward.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Documentation for the following sniffs:
    - Squiz.Classes.ClassFileName
    - Squiz.Classes.ValidClassName
    - Thanks to [Brian Dunne][@braindawg] for the patches.

### Changed
- PHPCBF: the messaging when no fixable errors are found will now distinguish between "No violations" (at all) versus "No fixable errors". [#806]
    - Thanks to [Peter Wilson][@peterwilsoncc] for the patch.
- The `-h` (Help) option now contains a more extensive list of "config" options which can be set. [#809]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Improved error message when invalid sniff codes are supplied to `--sniffs` or `--exclude` command line arguments. [#344]
    - Thanks to [Dan Wallis][@fredden] for the patch.
- Improved error message when an invalid generator name is supplied to the `--generator` command line argument. [#709], [#771]
    - The generator name will now also always be handled case-insensitively, independently of the OS used.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- The user will be shown an informative error message for sniffs missing one of the required methods. [#873]
    - Previously this would result in a fatal error.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Ruleset processing will now be allowed to run to its conclusion - barring critical errors - before displaying all ruleset errors in one go. [#857]
    - Previously an error in a ruleset would cause PHPCS to exit immediately and show only one error at a time.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Generators: XML documentation files which don't contain any actual documentation will now silently be ignored. [#755]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Generators: when the `title` attribute is missing, the documentation generation will now fall back to the sniff name as the title. [#820]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Generators: cleaner output based on the elements of the documentation which are available. [#819], [#821]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Generators/HTML: improved display of code tables by using semantic HTML. [#854]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Squiz.Classes.ClassFileName: recommend changing the file name instead of changing the class name. [#845]
    - This prevents unactionable recommendations due to the file name not translating to a valid PHP symbol name.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Squiz.Functions.FunctionDeclarationArgumentSpacing: incorrect spacing after a comma followed by a promoted property has an improved error message and will now be flagged with the `SpacingBeforePropertyModifier` or `NoSpaceBeforePropertyModifier` error codes. [#792]
    - This was previously already flagged, but using either the `SpacingBeforeHint` or `NoSpaceBeforeHint` error code, which was misleading.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Squiz.Functions.FunctionDeclarationArgumentSpacing: the sniff will now also check the spacing after property modifiers for promoted properties in constructor methods. [#792]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Squiz.WhiteSpace.ScopeKeywordSpacing: the sniff will now also check the spacing after the `final` and `abstract` modifier keywords. [#604]
    - Thanks to [Klaus Purer][@klausi] for the patch.
- The following sniff(s) have received efficiency improvements:
    - Squiz.WhiteSpace.ScopeKeywordSpacing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patches.
- Incorrectly set inline properties (in test case files) will be silently ignored again. [#884]
    - This removes the `Internal.PropertyDoesNotExist` error code.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- The AbstractMethodUnitTest class will now flag duplicate test case markers in a test case file. [#773]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Asis Pattisahusiwa][@asispts], [Dan Wallis][@fredden], [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Deprecated
All deprecation are slated for removal in PHP_CodeSniffer 4.0.

- Support for sniffs not implementing the PHPCS `Sniff` interface. See [#694].
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Support for including sniffs which don't comply with the PHPCS naming conventions (by referencing the sniff file directly). See [#689].
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Support for external standards named "Internal". See [#799].
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- The following Generator methods are now (soft) deprecated. See [#755]:
    - `PHP_CodeSniffer\Generators\Text::printTitle()` in favour of `PHP_CodeSniffer\Generators\Text::getFormattedTitle()`
    - `PHP_CodeSniffer\Generators\Text::printTextBlock()` in favour of `PHP_CodeSniffer\Generators\Text::getFormattedTextBlock()`
    - `PHP_CodeSniffer\Generators\Text::printCodeComparisonBlock()` in favour of `PHP_CodeSniffer\Generators\Text::getFormattedCodeComparisonBlock()`
    - `PHP_CodeSniffer\Generators\Markdown::printHeader()` in favour of `PHP_CodeSniffer\Generators\Markdown::getFormattedHeader()`
    - `PHP_CodeSniffer\Generators\Markdown::printFooter()` in favour of `PHP_CodeSniffer\Generators\Markdown::getFormattedFooter()`
    - `PHP_CodeSniffer\Generators\Markdown::printTextBlock()` in favour of `PHP_CodeSniffer\Generators\Markdown::getFormattedTextBlock()`
    - `PHP_CodeSniffer\Generators\Markdown::printCodeComparisonBlock()` in favour of `PHP_CodeSniffer\Generators\Markdown::getFormattedCodeComparisonBlock()`
    - `PHP_CodeSniffer\Generators\HTML::printHeader()` in favour of `PHP_CodeSniffer\Generators\HTML::getFormattedHeader()`
    - `PHP_CodeSniffer\Generators\HTML::printToc()` in favour of `PHP_CodeSniffer\Generators\HTML::getFormattedToc()`
    - `PHP_CodeSniffer\Generators\HTML::printFooter()` in favour of `PHP_CodeSniffer\Generators\HTML::getFormattedFooter()`
    - `PHP_CodeSniffer\Generators\HTML::printTextBlock()` in favour of `PHP_CodeSniffer\Generators\HTML::getFormattedTextBlock()`
    - `PHP_CodeSniffer\Generators\HTML::printCodeComparisonBlock()` in favour of `PHP_CodeSniffer\Generators\HTML::getFormattedCodeComparisonBlock()`
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.

### Fixed
- Fixed bug [#794] : Generators: prevent fatal error when the XML documentation does not comply with the expected format.
    - It is recommended to validate XML documentation files against the XSD file: <https://phpcsstandards.github.io/PHPCSDevTools/phpcsdocs.xsd>
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#814] : Generic.NamingConventions.ConstructorName: prevent potential fatal errors during live coding.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#816] : File::getDeclarationName(): prevent incorrect result for unfinished closures during live coding.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#817] : Squiz.Classes.ValidClassName: ignore comments when determining the name to be validated.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#825] : Squiz.Classes.ClassDeclaration: false positives when the next thing after a class was a function with an attribute attached.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#826] : Squiz.WhiteSpace.FunctionSpacing: prevent incorrect some results when attributes are attached to a function.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#827] : PEAR.Functions.FunctionDeclaration: fixer conflict over an unfinished closure during live coding.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#828] : Squiz.WhiteSpace.MemberVarSpacing: allow for `readonly` properties.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#832] : Squiz.WhiteSpace.MemberVarSpacing: prevent potential fixer conflict during live coding.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#833] : Squiz.PHP.EmbeddedPhp: fixer conflict when a PHP open tag for a multi-line snippet is found on the same line as a single-line embedded PHP snippet.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#833] : Squiz.PHP.EmbeddedPhp: incorrect indent calculation in certain specific situations.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#835] : Generic.PHP.DisallowShortOpenTag: don't act on parse errors.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#838] : Squiz.PHP.EmbeddedPhp: no new line before close tag was incorrectly enforced when a preceding OO construct or function had a trailing comment after the close curly.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#840] : Squiz.WhiteSpace.MemberVarSpacing: more accurate reporting on blank lines in the property "pre-amble" (i.e. docblock, attributes).
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#845] : Squiz.Classes.ClassFileName: don't throw an incorrect error for an unfinished OO declaration during live coding.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#865] : Setting an array property to an empty array from an XML ruleset now works correctly.
    - Previously, the property value would be set to `[0 => '']`.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#866] : Squiz.WhiteSpace.FunctionOpeningBraceSpace: XML docs were not accessible due to an issue with the file name.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.

### Other
- A new [wiki page][wiki-about-standards] is available to clarify the difference between a project ruleset and an external standard.
    - This wiki page also contains detailed information about the naming conventions external standards must comply with.
- A new [XMLLint validate][xmllint-validate] action runner is available which can be used in CI to validate rulesets for PHP_CodeSniffer against the XSD.

[wiki-about-standards]: https://github.com/PHPCSStandards/PHP_CodeSniffer/wiki/About-Standards-for-PHP_CodeSniffer
[xmllint-validate]: https://github.com/marketplace/actions/xmllint-validate

[#344]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/344
[#604]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/604
[#689]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/689
[#694]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/694
[#709]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/709
[#755]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/755
[#771]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/771
[#773]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/773
[#792]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/792
[#794]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/794
[#799]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/799
[#806]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/806
[#809]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/809
[#814]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/814
[#816]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/816
[#817]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/817
[#819]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/819
[#820]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/820
[#821]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/821
[#825]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/825
[#826]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/826
[#827]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/827
[#828]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/828
[#832]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/832
[#833]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/833
[#834]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/834
[#835]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/835
[#838]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/838
[#840]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/840
[#845]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/845
[#854]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/854
[#857]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/857
[#859]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/859
[#865]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/865
[#866]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/866
[#873]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/873
[#884]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/884

## [3.11.3] - 2025-01-23

### Changed
- Generic.ControlStructures.InlineControlStructure no longer unnecessarily listens for T_SWITCH tokens. [#595]
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Squiz.Functions.FunctionDeclarationArgumentSpacing: improvements to error message for `SpaceBeforeComma` error. [#783]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- The following sniff(s) have received efficiency improvements:
    - Squiz.Functions.FunctionDeclarationArgumentSpacing
    - Thanks to [Dan Wallis][@fredden] and [Juliette Reinders Folmer][@jrfnl] for the patches.
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Michał Bundyra][@michalbundyra], [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Fixed
- Fixed bug [#620] : Squiz.Functions.FunctionDeclarationArgumentSpacing: newlines after type will now be handled by the fixer. This also prevents a potential fixer conflict.
    - Thanks to [Dan Wallis][@fredden] for the patch.
- Fixed bug [#782] : Tokenizer/PHP: prevent an "Undefined array key" notice during live coding for unfinished arrow functions.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#783] : Squiz.Functions.FunctionDeclarationArgumentSpacing: new line after reference token was not flagged nor fixed.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#783] : Squiz.Functions.FunctionDeclarationArgumentSpacing: new line after variadic token was not flagged nor fixed.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#783] : Squiz.Functions.FunctionDeclarationArgumentSpacing: new line before/after the equal sign for default values was not flagged nor fixed when `equalsSpacing` was set to `0`.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#783] : Squiz.Functions.FunctionDeclarationArgumentSpacing: fixer conflict when a new line is found before/after the equal sign for default values and `equalsSpacing` was set to `1`.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#783] : Squiz.Functions.FunctionDeclarationArgumentSpacing: fixer for spacing before/after equal sign could inadvertently remove comment.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#783] : Squiz.Functions.FunctionDeclarationArgumentSpacing: fixer will now handle comments between the end of a parameter and a comma more cleanly.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#784] : Squiz.WhiteSpace.FunctionSpacing: prevent fixer conflict when a multi-line docblock would start on the same line as the function close curly being examined.
    - Thanks to [Klaus Purer][@klausi] for the patch

[#595]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/595
[#620]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/620
[#782]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/782
[#783]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/783
[#784]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/784

## [3.11.2] - 2024-12-11

### Changed
- Generators/HTML + Markdown: the output will now be empty (no page header/footer) when there are no docs to display. [#687]
    - This is in line with the Text Generator which already didn't produce output if there are no docs.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Generators/HTML: only display a Table of Contents when there is more than one sniff with documentation. [#697]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Generators/HTML: improved handling of line breaks in `<standard>` blocks. [#723]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Generators/Markdown: improved compatibility with the variety of available markdown parsers. [#722]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Generators/Markdown: improved handling of line breaks in `<standard>` blocks. [#737]
    - This prevents additional paragraphs from being displayed as code blocks.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Generic.NamingConventions.UpperCaseConstantName: the exact token containing the non-uppercase constant name will now be identified with more accuracy. [#665]
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Generic.Functions.OpeningFunctionBraceKernighanRitchie: minor improvement to the error message wording. [#736]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Fixed
- Fixed bug [#527] : Squiz.Arrays.ArrayDeclaration: short lists within a foreach condition should be ignored.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Fixed bug [#665] : Generic.NamingConventions.UpperCaseConstantName: false positives and false negatives when code uses unconventional spacing and comments when calling `define()`.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Fixed bug [#665] : Generic.NamingConventions.UpperCaseConstantName: false positive when a constant named `DEFINE` is encountered.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Fixed bug [#665] : Generic.NamingConventions.UpperCaseConstantName: false positive for attribute class called `define`.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Fixed bug [#665] : Generic.NamingConventions.UpperCaseConstantName: false positive when handling the instantiation of a class named `define`.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Fixed bug [#688] : Generators/Markdown could leave error_reporting in an incorrect state.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#698] : Generators/Markdown : link in the documentation footer would not parse as a link.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#738] : Generators/Text: stray blank lines after code sample titles.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#739] : Generators/HTML + Markdown: multi-space whitespace within a code sample title was folded into a single space.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.

[#527]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/527
[#665]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/665
[#687]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/687
[#688]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/688
[#697]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/697
[#698]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/698
[#722]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/722
[#723]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/723
[#736]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/736
[#737]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/737
[#738]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/738
[#739]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/739

## [3.11.1] - 2024-11-16

### Changed
- Output from the `--generator=...` feature will respect the OS-expected EOL char in more places. [#671]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Bartosz Dziewoński][@MatmaRex] and [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Fixed
- Fixed bug [#674] : Generic.WhiteSpace.HereNowdocIdentifierSpacing broken XML documentation
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Fixed bug [#675] : InvalidArgumentException when a ruleset includes a sniff by file name and the included sniff does not comply with the PHPCS naming conventions.
    - Notwithstanding this fix, it is strongly recommended to ensure custom sniff classes comply with the PHPCS naming conventions.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.

[#671]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/671
[#674]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/674
[#675]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/675

## [3.11.0] - 2024-11-12

### Added
- Runtime support for PHP 8.4. All known PHP 8.4 deprecation notices have been fixed.
    - Syntax support for new PHP 8.4 features will follow in a future release.
    - If you find any PHP 8.4 deprecation notices which were missed, please report them.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patches.
- Tokenizer support for PHP 8.3 "yield from" expressions with a comment between the keywords. [#529], [#647]
    - Sniffs explicitly handling T_YIELD_FROM tokens may need updating. The PR description contains example code for use by sniff developers.
    - Additionally, the following sniff has been updated to support "yield from" expressions with comments:
        - Generic.WhiteSpace.LanguageConstructSpacing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- New `Generic.WhiteSpace.HereNowdocIdentifierSpacing` sniff. [#586], [#637]
    - Forbid whitespace between the `<<<` and the identifier string in heredoc/nowdoc start tokens.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- New `Generic.Strings.UnnecessaryHeredoc` sniff. [#633]
    - Warns about heredocs without interpolation or expressions in the body text and can auto-fix these to nowdocs.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Documentation for the following sniffs:
    - Generic.Arrays.ArrayIndent
    - Squiz.PHP.Heredoc
    - Thanks to [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for the patches.

### Changed
- The Common::getSniffCode() method will now throw an InvalidArgumentException exception if an invalid `$sniffClass` is passed. [#524], [#625]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Documentation generated using the `--generator=...` feature will now always be presented in natural order based on the sniff name(s). [#668]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Minor improvements to the display of runtime information. [#658]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Squiz.Commenting.PostStatementComment: trailing annotations in PHP files will now be reported under a separate, non-auto-fixable error code `AnnotationFound`. [#560], [#627]
    - This prevents (tooling related) annotations from taking on a different meaning when moved by the fixer.
    - The separate error code also allows for selectively excluding it to prevent the sniff from triggering on trailing annotations, while still forbidding other trailing comments.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Squiz.ControlStructures.ForEachLoopDeclaration: the `SpacingAfterOpen` error code has been replaced by the `SpaceAfterOpen` error code. The latter is a pre-existing code. The former appears to have been a typo. [#582]
    - Thanks to [Dan Wallis][@fredden] for the patch.
- The following sniff(s) have received efficiency improvements:
    - Generic.Classes.DuplicateClassName
    - Generic.NamingConventions.ConstructorName
    - Thanks to [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for the patches.
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Fixed
- Fixed bug [#3808][sq-3808] : Generic.WhiteSpace.ScopeIndent would throw false positive for tab indented multi-token yield from expression.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#630] : The tokenizer could inadvertently transform "normal" parentheses to DNF parentheses, when a function call was preceded by a switch-case / alternative syntax control structure colon.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#645] : On PHP 5.4, if yield was used as the declaration name for a function declared to return by reference, the function name would incorrectly be tokenized as T_YIELD instead of T_STRING.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#647] : Tokenizer not applying tab replacement in single token "yield from" keywords.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#647] : Generic.WhiteSpace.DisallowSpaceIndent did not flag space indentation in multi-line yield from.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#647] : Generic.WhiteSpace.DisallowTabIndent did not flag tabs inside yield from.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#652] : Generic.NamingConventions.ConstructorName: false positives for PHP-4 style calls to PHP-4 style parent constructor when a method with the same name as the parent class was called on another class.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#652] : Generic.NamingConventions.ConstructorName: false negatives for PHP-4 style calls to parent constructor for function calls with whitespace and comments in unconventional places.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#653] : Generic.Classes.DuplicateClassName : the sniff did not skip namespace keywords used as operators, which could lead to false positives.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#653] : Generic.Classes.DuplicateClassName : sniff going into an infinite loop during live coding.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#653] : Generic.Classes.DuplicateClassName : false positives/negatives when a namespace declaration contained whitespace or comments in unconventional places.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#653] : Generic.Classes.DuplicateClassName : namespace for a file going in/out of PHP was not remembered/applied correctly.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

[sq-3808]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3808
[#524]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/524
[#529]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/529
[#560]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/560
[#582]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/582
[#586]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/586
[#625]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/625
[#627]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/627
[#630]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/630
[#633]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/633
[#637]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/637
[#645]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/645
[#647]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/647
[#652]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/652
[#653]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/653
[#658]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/658
[#668]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/668

## [3.10.3] - 2024-09-18

### Changed
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Fixed
- Fixed bug [#553] : Squiz.Classes.SelfMemberReference: false negative(s) when namespace operator was encountered between the namespace declaration and the OO declaration.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#579] : AbstractPatternSniff: potential PHP notice during live coding.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#580] : Squiz.Formatting.OperatorBracket: potential PHP notice during live coding.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#581] : PSR12.ControlStructures.ControlStructureSpacing: prevent fixer conflict by correctly handling multiple empty newlines before the first condition in a multi-line control structure.
    - Thanks to [Dan Wallis][@fredden] for the patch.
- Fixed bug [#585] : Tokenizer not applying tab replacement in heredoc/nowdoc openers.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#588] : Squiz.PHP.EmbeddedPhp false positive when checking spaces after a PHP short open tag.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Fixed bug [#597] : Generic.PHP.LowerCaseKeyword did not flag nor fix non-lowercase anonymous class keywords.
    - Thanks to [Marek Štípek][@maryo] for the patch.
- Fixed bug [#598] : Squiz.PHP.DisallowMultipleAssignments: false positive on assignments to variable property on object stored in array.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#608] : Squiz.Functions.MultiLineFunctionDeclaration did not take (parameter) attributes into account when checking for one parameter per line.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

### Other
- The provenance of PHAR files associated with a release can now be verified via [GitHub Artifact Attestations][ghattest] using the [GitHub CLI tool][ghcli] with the following command: `gh attestation verify [phpcs|phpcbf].phar -o PHPCSStandards`. [#574]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.

[#553]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/553
[#574]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/574
[#579]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/579
[#580]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/580
[#581]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/581
[#585]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/585
[#588]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/588
[#597]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/597
[#598]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/598
[#608]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/608

[ghcli]:    https://cli.github.com/
[ghattest]: https://docs.github.com/en/actions/security-for-github-actions/using-artifact-attestations/using-artifact-attestations-to-establish-provenance-for-builds

## [3.10.2] - 2024-07-22

### Changed
- The following sniff(s) have received efficiency improvements:
    - Generic.Functions.FunctionCallArgumentSpacing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- The array format of the information passed to the `Reports::generateFileReport()` method is now documented in the Reports interface. [#523]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Bill Ruddock][@biinari], [Dan Wallis][@fredden], [Klaus Purer][@klausi], [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Fixed
- Fixed bug [#513] : Generic.Functions.FunctionCallArgumentSpacing did not ignore the body of a match expressions passed as a function argument, which could lead to false positives.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#533] : Generic.WhiteSpace.DisallowTabIndent: tab indentation for heredoc/nowdoc closers will no longer be auto-fixed to prevent parse errors. The issue will still be reported.
    - The error code for heredoc/nowdoc indentation using tabs has been made more specific - `TabsUsedHeredocCloser` - to allow for selectively excluding the indentation check for heredoc/nowdoc closers.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#534] : Generic.WhiteSpace.DisallowSpaceIndent did not report on space indentation for PHP 7.3 flexible heredoc/nowdoc closers.
    - Closers using space indentation will be reported with a dedicated error code: `SpacesUsedHeredocCloser`.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#537] : Squiz.PHP.DisallowMultipleAssignments false positive for list assignments at the start of a new PHP block after an embedded PHP statement.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#551] : Squiz.PHP.DisallowMultipleAssignments prevent false positive for function parameters during live coding.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Fixed bug [#554] : Generic.CodeAnalysis.UselessOverridingMethod edge case false negative when the call to the parent method would end on a PHP close tag.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#555] : Squiz.Classes.SelfMemberReference edge case false negative when the namespace declaration would end on a PHP close tag.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

[#513]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/513
[#523]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/523
[#533]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/533
[#534]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/534
[#537]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/537
[#551]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/551
[#554]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/554
[#555]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/555

## [3.10.1] - 2024-05-22

### Added
- Documentation for the following sniffs:
    - Generic.Commenting.DocComment
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.

### Changed
- The following have received efficiency improvements:
    - Type handling in the PHP Tokenizer
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Fixed
- Fixed bug [#110], [#437], [#475] : `File::findStartOfStatement()`: the start of statement/expression determination for tokens in parentheses/short array brackets/others scopes, nested within match expressions, was incorrect in most cases.
    The trickle down effect of the bug fixes made to the `File::findStartOfStatement()` method, is that the Generic.WhiteSpace.ScopeIndent and the PEAR.WhiteSpace.ScopeIndent sniffs should now be able to correctly determine and fix the indent for match expressions containing nested expressions.
    These fixes also fix an issue with the `Squiz.Arrays.ArrayDeclaration` sniff and possibly other, unreported bugs.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#504] : The tokenizer could inadvertently mistake the last parameter in a function call using named arguments for a DNF type.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#508] : Tokenizer/PHP: extra hardening against handling parse errors in the type handling layer.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

[#110]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/110
[#437]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/437
[#475]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/475
[#504]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/504
[#508]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/508

## [3.10.0] - 2024-05-20

### Added
- Tokenizer support for PHP 8.2 Disjunctive Normal Form (DNF) types. [#3731][sq-3731], [#387], [#461]
    - Includes new `T_TYPE_OPEN_PARENTHESIS` and `T_TYPE_CLOSE_PARENTHESIS` tokens to represent the parentheses in DNF types.
    - These new tokens, like other parentheses, will have the `parenthesis_opener` and `parenthesis_closer` token array indexes set and the tokens between them will have the `nested_parenthesis` index.
    - The `File::getMethodProperties()`, `File::getMethodParameters()` and `File::getMemberProperties()` methods now all support DNF types. [#471], [#472], [#473]
    - Additionally, the following sniff has been updated to support DNF types:
        - Generic.PHP.LowerCaseType [#478]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patches.
- Documentation for the following sniffs:
    - Squiz.WhiteSpace.FunctionClosingBraceSpace
    - Thanks to [Przemek Hernik][@przemekhernik] for the patch.

### Changed
- The help screens have received a face-lift for improved usability and readability. [#447]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch and thanks to [Colin Stewart][@costdev], [Gary Jones][@GaryJones] and [@mbomb007] for reviewing.
- The Squiz.Commenting.ClosingDeclarationComment sniff will now also examine and flag closing comments for traits. [#442]
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- The following sniff(s) have efficiency improvements:
    - Generic.Arrays.ArrayIndent
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- The autoloader will now always return a boolean value indicating whether it has loaded a class or not. [#479]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Dan Wallis][@fredden], [Danny van der Sluijs][@DannyvdSluijs], [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Fixed
- Fixed bug [#466] : Generic.Functions.CallTimePassByReference was not flagging call-time pass-by-reference in class instantiations using the self/parent/static keywords.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Fixed bug [#494] : edge case bug in tokenization of an empty block comment.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#494] : edge case bug in tokenization of an empty single-line DocBlock.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#499] : Generic.ControlStructures.InlineControlStructure now handles statements with a comment between `else` and `if` correctly.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.

[sq-3731]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3731
[#387]:    https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/387
[#442]:    https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/442
[#447]:    https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/447
[#461]:    https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/461
[#466]:    https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/466
[#471]:    https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/471
[#472]:    https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/472
[#473]:    https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/473
[#478]:    https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/478
[#479]:    https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/479
[#494]:    https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/494
[#499]:    https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/499

## [3.9.2] - 2024-04-24

### Changed
- The Generic.ControlStructures.DisallowYodaConditions sniff no longer listens for the null coalesce operator. [#458]
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Dan Wallis][@fredden], [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Fixed
- Fixed bug [#381] : Squiz.Commenting.ClosingDeclarationComment could throw the wrong error when the close brace being examined is at the very end of a file.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Fixed bug [#385] : Generic.CodeAnalysis.JumbledIncrementer improved handling of parse errors/live coding.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Fixed bug [#394] : Generic.Functions.CallTimePassByReference was not flagging call-time pass-by-reference in anonymous class instantiations
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Fixed bug [#420] : PEAR.Functions.FunctionDeclaration could run into a blocking PHP notice while fixing code containing a parse error.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#421] : File::getMethodProperties() small performance improvement & more defensive coding.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#423] : PEAR.WhiteSpace.ScopeClosingBrace would have a fixer conflict with itself when a close tag was preceded by non-empty inline HTML.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#424] : PSR2.Classes.ClassDeclaration using namespace relative interface names in the extends/implements part of a class declaration would lead to a fixer conflict.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#427] : Squiz.Operators.OperatorSpacing would have a fixer conflict with itself when an operator was preceeded by a new line and the previous line ended in a comment.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#430] : Squiz.ControlStructures.ForLoopDeclaration: fixed potential undefined array index notice
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#431] : PSR2.Classes.ClassDeclaration will no longer try to auto-fix multi-line interface implements statements if these are interlaced with comments on their own line. This prevents a potential fixer conflict.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#453] : Arrow function tokenization was broken when the return type was a stand-alone `true` or `false`; or contained `true` or `false` as part of a union type.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

### Other
- [ESLint 9.0] has been released and changes the supported configuration file format.
    The (deprecated) `Generic.Debug.ESLint` sniff only supports the "old" configuration file formats and when using the sniff to run ESLint, the `ESLINT_USE_FLAT_CONFIG=false` environment variable will need to be set when using ESLint >= 9.0.
    For more information, see [#436].


[ESLint 9.0]: https://eslint.org/blog/2024/04/eslint-v9.0.0-released/#flat-config-is-now-the-default-and-has-some-changes

[#381]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/381
[#385]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/385
[#394]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/394
[#420]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/420
[#421]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/421
[#423]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/423
[#424]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/424
[#427]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/427
[#430]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/430
[#431]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/431
[#436]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/436
[#453]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/453
[#458]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/458

## [3.9.1] - 2024-03-31

### Added
- Documentation for the following sniffs:
    - Generic.PHP.RequireStrictTypes
    - Squiz.WhiteSpace.MemberVarSpacing
    - Squiz.WhiteSpace.ScopeClosingBrace
    - Squiz.WhiteSpace.SuperfluousWhitespace
    - Thanks to [Jay McPartland][@jaymcp] and [Rodrigo Primo][@rodrigoprimo] for the patches.

### Changed
- The following sniffs have received performance related improvements:
    - Generic.CodeAnalysis.UselessOverridingMethod
    - Generic.Files.ByteOrderMark
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patches.
- Performance improvement for the "Diff" report. Should be most notable for Windows users. [#355]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- The test suite has received some performance improvements. Should be most notable contributors using Windows. [#351]
    - External standards with sniff tests using the PHP_CodeSniffer native test framework will also benefit from these changes.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch.
- Various housekeeping, including improvements to the tests and documentation.
    - Thanks to [Jay McPartland][@jaymcp], [João Pedro Oliveira][@jpoliveira08], [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for their contributions.

### Fixed
- Fixed bug [#289] : Squiz.WhiteSpace.OperatorSpacing and PSR12.Operators.OperatorSpacing : improved fixer conflict protection by more strenuously avoiding handling operators in declare statements.
    - Thanks to [Dan Wallis][@fredden] for the patch.
- Fixed bug [#366] : Generic.CodeAnalysis.UselessOverridingMethod : prevent false negative when the declared method name and the called method name do not use the same case.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch.
- Fixed bug [#368] : Squiz.Arrays.ArrayDeclaration fixer did not handle static closures correctly when moving array items to their own line.
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch.
- Fixed bug [#404] : Test framework : fixed PHP 8.4 deprecation notice.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

[#289]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/289
[#351]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/351
[#355]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/355
[#366]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/366
[#368]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/368
[#404]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/404

## [3.9.0] - 2024-02-16

### Added
- Tokenizer support for PHP 8.3 typed class constants. [#321]
    - Additionally, the following sniffs have been updated to support typed class constants:
        - Generic.NamingConventions.UpperCaseConstantName [#332]
        - Generic.PHP.LowerCaseConstant [#330]
        - Generic.PHP.LowerCaseType [#331]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patches
- Tokenizer support for PHP 8.3 readonly anonymous classes. [#309]
    - Additionally, the following sniffs have been updated to support readonly anonymous classes:
        - PSR12.Classes.ClassInstantiation [#324]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patches
- New `PHP_CodeSniffer\Sniffs\DeprecatedSniff` interface to allow for marking a sniff as deprecated. [#281]
    - If a ruleset uses deprecated sniffs, deprecation notices will be shown to the end-user before the scan starts.
        When running in `-q` (quiet) mode, the deprecation notices will be hidden.
    - Deprecated sniffs will still run and using them will have no impact on the exit code for a scan.
    - In ruleset "explain"-mode (`-e`) an asterix `*` will show next to deprecated sniffs.
    - Sniff maintainers are advised to read through the PR description for full details on how to use this feature for their own (deprecated) sniffs.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- New `Generic.CodeAnalysis.RequireExplicitBooleanOperatorPrecedence` sniff. [#197]
    - Forbid mixing different binary boolean operators within a single expression without making precedence clear using parentheses
    - Thanks to [Tim Düsterhus][@TimWolla] for the contribution
- Squiz.PHP.EmbeddedPhp : the sniff will now also examine the formatting of embedded PHP statements using short open echo tags. [#27]
    - Includes a new `ShortOpenEchoNoSemicolon` errorcode to allow for selectively ignoring missing semicolons in single line embedded PHP snippets within short open echo tags.
    - The other error codes are the same and do not distinguish between what type of open tag was used.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Documentation for the following sniffs:
    - Generic.WhiteSpace.IncrementDecrementSpacing
    - PSR12.ControlStructures.ControlStructureSpacing
    - PSR12.Files.ImportStatement
    - PSR12.Functions.ReturnTypeDeclaration
    - PSR12.Properties.ConstantVisibility
    - Thanks to [Denis Žoljom][@dingo-d] and [Rodrigo Primo][@rodrigoprimo] for the patches

### Changed
- The Performance report can now also be used for a `phpcbf` run. [#308]
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Sniff tests which extend the PHPCS native `AbstractSniffUnitTest` class will now show a (non-build-breaking) warning when test case files contain fixable errors/warnings, but there is no corresponding `.fixed` file available in the test suite to verify the fixes against. [#336]
    - The warning is only displayed on PHPUnit 7.3.0 and higher.
    - The warning will be elevated to a test failure in PHPCS 4.0.
    - Thanks to [Dan Wallis][@fredden] for the patch
- The following sniffs have received performance related improvements:
    - Squiz.PHP.EmbeddedPhp
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Various housekeeping, including improvements to the tests and documentation
    - Thanks to [Dan Wallis][@fredden], [Joachim Noreiko][@joachim-n], [Remi Collet][@remicollet], [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for their contributions

### Deprecated
- Support for scanning JavaScript and CSS files. See [#2448][sq-2448].
    - This also means that all sniffs which are only aimed at JavaScript or CSS files are now deprecated.
    - The Javascript and CSS Tokenizers, all Javascript and CSS specific sniffs, and support for JS and CSS in select sniffs which support multiple file types, will be removed in version 4.0.0.
- The abstract `PHP_CodeSniffer\Filters\ExactMatch::getBlacklist()` and `PHP_CodeSniffer\Filters\ExactMatch::getWhitelist()` methods are deprecated and will be removed in the 4.0 release. See [#198].
    - In version 4.0, these methods will be replaced with abstract `ExactMatch::getDisallowedFiles()` and `ExactMatch::getAllowedFiles()` methods
    - To make Filters extending `ExactMatch` cross-version compatible with both PHP_CodeSniffer 3.9.0+ as well as 4.0+, implement the new `getDisallowedFiles()` and `getAllowedFiles()` methods.
        - When both the `getDisallowedFiles()` and `getAllowedFiles()` methods as well as the `getBlacklist()` and `getWhitelist()` are available, the new methods will take precedence over the old methods.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The MySource standard and all sniffs in it. See [#2471][sq-2471].
    - The MySource standard and all sniffs in it will be removed in version 4.0.0.
- The `Zend.Debug.CodeAnalyzer` sniff. See [#277].
    - This sniff will be removed in version 4.0.0.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

### Fixed
- Fixed bug [#127] : Squiz.Commenting.FunctionComment : The `MissingParamType` error code will now be used instead of `MissingParamName` when a parameter name is provided, but not its type. Additionally, invalid type hint suggestions will no longer be provided in these cases.
    - Thanks to [Dan Wallis][@fredden] for the patch
- Fixed bug [#196] : Squiz.PHP.EmbeddedPhp : fixer will no longer leave behind trailing whitespace when moving code to another line.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#196] : Squiz.PHP.EmbeddedPhp : will now determine the needed indent with higher precision in multiple situations.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#196] : Squiz.PHP.EmbeddedPhp : fixer will no longer insert a stray new line when the closer of a multi-line embedded PHP block and the opener of the next multi-line embedded PHP block would be on the same line.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#235] : Generic.CodeAnalysis.ForLoopWithTestFunctionCall : prevent a potential PHP 8.3 deprecation notice during live coding
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch
- Fixed bug [#288] : Generic.WhiteSpace.IncrementDecrementSpacing : error message for post-in/decrement will now correctly inform about new lines found before the operator.
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch
- Fixed bug [#296] : Generic.WhiteSpace.ArbitraryParenthesesSpacing : false positive for non-arbitrary parentheses when these follow the scope closer of a `switch` `case`.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#307] : PSR2.Classes.ClassDeclaration : space between a modifier keyword and the `class` keyword was not checked when the space included a new line or comment.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#325] : Squiz.Operators.IncrementDecrementUsage : the sniff was underreporting when there was (no) whitespace and/or comments in unexpected places.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#335] : PSR12.Files.DeclareStatement : bow out in a certain parse error situation to prevent incorrect auto-fixes from being made.
    - Thanks to [Dan Wallis][@fredden] for the patch
- Fixed bug [#340] : Squiz.Commenting.ClosingDeclarationComment : no longer adds a stray newline when adding a missing comment.
    - Thanks to [Dan Wallis][@fredden] for the patch

### Other
- A "Community cc list" has been introduced to ping maintainers of external standards and integrators for input regarding change proposals for PHP_CodeSniffer which may impact them. [#227]
    - For anyone who missed the discussion about this and is interested to be on this list, please feel invited to submit a PR to add yourself.
        The list is located in the `.github` folder.

[sq-2448]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2448
[sq-2471]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2471
[#27]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/27
[#127]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/127
[#196]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/196
[#197]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/197
[#198]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/198
[#227]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/227
[#235]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/235
[#277]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/277
[#281]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/281
[#288]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/288
[#296]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/296
[#307]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/307
[#308]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/308
[#309]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/309
[#321]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/321
[#324]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/324
[#325]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/325
[#330]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/330
[#331]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/331
[#332]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/332
[#335]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/335
[#336]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/336
[#340]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/340

## [3.8.1] - 2024-01-11

### Added
- Documentation has been added for the following sniffs:
    - Generic.CodeAnalysis.EmptyPHPStatement
    - Generic.Formatting.SpaceBeforeCast
    - Generic.PHP.Syntax
    - Generic.WhiteSpace.LanguageConstructSpacing
    - PSR12.Classes.ClosingBrace
    - PSR12.Classes.OpeningBraceSpace
    - PSR12.ControlStructures.BooleanOperatorPlacement
    - PSR12.Files.OpenTag
    - Thanks to [Rodrigo Primo][@rodrigoprimo] and [Denis Žoljom][@dingo-d] for the patches

### Changed
- GitHub releases will now always only contain unversioned release assets (PHARS + asc files) (same as it previously was in the squizlabs repo). See [#205] for context.
    - Thanks to [Shivam Mathur][@shivammathur] for opening a discussion about this
- Various housekeeping, includes improvements to the tests and documentation
    - Thanks to [Dan Wallis][@fredden], [Lucas Hoffmann][@lucc], [Rodrigo Primo][@rodrigoprimo] and [Juliette Reinders Folmer][@jrfnl] for their contributions

### Fixed
- Fixed bug [#124] : Report Full : avoid unnecessarily wrapping lines when `-s` is used
    - Thanks to [Brad Jorsch][@anomiex] for the patch
- Fixed bug [#124] : Report Full : fix incorrect bolding of pipes when `-s` is used and messages wraps
    - Thanks to [Brad Jorsch][@anomiex] for the patch
- Fixed bug [#150] : Squiz.WhiteSpace.KeywordSpacing : prevent a PHP notice when run during live coding
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#154] : Report Full : delimiter line calculation could go wonky on wide screens when a report contains multi-line messages
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#178] : Squiz.Commenting.VariableComment : docblocks were incorrectly being flagged as missing when a property declaration used PHP native union/intersection type declarations
    - Thanks to [Ferdinand Kuhl][@fcool] for the patch
- Fixed bug [#211] : Squiz.Commenting.VariableComment : docblocks were incorrectly being flagged as missing when a property declaration used PHP 8.2+ stand-alone `true`/`false`/`null` type declarations
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#211] : Squiz.Commenting.VariableComment : docblocks were incorrectly being flagged as missing when a property declaration used PHP native `parent`, `self` or a namespace relative class name type declaration
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#226] : Generic.CodeAnalysis.ForLoopShouldBeWhileLoop : prevent a potential PHP 8.3 deprecation notice during live coding
    - Thanks to [Rodrigo Primo][@rodrigoprimo] for the patch

[#124]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/124
[#150]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/150
[#154]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/154
[#178]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/178
[#205]: https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/205
[#211]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/211
[#226]: https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/226

## [3.8.0] - 2023-12-08

[Squizlabs/PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) is dead. Long live [PHPCSStandards/PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer)!

### Breaking Changes
- The `squizlabs/PHP_CodeSniffer` repository has been abandoned. The `PHPCSStandards/PHP_CodeSniffer` repository will serve as the continuation of the project. For more information about this change, please read the [announcement](https://github.com/squizlabs/PHP_CodeSniffer/issues/3932).
    - Installation of PHP_CodeSniffer via PEAR is no longer supported.
        - Users will need to switch to another installation method.
        - Note: this does not affect the PEAR sniffs.
    - For Composer users, nothing changes.
        - **_In contrast to earlier information, the `squizlabs/php_codesniffer` package now points to the new repository and everything will continue to work as before._**
    - PHIVE users may need to clear the PHIVE URL cache.
        - PHIVE users who don't use the package alias, but refer to the package URL, will need to update the URL from `https://squizlabs.github.io/PHP_CodeSniffer/phars/` to `https://phars.phpcodesniffer.com/phars/`.
    - Users who download the PHAR files using curl or wget, will need to update the download URL from `https://squizlabs.github.io/PHP_CodeSniffer/[phpcs|phpcbf].phar` or `https://github.com/squizlabs/PHP_CodeSniffer/releases/latest/download/[phpcs|phpcbf].phar` to `https://phars.phpcodesniffer.com/[phpcs|phpcbf].phar`.
    - For users who install PHP_CodeSniffer via the [Setup-PHP](https://github.com/shivammathur/setup-php/) action runner for GitHub Actions, nothing changes.
    - Users using a git clone will need to update the clone address from `git@github.com:squizlabs/PHP_CodeSniffer.git` to `git@github.com:PHPCSStandards/PHP_CodeSniffer.git`.
        - Contributors will need to fork the new repo and add both the new fork as well as the new repo as remotes to their local git copy of PHP_CodeSniffer.
        - Users who have (valid) open issues or pull requests in the `squizlabs/PHP_CodeSniffer` repository are invited to resubmit these to the `PHPCSStandards/PHP_CodeSniffer` repository.

### Added
- Runtime support for PHP 8.3. All known PHP 8.3 deprecation notices have been fixed
    - Syntax support for new PHP 8.3 features will follow in a future release
    - If you find any PHP 8.3 deprecation notices which were missed, please report them
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patches
- Added support for PHP 8.2 readonly classes to File::getClassProperties() through a new is_readonly array index in the return value
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added support for PHP 8.2 readonly classes to a number of sniffs
    - Generic.CodeAnalysis.UnnecessaryFinalModifier
    - PEAR.Commenting.ClassComment
    - PEAR.Commenting.FileComment
    - PSR1.Files.SideEffects
    - PSR2.Classes.ClassDeclaration
    - PSR12.Files.FileHeader
    - Squiz.Classes.ClassDeclaration
    - Squiz.Classes.LowercaseClassKeywords
    - Squiz.Commenting.ClassComment
    - Squiz.Commenting.DocCommentAlignment
    - Squiz.Commenting.FileComment
    - Squiz.Commenting.InlineComment
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added support for PHP 8.2 `true` as a stand-alone type declaration
    - The `File::getMethodProperties()`, `File::getMethodParameters()` and `File::getMemberProperties()` methods now all support the `true` type
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added support for PHP 8.2 `true` as a stand-alone type to a number of sniffs
    - Generic.PHP.LowerCaseType
    - PSr12.Functions.NullableTypeDeclaration
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added a Performance report to allow for finding "slow" sniffs
    - To run this report, run PHPCS with --report=Performance.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.PHP.RequireStrictTypes : new warning for when there is a declare statement, but the strict_types directive is set to 0
    - The warning can be turned off by excluding the `Generic.PHP.RequireStrictTypes.Disabled` error code
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Commenting.FunctionComment : new `ParamNameUnexpectedAmpersandPrefix` error for parameters annotated as passed by reference while the parameter is not passed by reference
    - Thanks to [Dan Wallis][@fredden] for the patch
- Documentation has been added for the following sniffs:
    - PSR2.Files.ClosingTag
    - PSR2.Methods.FunctionCallSignature
    - PSR2.Methods.FunctionClosingBrace
    - Thanks to [Atsushi Okui][@blue32a] for the patch
- Support for PHPUnit 8 and 9 to the test suite
    - Test suites for external standards which run via the PHPCS native test suite can now run on PHPUnit 4-9 (was 4-7)
    - If any of these tests use the PHPUnit `setUp()`/`tearDown()` methods or overload the `setUp()` in the `AbstractSniffUnitTest` test case, they will need to be adjusted. See the [PR details for further information](https://github.com/PHPCSStandards/PHP_CodeSniffer/pull/59/commits/bc302dd977877a22c5e60d42a2f6b7d9e9192dab)
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

### Changed
- Changes have been made to the way PHPCS handles invalid sniff properties being set in a custom ruleset
    - Fixes PHP 8.2 deprecation notices for properties set in a (custom) ruleset for complete standards/complete sniff categories
    - Invalid sniff properties set for individual sniffs will now result in an error and halt the execution of PHPCS
        - A descriptive error message is provided to allow users to fix their ruleset
    - Sniff properties set for complete standards/complete sniff categories will now only be set on sniffs which explicitly support the property
        - The property will be silently ignored for those sniffs which do not support the property
    - Invalid sniff properties set for sniffs via inline annotations will result in an informative `Internal.PropertyDoesNotExist` errror on line 1 of the scanned file, but will not halt the execution of PHPCS
    - For sniff developers, it is strongly recommended for sniffs to explicitly declare any user-adjustable public properties
        - If dynamic properties need to be supported for a sniff, either declare the magic __set()/__get()/__isset()/__unset() methods on the sniff or let the sniff extend stdClass
        - Note: The `#[\AllowDynamicProperties]` attribute will have no effect for properties which are being set in rulesets
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The third parameter for the Ruleset::setSniffProperty() method has been changed to expect an array
    - Sniff developers/integrators of PHPCS may need to make some small adjustments to allow for this change
    - Existing code will continue to work but will throw a deprecation error
    - The backwards compatiblity layer will be removed in PHPCS 4.0
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- When using `auto` report width (the default) a value of 80 columns will be used if the width cannot be determined
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Sniff error messages are now more informative to help bugs get reported to the correct project
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.CodeAnalysis.UnusedFunctionParameter will now ignore magic methods for which the signature is defined by PHP
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.Functions.OpeningFunctionBraceBsdAllman will now check the brace indent before the opening brace for empty functions
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.Functions.OpeningFunctionBraceKernighanRitchie will now check the spacing before the opening brace for empty functions
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.WhiteSpace.IncrementDecrementSpacing now detects more spacing issues
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- PSR2.Classes.PropertyDeclaration now enforces that the readonly modifier comes after the visibility modifier
    - PSR2 and PSR12 do not have documented rules for this as they pre-date the readonly modifier
    - PSR-PER has been used to confirm the order of this keyword so it can be applied to PSR2 and PSR12 correctly
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- PEAR.Commenting.FunctionComment + Squiz.Commenting.FunctionComment: the SpacingAfter error can now be auto-fixed
    - Thanks to [Dan Wallis][@fredden] for the patch
- Squiz.PHP.InnerFunctions sniff no longer reports on OO methods for OO structures declared within a function or closure
    - Thanks to [@Daimona] for the patch
- Squiz.PHP.NonExecutableCode will now also flag redundant return statements just before a closure close brace
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Runtime performance improvement for PHPCS CLI users. The improvement should be most noticeable for users on Windows.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The following sniffs have received performance related improvements:
    - Generic.PHP.LowerCaseConstant
    - Generic.PHP.LowerCaseType
    - PSR12.Files.OpenTag
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patches
- The -e (explain) command will now list sniffs in natural order
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Tests using the PHPCS native test framework with multiple test case files will now run the test case files in numeric order.
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The following sniffs have received minor message readability improvements:
    - Generic.Arrays.ArrayIndent
    - Generic.Formatting.SpaceAfterCast
    - Generic.Formatting.SpaceAfterNot
    - Generic.WhiteSpace.SpreadOperatorSpacingAfter
    - Squiz.Arrays.ArrayDeclaration
    - Squiz.Commenting.DocCommentAlignment
    - Squiz.ControlStructures.ControlSignature
    - Thanks to [Danny van der Sluijs][@DannyvdSluijs] and [Juliette Reinders Folmer][@jrfnl] for the patches
- Improved README syntax highlighting
    - Thanks to [Benjamin Loison][@Benjamin-Loison] for the patch
- Various documentation improvements
    - Thanks to [Andrew Dawes][@AndrewDawes], [Danny van der Sluijs][@DannyvdSluijs] and [Juliette Reinders Folmer][@jrfnl] for the patches

### Removed
- Removed support for installation via PEAR
    - Use composer or the PHAR files instead

### Fixed
- Fixed bug [#2857][sq-2857] : Squiz/NonExecutableCode: prevent false positives when exit is used in a ternary expression or as default with null coalesce
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3386][sq-3386] : PSR1/SideEffects : improved recognition of disable/enable annotations
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3557][sq-3557] : Squiz.Arrays.ArrayDeclaration will now ignore PHP 7.4 array unpacking when determining whether an array is associative
    - Thanks to [Volker Dusch][@edorian] for the patch
- Fixed bug [#3592][sq-3592] : Squiz/NonExecutableCode: prevent false positives when a PHP 8.0+ inline throw expression is encountered
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3715][sq-3715] : Generic/UnusedFunctionParameter: fixed incorrect errorcode for closures/arrow functions nested within extended classes/classes which implement
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3717][sq-3717] : Squiz.Commenting.FunctionComment: fixed false positive for `InvalidNoReturn` when type is never
    - Thanks to [Choraimy Kroonstuiver][@axlon] for the patch
- Fixed bug [#3720][sq-3720] : Generic/RequireStrictTypes : will now bow out silently in case of parse errors/live coding instead of throwing false positives/false negatives
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3720][sq-3720] : Generic/RequireStrictTypes : did not handle multi-directive declare statements
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3722][sq-3722] : Potential "Uninitialized string offset 1" in octal notation backfill
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3736][sq-3736] : PEAR/FunctionDeclaration: prevent fixer removing the close brace (and creating a parse error) when there is no space between the open brace and close brace of a function
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3739][sq-3739] : PEAR/FunctionDeclaration: prevent fixer conflict, and potentially creating a parse error, for unconventionally formatted return types
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3770][sq-3770] : Squiz/NonExecutableCode: prevent false positives for switching between PHP and HTML
    - Thanks to [Dan Wallis][@fredden] for the patch
- Fixed bug [#3773][sq-3773] : Tokenizer/PHP: tokenization of the readonly keyword when used in combination with PHP 8.2 disjunctive normal types
    - Thanks to [Dan Wallis][@fredden] and [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3776][sq-3776] : Generic/JSHint: error when JSHint is not available
    - Thanks to [Dan Wallis][@fredden] for the patch
- Fixed bug [#3777][sq-3777] : Squiz/NonExecutableCode: slew of bug fixes, mostly related to modern PHP
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3778][sq-3778] : Squiz/LowercasePHPFunctions: bug fix for class names in attributes
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3779][sq-3779] : Generic/ForbiddenFunctions: bug fix for class names in attributes
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3785][sq-3785] : Squiz.Commenting.FunctionComment: potential "Uninitialized string offset 0" when a type contains a duplicate pipe symbol
    - Thanks to [Dan Wallis][@fredden] for the patch
- Fixed bug [#3787][sq-3787] : `PEAR/Squiz/[MultiLine]FunctionDeclaration`: allow for PHP 8.1 new in initializers
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3789][sq-3789] : Incorrect tokenization for ternary operator with `match` inside of it
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3790][sq-3790] : PSR12/AnonClassDeclaration: prevent fixer creating parse error when there was no space before the open brace
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3797][sq-3797] : Tokenizer/PHP: more context sensitive keyword fixes
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3801][sq-3801] : File::getMethodParameters(): allow for readonly promoted properties without visibility
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3805][sq-3805] : Generic/FunctionCallArgumentSpacing: prevent fixer conflict over PHP 7.3+ trailing comma's in function calls
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3806][sq-3806] : Squiz.PHP.InnerFunctions sniff now correctly reports inner functions declared within a closure
    - Thanks to [@Daimona] for the patch
- Fixed bug [#3809][sq-3809] : GitBlame report was broken when passing a basepath
    - Thanks to [Chris][@datengraben] for the patch
- Fixed bug [#3813][sq-3813] : Squiz.Commenting.FunctionComment: false positive for parameter name mismatch on parameters annotated as passed by reference
    - Thanks to [Dan Wallis][@fredden] for the patch
- Fixed bug [#3833][sq-3833] : Generic.PHP.LowerCaseType: fixed potential undefined array index notice
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3846][sq-3846] : PSR2.Classes.ClassDeclaration.CloseBraceAfterBody : fixer will no longer remove indentation on the close brace line
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3854][sq-3854] : Fatal error when using Gitblame report in combination with `--basepath` and running from project subdirectory
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3856][sq-3856] : PSR12.Traits.UseDeclaration was using the wrong error code - SpacingAfterAs - for spacing issues after the `use` keyword
    - These will now be reported using the SpacingAfterUse error code
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3856][sq-3856] : PSR12.Traits.UseDeclaration did not check spacing after `use` keyword for multi-line trait use statements
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3867][sq-3867] : Tokenizer/PHP: union type and intersection type operators were not correctly tokenized for static properties without explicit visibility
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3877][sq-3877] : Filter names can be case-sensitive. The -h help text will now display the correct case for the available filters
    - Thanks to [@simonsan] for the patch
- Fixed bug [#3893][sq-3893] : Generic/DocComment : the SpacingAfterTagGroup fixer could accidentally remove ignore annotations
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3898][sq-3898] : Squiz/NonExecutableCode : the sniff could get confused over comments in unexpected places
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3904][sq-3904] : Squiz/FunctionSpacing : prevent potential fixer conflict
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3906][sq-3906] : Tokenizer/CSS: bug fix related to the unsupported slash comment syntax
    - Thanks to [Dan Wallis][@fredden] for the patch
- Fixed bug [#3913][sq-3913] : Config did not always correctly store unknown "long" arguments in the `$unknown` property
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

Thanks go to [Dan Wallis][@fredden] and [Danny van der Sluijs][@DannyvdSluijs] for reviewing quite a few of the PRs for this release.
Additionally, thanks to [Alexander Turek][@derrabus] for consulting on the repo change over.

[sq-2857]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2857
[sq-3386]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3386
[sq-3557]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3557
[sq-3592]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3592
[sq-3715]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3715
[sq-3717]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3717
[sq-3720]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3720
[sq-3722]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3722
[sq-3736]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3736
[sq-3739]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3739
[sq-3770]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3770
[sq-3773]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3773
[sq-3776]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3776
[sq-3777]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3777
[sq-3778]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3778
[sq-3779]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3779
[sq-3785]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3785
[sq-3787]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3787
[sq-3789]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3789
[sq-3790]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3790
[sq-3797]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3797
[sq-3801]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3801
[sq-3805]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3805
[sq-3806]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3806
[sq-3809]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3809
[sq-3813]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3813
[sq-3833]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3833
[sq-3846]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3846
[sq-3854]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3854
[sq-3856]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3856
[sq-3867]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3867
[sq-3877]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3877
[sq-3893]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3893
[sq-3898]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3898
[sq-3904]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3904
[sq-3906]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3906
[sq-3913]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3913

## [3.7.2] - 2023-02-23

### Changed
- Newer versions of Composer will now suggest installing PHPCS using require-dev instead of require
    - Thanks to [Gary Jones][@GaryJones] for the patch
- A custom Out Of Memory error will now be shown if PHPCS or PHPCBF run out of memory during a run
    - Error message provides actionable information about how to fix the problem and ensures the error is not silent
    - Thanks to [Juliette Reinders Folmer][@jrfnl] and [Alain Schlesser][@schlessera] for the patch
- Generic.PHP.LowerCaseType sniff now correctly examines types inside arrow functions
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Formatting.OperatorBracket no longer reports false positives in match() structures

### Fixed
- Fixed bug [#3616][sq-3616] : Squiz.PHP.DisallowComparisonAssignment false positive for PHP 8 match expression
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3618][sq-3618] : Generic.WhiteSpace.ArbitraryParenthesesSpacing false positive for return new parent()
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3632][sq-3632] : Short list not tokenized correctly in control structures without braces
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3639][sq-3639] : Tokenizer not applying tab replacement to heredoc/nowdoc closers
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3640][sq-3640] : Generic.WhiteSpace.DisallowTabIndent not reporting errors for PHP 7.3 flexible heredoc/nowdoc syntax
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3645][sq-3645] : PHPCS can show 0 exit code when running in parallel even if child process has fatal error
    - Thanks to [Alex Panshin][@enl] for the patch
- Fixed bug [#3653][sq-3653] : False positives for match() in OperatorSpacingSniff
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- Fixed bug [#3666][sq-3666] : PEAR.Functions.FunctionCallSignature incorrect indent fix when checking mixed HTML/PHP files
- Fixed bug [#3668][sq-3668] : PSR12.Classes.ClassInstantiation.MissingParentheses false positive when instantiating parent classes
    - Similar issues also fixed in Generic.Functions.FunctionCallArgumentSpacing and Squiz.Formatting.OperatorBracket
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3672][sq-3672] : Incorrect ScopeIndent.IncorrectExact report for match inside array literal
- Fixed bug [#3694][sq-3694] : Generic.WhiteSpace.SpreadOperatorSpacingAfter does not ignore spread operator in PHP 8.1 first class   callables
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

[sq-3616]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3616
[sq-3618]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3618
[sq-3632]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3632
[sq-3639]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3639
[sq-3640]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3640
[sq-3645]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3645
[sq-3653]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3653
[sq-3666]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3666
[sq-3668]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3668
[sq-3672]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3672
[sq-3694]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3694

## [3.7.1] - 2022-06-18

### Fixed
- Fixed bug [#3609][sq-3609] : Methods/constants with name empty/isset/unset are always reported as error
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

[sq-3609]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3609

## [3.7.0] - 2022-06-13

### Added
- Added support for PHP 8.1 explicit octal notation
    - This new syntax has been backfilled for PHP versions less than 8.1
    - Thanks to [Mark Baker][@MarkBaker] for the patch
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for additional fixes
- Added support for PHP 8.1 enums
    - This new syntax has been backfilled for PHP versions less than 8.1
    - Includes a new T_ENUM_CASE token to represent the case statements inside an enum
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for additional core and sniff support
- Added support for the PHP 8.1 readonly token
    - Tokenizing of the readonly keyword has been backfilled for PHP versions less than 8.1
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- Added support for PHP 8.1 intersection types
    - Includes a new T_TYPE_INTERSECTION token to represent the ampersand character inside intersection types
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch

### Changed
- File::getMethodParameters now supports the new PHP 8.1 readonly token
    - When constructor property promotion is used, a new property_readonly array index is included in the return value
        - This is a boolean value indicating if the property is readonly
    - If the readonly token is detected, a new readonly_token array index is included in the return value
        - This contains the token index of the readonly keyword
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Support for new PHP 8.1 readonly keyword has been added to the following sniffs:
    - Generic.PHP.LowerCaseKeyword
    - PSR2.Classes.PropertyDeclaration
    - Squiz.Commenting.BlockComment
    - Squiz.Commenting.DocCommentAlignment
    - Squiz.Commenting.VariableComment
    - Squiz.WhiteSpace.ScopeKeywordSpacing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patches
- The parallel feature is now more efficient and runs faster in some situations due to improved process management
    - Thanks to [Sergei Morozov][@morozov] for the patch
- The list of installed coding standards now has consistent ordering across all platforms
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.PHP.UpperCaseConstant and Generic.PHP.LowerCaseConstant now ignore type declarations
    - These sniffs now only report errors for true/false/null when used as values
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.PHP.LowerCaseType now supports the PHP 8.1 never type
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch

### Fixed
- Fixed bug [#3502][sq-3502] : A match statement within an array produces Squiz.Arrays.ArrayDeclaration.NoKeySpecified
- Fixed bug [#3503][sq-3503] : Squiz.Commenting.FunctionComment.ThrowsNoFullStop false positive when one line @throw
- Fixed bug [#3505][sq-3505] : The nullsafe operator is not counted in Generic.Metrics.CyclomaticComplexity
    - Thanks to [Mark Baker][@MarkBaker] for the patch
- Fixed bug [#3526][sq-3526] : PSR12.Properties.ConstantVisibility false positive when using public final const syntax
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3530][sq-3530] : Line indented incorrectly false positive when using match-expression inside switch case
- Fixed bug [#3534][sq-3534] : Name of typed enum tokenized as T_GOTO_LABEL
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3546][sq-3546] : Tokenizer/PHP: bug fix - parent/static keywords in class instantiations
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3550][sq-3550] : False positive from PSR2.ControlStructures.SwitchDeclaration.TerminatingComment when using trailing   comment
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3575][sq-3575] :  Squiz.Scope.MethodScope misses visibility keyword on previous line
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3604][sq-3604] :  Tokenizer/PHP: bug fix for double quoted strings using ${
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

[sq-3502]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3502
[sq-3503]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3503
[sq-3505]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3505
[sq-3526]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3526
[sq-3530]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3530
[sq-3534]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3534
[sq-3546]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3546
[sq-3550]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3550
[sq-3575]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3575
[sq-3604]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3604

## [3.6.2] - 2021-12-13

### Changed
- Processing large code bases that use tab indenting inside comments and strings will now be faster
    - Thanks to [Thiemo Kreuz][@thiemowmde] for the patch

### Fixed
- Fixed bug [#3388][sq-3388] : phpcs does not work when run from WSL drives
    - Thanks to [Juliette Reinders Folmer][@jrfnl] and [Graham Wharton][@gwharton] for the patch
- Fixed bug [#3422][sq-3422] : Squiz.WhiteSpace.ScopeClosingBrace fixer removes HTML content when fixing closing brace alignment
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3437][sq-3437] : PSR12 does not forbid blank lines at the start of the class body
    - Added new PSR12.Classes.OpeningBraceSpace sniff to enforce this
- Fixed bug [#3440][sq-3440] : Squiz.WhiteSpace.MemberVarSpacing false positives when attributes used without docblock
    - Thanks to [Vadim Borodavko][@javer] for the patch
- Fixed bug [#3448][sq-3448] : PHP 8.1 deprecation notice while generating running time value
    - Thanks to [Juliette Reinders Folmer][@jrfnl] and [Andy Postnikov][@andypost] for the patch
- Fixed bug [#3456][sq-3456] : PSR12.Classes.ClassInstantiation.MissingParentheses false positive using attributes on anonymous class
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3460][sq-3460] : Generic.Formatting.MultipleStatementAlignment false positive on closure with parameters
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3468][sq-3468] : do/while loops are double-counted in Generic.Metrics.CyclomaticComplexity
    - Thanks to [Mark Baker][@MarkBaker] for the patch
- Fixed bug [#3469][sq-3469] : Ternary Operator and Null Coalescing Operator are not counted in Generic.Metrics.CyclomaticComplexity
    - Thanks to [Mark Baker][@MarkBaker] for the patch
- Fixed bug [#3472][sq-3472] : PHP 8 match() expression is not counted in Generic.Metrics.CyclomaticComplexity
    - Thanks to [Mark Baker][@MarkBaker] for the patch

[sq-3388]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3388
[sq-3422]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3422
[sq-3437]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3437
[sq-3440]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3440
[sq-3448]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3448
[sq-3456]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3456
[sq-3460]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3460
[sq-3468]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3468
[sq-3469]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3469
[sq-3472]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3472

## [3.6.1] - 2021-10-11

### Changed
- PHPCS annotations can now be specified using hash-style comments
    - Previously, only slash-style and block-style comments could be used to do things like disable errors
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The new PHP 8.1 tokenization for ampersands has been reverted to use the existing PHP_CodeSniffer method
    - The PHP 8.1 tokens T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG and T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG are unused
    - Ampersands continue to be tokenized as T_BITWISE_AND for all PHP versions
    - Thanks to [Juliette Reinders Folmer][@jrfnl] and [Anna Filina][@afilina] for the patch
- File::getMethodParameters() no longer incorrectly returns argument attributes in the type hint array index
    - A new has_attributes array index is available and set to TRUE if the argument has attributes defined
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed an issue where some sniffs would not run on PHP files that only used the short echo tag
    - The following sniffs were affected:
        - Generic.Files.ExecutableFile
        - Generic.Files.LowercasedFilename
        - Generic.Files.LineEndings
        - Generic.Files.EndFileNewline
        - Generic.Files.EndFileNoNewline
        - Generic.PHP.ClosingPHPTag
        - Generic.PHP.Syntax
        - Generic.VersionControl.GitMergeConflict
        - Generic.WhiteSpace.DisallowSpaceIndent
        - Generic.WhiteSpace.DisallowTabIndent
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Commenting.BlockComment now correctly applies rules for block comments after a short echo tag
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

### Fixed
- Generic.NamingConventions.ConstructorName no longer throws deprecation notices on PHP 8.1
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed false positives when using attributes in the following sniffs:
    - PEAR.Commenting.FunctionComment
    - Squiz.Commenting.InlineComment
    - Squiz.Commenting.BlockComment
    - Squiz.Commenting.VariableComment
    - Squiz.WhiteSpace.MemberVarSpacing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3294][sq-3294] : Bug in attribute tokenization when content contains PHP end token or attribute closer on new line
    - Thanks to [Alessandro Chitolina][@alekitto] for the patch
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the tests
- Fixed bug [#3296][sq-3296] : PSR2.ControlStructures.SwitchDeclaration takes phpcs:ignore as content of case body
- Fixed bug [#3297][sq-3297] : PSR2.ControlStructures.SwitchDeclaration.TerminatingComment does not handle try/finally blocks
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3302][sq-3302] : PHP 8.0 | Tokenizer/PHP: bugfix for union types using namespace operator
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3303][sq-3303] : findStartOfStatement() doesn't work with T_OPEN_TAG_WITH_ECHO
- Fixed bug [#3316][sq-3316] : Arrow function not tokenized correctly when using null in union type
- Fixed bug [#3317][sq-3317] : Problem with how phpcs handles ignored files when running in parallel
    - Thanks to [Emil Andersson][@emil-nasso] for the patch
- Fixed bug [#3324][sq-3324] : PHPCS hangs processing some nested arrow functions inside a function call
- Fixed bug [#3326][sq-3326] : Generic.Formatting.MultipleStatementAlignment error with const DEFAULT
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3333][sq-3333] : Squiz.Objects.ObjectInstantiation: null coalesce operators are not recognized as assignment
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3340][sq-3340] : Ensure interface and trait names are always tokenized as T_STRING
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3342][sq-3342] : PSR12/Squiz/PEAR standards all error on promoted properties with docblocks
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3345][sq-3345] : IF statement with no braces and double catch turned into syntax error by auto-fixer
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3352][sq-3352] : PSR2.ControlStructures.SwitchDeclaration can remove comments on the same line as the case statement while fixing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3357][sq-3357] : Generic.Functions.OpeningFunctionBraceBsdAllman removes return type when additional lines are present
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3362][sq-3362] : Generic.WhiteSpace.ScopeIndent false positive for arrow functions inside arrays
- Fixed bug [#3384][sq-3384] : Squiz.Commenting.FileComment.SpacingAfterComment false positive on empty file
- Fixed bug [#3394][sq-3394] : Fix PHP 8.1 auto_detect_line_endings deprecation notice
- Fixed bug [#3400][sq-3400] : PHP 8.1: prevent deprecation notices about missing return types
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3424][sq-3424] : PHPCS fails when using PHP 8 Constructor property promotion with attributes
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3425][sq-3425] : PHP 8.1 | Runner::processChildProcs(): fix passing null to non-nullable bug
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3445][sq-3445] : Nullable parameter after attribute incorrectly tokenized as ternary operator
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

[sq-3294]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3294
[sq-3296]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3296
[sq-3297]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3297
[sq-3302]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3302
[sq-3303]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3303
[sq-3316]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3316
[sq-3317]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3317
[sq-3324]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3324
[sq-3326]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3326
[sq-3333]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3333
[sq-3340]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3340
[sq-3342]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3342
[sq-3345]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3345
[sq-3352]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3352
[sq-3357]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3357
[sq-3362]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3362
[sq-3384]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3384
[sq-3394]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3394
[sq-3400]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3400
[sq-3424]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3424
[sq-3425]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3425
[sq-3445]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3445

## [3.6.0] - 2021-04-09

### Added
- Added support for PHP 8.0 union types
    - A new T_TYPE_UNION token is available to represent the pipe character
    - File::getMethodParameters(), getMethodProperties(), and getMemberProperties() will now return union types
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added support for PHP 8.0 named function call arguments
    - A new T_PARAM_NAME token is available to represent the label with the name of the function argument in it
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added support for PHP 8.0 attributes
    - The PHP-supplied T_ATTRIBUTE token marks the start of an attribute
    - A new T_ATTRIBUTE_END token is available to mark the end of an attribute
    - New attribute_owner and attribute_closer indexes are available in the tokens array for all tokens inside an attribute
    - Tokenizing of attributes has been backfilled for older PHP versions
    - The following sniffs have been updated to support attributes:
        - PEAR.Commenting.ClassComment
        - PEAR.Commenting.FileComment
        - PSR1.Files.SideEffects
        - PSR12.Files.FileHeader
        - Squiz.Commenting.ClassComment
        - Squiz.Commenting.FileComment
        - Squiz.WhiteSpace.FunctionSpacing
            - Thanks to [Vadim Borodavko][@javer] for the patch
    - Thanks to [Alessandro Chitolina][@alekitto] for the patch
- Added support for PHP 8.0 dereferencing of text strings with interpolated variables
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added support for PHP 8.0 match expressions
    - Match expressions are now tokenized with parenthesis and scope openers and closers
        - Sniffs can listen for the T_MATCH token to process match expressions
        - Note that the case and default statements inside match expressions do not have scopes set
    - A new T_MATCH_ARROW token is available to represent the arrows in match expressions
    - A new T_MATCH_DEFAULT token is available to represent the default keyword in match expressions
    - All tokenizing of match expressions has been backfilled for older PHP versions
    - The following sniffs have been updated to support match expressions:
        - Generic.CodeAnalysis.AssignmentInCondition
        - Generic.CodeAnalysis.EmptyPHPStatement
            - Thanks to [Vadim Borodavko][@javer] for the patch
        - Generic.CodeAnalysis.EmptyStatement
        - Generic.PHP.LowerCaseKeyword
        - PEAR.ControlStructures.ControlSignature
        - PSR12.ControlStructures.BooleanOperatorPlacement
        - Squiz.Commenting.LongConditionClosingComment
        - Squiz.Commenting.PostStatementComment
        - Squiz.ControlStructures.LowercaseDeclaration
        - Squiz.ControlStructures.ControlSignature
        - Squiz.Formatting.OperatorBracket
        - Squiz.PHP.DisallowMultipleAssignments
        - Squiz.Objects.ObjectInstantiation
        - Squiz.WhiteSpace.ControlStructureSpacing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added Generic.NamingConventions.AbstractClassNamePrefix to enforce that class names are prefixed with "Abstract"
    - Thanks to [Anna Borzenko][@annechko] for the contribution
- Added Generic.NamingConventions.InterfaceNameSuffix to enforce that interface names are suffixed with "Interface"
    - Thanks to [Anna Borzenko][@annechko] for the contribution
- Added Generic.NamingConventions.TraitNameSuffix to enforce that trait names are suffixed with "Trait"
    - Thanks to [Anna Borzenko][@annechko] for the contribution

### Changed
- The value of the T_FN_ARROW token has changed from "T_FN_ARROW" to "PHPCS_T_FN_ARROW" to avoid package conflicts
    - This will have no impact on custom sniffs unless they are specifically looking at the value of the T_FN_ARROW constant
    - If sniffs are just using constant to find arrow functions, they will continue to work without modification
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- File::findStartOfStatement() now works correctly when passed the last token in a statement
- File::getMethodParameters() now supports PHP 8.0 constructor property promotion
    - Returned method params now include a "property_visibility" and "visibility_token" index if property promotion is detected
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- File::getMethodProperties() now includes a "return_type_end_token" index in the return value
    - This indicates the last token in the return type, which is helpful when checking union types
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Include patterns are now ignored when processing STDIN
    - Previously, checks using include patterns were excluded when processing STDIN when no file path was provided via --stdin-path
    - Now, all include and exclude rules are ignored when no file path is provided, allowing all checks to run
    - If you want include and exclude rules enforced when checking STDIN, use --stdin-path to set the file path
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Spaces are now correctly escaped in the paths to external on Windows
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.CodeAnalysis.UnusedFunctionParameter can now be configured to ignore variable usage for specific type hints
    - This allows you to suppress warnings for some variables that are not required, but leave warnings for others
    - Set the ignoreTypeHints array property to a list of type hints to ignore
    - Thanks to [Petr Bugyík][@o5] for the patch
- Generic.Formatting.MultipleStatementAlignment can now align statements at the start of the assignment token
    - Previously, the sniff enforced that the values were aligned, even if this meant the assignment tokens were not
    - Now, the sniff can enforce that the assignment tokens are aligned, even if this means the values are not
    - Set the "alignAtEnd" sniff property to "false" to align the assignment tokens
    - The default remains at "true", so the assigned values are aligned
    - Thanks to [John P. Bloch][@johnpbloch] for the patch
- Generic.PHP.LowerCaseType now supports checking of typed properties
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.PHP.LowerCaseType now supports checking of union types
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- PEAR.Commenting.FunctionComment and Squiz.Commenting.FunctionComment sniffs can now ignore private and protected methods
    - Set the "minimumVisibility" sniff property to "protected" to ignore private methods
    - Set the "minimumVisibility" sniff property to "public" to ignore both private and protected methods
    - The default remains at "private", so all methods are checked
    - Thanks to [Vincent Langlet][@VincentLanglet] for the patch
- PEAR.Commenting.FunctionComment and Squiz.Commenting.FunctionComment sniffs can now ignore return tags in any method
    - Previously, only `__construct()` and `__destruct()` were ignored
    - Set the list of method names to ignore in the "specialMethods" sniff property
    - The default remains at "__construct" and "__destruct" only
    - Thanks to [Vincent Langlet][@VincentLanglet] for the patch
- PSR2.ControlStructures.SwitchDeclaration now supports nested switch statements where every branch terminates
    - Previously, if a CASE only contained a SWITCH and no direct terminating statement, a fall-through error was displayed
    - Now, the error is suppressed if every branch of the SWITCH has a terminating statement
    - Thanks to [Vincent Langlet][@VincentLanglet] for the patch
- The PSR2.Methods.FunctionCallSignature.SpaceBeforeCloseBracket error message is now reported on the closing parenthesis token
    - Previously, the error was being reported on the function keyword, leading to confusing line numbers in the error report
- Squiz.Commenting.FunctionComment is now able to ignore function comments that are only inheritdoc statements
    - Set the skipIfInheritdoc sniff property to "true" to skip checking function comments if the content is only {@inhertidoc}
    - The default remains at "false", so these comments will continue to report errors
    - Thanks to [Jess Myrbo][@xjm] for the patch
- Squiz.Commenting.FunctionComment now supports the PHP 8 mixed type
    - Thanks to [Vadim Borodavko][@javer] for the patch
- Squiz.PHP.NonExecutableCode now has improved handling of syntax errors
    - Thanks to [Thiemo Kreuz][@thiemowmde] for the patch
- Squiz.WhiteSpace.ScopeKeywordSpacing now checks spacing when using PHP 8.0 constructor property promotion
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

### Fixed
- Fixed an issue that could occur when checking files on network drives, such as with WSL2 on Windows 10
    - This works around a long-standing PHP bug with is_readable()
    - Thanks to [Michael S][@codebymikey] for the patch
- Fixed a number of false positives in the Squiz.PHP.DisallowMultipleAssignments sniff
    - Sniff no longer errors for default value assignments in arrow functions
    - Sniff no longer errors for assignments on first line of closure
    - Sniff no longer errors for assignments after a goto label
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- Fixed bug [#2913][sq-2913] : Generic.WhiteSpace.ScopeIndent false positive when opening and closing tag on same line inside conditional
- Fixed bug [#2992][sq-2992] : Enabling caching using a ruleset produces invalid cache files when using --sniffs and --exclude CLI args
- Fixed bug [#3003][sq-3003] : Squiz.Formatting.OperatorBracket autofix incorrect when assignment used with null coalescing operator
- Fixed bug [#3145][sq-3145] : Autoloading of sniff fails when multiple classes declared in same file
- Fixed bug [#3157][sq-3157] : PSR2.ControlStructures.SwitchDeclaration.BreakIndent false positive when case keyword is not indented
- Fixed bug [#3163][sq-3163] : Undefined index error with pre-commit hook using husky on PHP 7.4
    - Thanks to [Ismo Vuorinen][@ivuorinen] for the patch
- Fixed bug [#3165][sq-3165] : Squiz.PHP.DisallowComparisonAssignment false positive when comparison inside closure
- Fixed bug [#3167][sq-3167] : Generic.WhiteSpace.ScopeIndent false positive when using PHP 8.0 constructor property promotion
- Fixed bug [#3170][sq-3170] : Squiz.WhiteSpace.OperatorSpacing false positive when using negation with string concat
    - This also fixes the same issue in the PSR12.Operators.OperatorSpacing sniff
- Fixed bug [#3177][sq-3177] : Incorrect tokenization of GOTO statements in mixed PHP/HTML files
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3184][sq-3184] : PSR2.Namespace.NamespaceDeclaration false positive on namespace operator
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3188][sq-3188] : Squiz.WhiteSpace.ScopeKeywordSpacing false positive for static return type
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3192][sq-3192] : findStartOfStatement doesn't work correctly inside switch
    - Thanks to [Vincent Langlet][@VincentLanglet] for the patch
- Fixed bug [#3195][sq-3195] : Generic.WhiteSpace.ScopeIndent confusing message when combination of tabs and spaces found
- Fixed bug [#3197][sq-3197] : Squiz.NamingConventions.ValidVariableName does not use correct error code for all member vars
- Fixed bug [#3219][sq-3219] : Generic.Formatting.MultipleStatementAlignment false positive for empty anonymous classes and closures
- Fixed bug [#3258][sq-3258] : Squiz.Formatting.OperatorBracket duplicate error messages for unary minus
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3273][sq-3273] : Squiz.Functions.FunctionDeclarationArgumentSpacing reports line break as 0 spaces between parenthesis
- Fixed bug [#3277][sq-3277] : Nullable static return typehint causes whitespace error
- Fixed bug [#3284][sq-3284] : Unused parameter false positive when using array index in arrow function

[sq-2913]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2913
[sq-2992]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2992
[sq-3003]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3003
[sq-3145]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3145
[sq-3157]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3157
[sq-3163]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3163
[sq-3165]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3165
[sq-3167]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3167
[sq-3170]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3170
[sq-3177]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3177
[sq-3184]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3184
[sq-3188]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3188
[sq-3192]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3192
[sq-3195]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3195
[sq-3197]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3197
[sq-3219]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3219
[sq-3258]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3258
[sq-3273]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3273
[sq-3277]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3277
[sq-3284]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3284

## [3.5.8] - 2020-10-23

### Removed
- Reverted a change to the way include/exclude patterns are processed for STDIN content
    - This change is not backwards compatible and will be re-introduced in version 3.6.0

## [3.5.7] - 2020-10-23

### Added
- The PHP 8.0 T_NULLSAFE_OBJECT_OPERATOR token has been made available for older versions
    - Existing sniffs that check for T_OBJECT_OPERATOR have been modified to apply the same rules for the nullsafe object operator
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The new method of PHP 8.0 tokenizing for namespaced names has been reverted to the pre 8.0 method
    - This maintains backwards compatible for existing sniffs on PHP 8.0
    - This change will be removed in PHPCS 4.0 as the PHP 8.0 tokenizing method will be backported for pre 8.0 versions
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added support for changes to the way PHP 8.0 tokenizes hash comments
    - The existing PHP 5-7 behaviour has been replicated for version 8, so no sniff changes are required
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Running the unit tests now includes warnings in the found and fixable error code counts
      - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- PSR12.Functions.NullableTypeDeclaration now supports the PHP8 static return type
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

### Changed
- The autoloader has been changed to fix sniff class name detection issues that may occur when running on PHP 7.4+
    - Thanks to [Eloy Lafuente][@stronk7] for the patch
- PSR12.ControlStructures.BooleanOperatorPlacement.FoundMixed error message is now more accurate when using the allowOnly setting
    - Thanks to [Vincent Langlet][@VincentLanglet] for the patch

### Fixed
- Fixed Squiz.Formatting.OperatorBracket false positive when exiting with a negative number
- Fixed Squiz.PHP.DisallowComparisonAssignment false positive for methods called on an object
- Fixed bug [#2882][sq-2882] : Generic.Arrays.ArrayIndent can request close brace indent to be less than the statement indent level
- Fixed bug [#2883][sq-2883] : Generic.WhiteSpace.ScopeIndent.Incorrect issue after NOWDOC
- Fixed bug [#2975][sq-2975] : Undefined offset in PSR12.Functions.ReturnTypeDeclaration when checking function return type inside ternary
- Fixed bug [#2988][sq-2988] : Undefined offset in Squiz.Strings.ConcatenationSpacing during live coding
    - Thanks to [Thiemo Kreuz][@thiemowmde] for the patch
- Fixed bug [#2989][sq-2989] : Incorrect auto-fixing in Generic.ControlStructures.InlineControlStructure during live coding
    - Thanks to [Thiemo Kreuz][@thiemowmde] for the patch
- Fixed bug [#3007][sq-3007] : Directory exclude pattern improperly excludes directories with names that start the same
    - Thanks to [Steve Talbot][@SteveTalbot] for the patch
- Fixed bug [#3043][sq-3043] : Squiz.WhiteSpace.OperatorSpacing false positive for negation in arrow function
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3049][sq-3049] : Incorrect error with arrow function and parameter passed as reference
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3053][sq-3053] : PSR2 incorrect fix when multiple use statements on same line do not have whitespace between them
- Fixed bug [#3058][sq-3058] : Progress gets unaligned when 100% happens at the end of the available dots
- Fixed bug [#3059][sq-3059] : Squiz.Arrays.ArrayDeclaration false positive when using type casting
    - Thanks to [Sergei Morozov][@morozov] for the patch
- Fixed bug [#3060][sq-3060] : Squiz.Arrays.ArrayDeclaration false positive for static functions
    - Thanks to [Sergei Morozov][@morozov] for the patch
- Fixed bug [#3065][sq-3065] : Should not fix Squiz.Arrays.ArrayDeclaration.SpaceBeforeComma if comment between element and comma
    - Thanks to [Sergei Morozov][@morozov] for the patch
- Fixed bug [#3066][sq-3066] : No support for namespace operator used in type declarations
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3075][sq-3075] : PSR12.ControlStructures.BooleanOperatorPlacement false positive when operator is the only content on line
- Fixed bug [#3099][sq-3099] : Squiz.WhiteSpace.OperatorSpacing false positive when exiting with negative number
    - Thanks to [Sergei Morozov][@morozov] for the patch
- Fixed bug [#3102][sq-3102] : PSR12.Squiz.OperatorSpacing false positive for default values of arrow functions
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#3124][sq-3124] : PSR-12 not reporting error for empty lines with only whitespace
- Fixed bug [#3135][sq-3135] : Ignore annotations are broken on PHP 8.0
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

[sq-2882]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2882
[sq-2883]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2883
[sq-2975]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2975
[sq-2988]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2988
[sq-2989]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2989
[sq-3007]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3007
[sq-3043]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3043
[sq-3049]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3049
[sq-3053]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3053
[sq-3058]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3058
[sq-3059]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3059
[sq-3060]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3060
[sq-3065]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3065
[sq-3066]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3066
[sq-3075]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3075
[sq-3099]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3099
[sq-3102]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3102
[sq-3124]: https://github.com/squizlabs/PHP_CodeSniffer/issues/3124
[sq-3135]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3135

## [3.5.6] - 2020-08-10

### Added
- Added support for PHP 8.0 magic constant dereferencing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added support for changes to the way PHP 8.0 tokenizes comments
    - The existing PHP 5-7 behaviour has been replicated for version 8, so no sniff changes are required
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- `File::getMethodProperties()` now detects the PHP 8.0 static return type
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The PHP 8.0 static return type is now supported for arrow functions
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

### Changed
- The cache is no longer used if the list of loaded PHP extensions changes
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- `Generic.NamingConventions.CamelCapsFunctionName` no longer reports `__serialize` and `__unserialize` as invalid names
    - Thanks to [Filip Š][@filips123] for the patch
- `PEAR.NamingConventions.ValidFunctionName` no longer reports `__serialize` and `__unserialize` as invalid names
    - Thanks to [Filip Š][@filips123] for the patch
- `Squiz.Scope.StaticThisUsage` now detects usage of `$this` inside closures and arrow functions
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch

### Fixed
- Fixed bug [#2877][sq-2877] : PEAR.Functions.FunctionCallSignature false positive for array of functions
    - Thanks to [Vincent Langlet][@VincentLanglet] for the patch
- Fixed bug [#2888][sq-2888] : PSR12.Files.FileHeader blank line error with multiple namespaces in one file
- Fixed bug [#2926][sq-2926] : phpcs hangs when using arrow functions that return heredoc
- Fixed bug [#2943][sq-2943] : Redundant semicolon added to a file when fixing PSR2.Files.ClosingTag.NotAllowed
- Fixed bug [#2967][sq-2967] : Markdown generator does not output headings correctly
    - Thanks to [Petr Bugyík][@o5] for the patch
- Fixed bug [#2977][sq-2977] : File::isReference() does not detect return by reference for closures
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2994][sq-2994] : Generic.Formatting.DisallowMultipleStatements false positive for FOR loop with no body
- Fixed bug [#3033][sq-3033] : Error generated during tokenizing of goto statements on PHP 8
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

[sq-2877]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2877
[sq-2888]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2888
[sq-2926]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2926
[sq-2943]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2943
[sq-2967]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2967
[sq-2977]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2977
[sq-2994]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2994
[sq-3033]: https://github.com/squizlabs/PHP_CodeSniffer/pull/3033

## [3.5.5] - 2020-04-17

### Changed
- The T_FN backfill now works more reliably so T_FN tokens only ever represent real arrow functions
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed an issue where including sniffs using paths containing multiple dots would silently fail
- Generic.CodeAnalysis.EmptyPHPStatement now detects empty statements at the start of control structures

### Fixed
- Error wording in PEAR.Functions.FunctionCallSignature now always uses "parenthesis" instead of sometimes using "bracket"
    - Thanks to [Vincent Langlet][@VincentLanglet] for the patch
- Fixed bug [#2787][sq-2787] : Squiz.PHP.DisallowMultipleAssignments not ignoring typed property declarations
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2810][sq-2810] : PHPCBF fails to fix file with empty statement at start on control structure
- Fixed bug [#2812][sq-2812] : Squiz.Arrays.ArrayDeclaration not detecting some arrays with multiple arguments on the same line
    - Thanks to [Jakub Chábek][@grongor] for the patch
- Fixed bug [#2826][sq-2826] : Generic.WhiteSpace.ArbitraryParenthesesSpacing doesn't detect issues for statements directly after a control structure
    - Thanks to [Vincent Langlet][@VincentLanglet] for the patch
- Fixed bug [#2848][sq-2848] : PSR12.Files.FileHeader false positive for file with mixed PHP and HTML and no file header
- Fixed bug [#2849][sq-2849] : Generic.WhiteSpace.ScopeIndent false positive with arrow function inside array
- Fixed bug [#2850][sq-2850] : Generic.PHP.LowerCaseKeyword complains __HALT_COMPILER is uppercase
- Fixed bug [#2853][sq-2853] : Undefined variable error when using Info report
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2865][sq-2865] : Double arrow tokenized as T_STRING when placed after function named "fn"
- Fixed bug [#2867][sq-2867] : Incorrect scope matching when arrow function used inside IF condition
- Fixed bug [#2868][sq-2868] : phpcs:ignore annotation doesn't work inside a docblock
- Fixed bug [#2878][sq-2878] : PSR12.Files.FileHeader conflicts with Generic.Files.LineEndings
- Fixed bug [#2895][sq-2895] : PSR2.Methods.FunctionCallSignature.MultipleArguments false positive with arrow function argument

[sq-2787]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2787
[sq-2810]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2810
[sq-2812]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2812
[sq-2826]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2826
[sq-2848]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2848
[sq-2849]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2849
[sq-2850]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2850
[sq-2853]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2853
[sq-2865]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2865
[sq-2867]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2867
[sq-2868]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2868
[sq-2878]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2878
[sq-2895]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2895

## [3.5.4] - 2020-01-31

### Changed
- The PHP 7.4 numeric separator backfill now works correctly for more float formats
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The PHP 7.4 numeric separator backfill is no longer run on PHP version 7.4.0 or greater
- File::getCondition() now accepts a 3rd argument that allows for the closest matching token to be returned
    - By default, it continues to return the first matched token found from the top of the file
- Fixed detection of array return types for arrow functions
- Added Generic.PHP.DisallowRequestSuperglobal to ban the use of the $_REQUEST superglobal
    - Thanks to [Jeantwan Teuma][@Morerice] for the contribution
- Generic.ControlStructures.InlineControlStructure no longer shows errors for WHILE and FOR statements without a body
    - Previously it required these to have curly braces, but there were no statements to enclose in them
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- PSR12.ControlStructures.BooleanOperatorPlacement can now be configured to enforce a specific operator position
    - By default, the sniff ensures that operators are all at the beginning or end of lines, but not a mix of both
    - Set the allowOnly property to "first" to enforce all boolean operators to be at the start of a line
    - Set the allowOnly property to "last" to enforce all boolean operators to be at the end of a line
    - Thanks to [Vincent Langlet][@VincentLanglet] for the patch
- PSR12.Files.ImportStatement now auto-fixes import statements by removing the leading slash
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Squiz.ControlStructures.ForLoopDeclaration now has a setting to ignore newline characters
    - Default remains FALSE, so newlines are not allowed within FOR definitions
    - Override the "ignoreNewlines" setting in a ruleset.xml file to change
- Squiz.PHP.InnerFunctions now handles multiple nested anon classes correctly

### Fixed
- Fixed bug [#2497][sq-2497] : Sniff properties not set when referencing a sniff using relative paths or non-native slashes
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2657][sq-2657] : Squiz.WhiteSpace.FunctionSpacing can remove spaces between comment and first/last method during auto-fixing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2688][sq-2688] : Case statements not tokenized correctly when switch is contained within ternary
- Fixed bug [#2698][sq-2698] : PHPCS throws errors determining auto report width when shell_exec is disabled
    - Thanks to [Matthew Peveler][@MasterOdin] for the patch
- Fixed bug [#2730][sq-2730] : PSR12.ControlStructures.ControlStructureSpacing does not ignore comments between conditions
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2732][sq-2732] : PSR12.Files.FileHeader misidentifies file header in mixed content file
- Fixed bug [#2745][sq-2745] : AbstractArraySniff wrong indices when mixed coalesce and ternary values
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Fixed bug [#2748][sq-2748] : Wrong end of statement for fn closures
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Fixed bug [#2751][sq-2751] : Autoload relative paths first to avoid confusion with files from the global include path
    - Thanks to [Klaus Purer][@klausi] for the patch
- Fixed bug [#2763][sq-2763] : PSR12 standard reports errors for multi-line FOR definitions
- Fixed bug [#2768][sq-2768] : Generic.Files.LineLength false positive for non-breakable strings at exactly the soft limit
    - Thanks to [Alex Miles][@ghostal] for the patch
- Fixed bug [#2773][sq-2773] : PSR2.Methods.FunctionCallSignature false positive when arrow function has array return type
- Fixed bug [#2790][sq-2790] : PSR12.Traits.UseDeclaration ignores block comments
    - Thanks to [Vincent Langlet][@VincentLanglet] for the patch
- Fixed bug [#2791][sq-2791] : PSR12.Functions.NullableTypeDeclaration false positive when ternary operator used with instanceof
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2802][sq-2802] : Can't specify a report file path using the tilde shortcut
- Fixed bug [#2804][sq-2804] : PHP4-style typed properties not tokenized correctly
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2805][sq-2805] : Undefined Offset notice during live coding of arrow functions
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2843][sq-2843] : Tokenizer does not support alternative syntax for declare statements
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

[sq-2497]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2497
[sq-2657]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2657
[sq-2688]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2688
[sq-2698]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2698
[sq-2730]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2730
[sq-2732]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2732
[sq-2745]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2745
[sq-2748]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2748
[sq-2751]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2751
[sq-2763]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2763
[sq-2768]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2768
[sq-2773]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2773
[sq-2790]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2790
[sq-2791]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2791
[sq-2802]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2802
[sq-2804]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2804
[sq-2805]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2805
[sq-2843]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2843

## [3.5.3] - 2019-12-04

### Changed
- The PHP 7.4 T_FN token has been made available for older versions
    - T_FN represents the fn string used for arrow functions
    - The double arrow becomes the scope opener, and uses a new T_FN_ARROW token type
    - The token after the statement (normally a semicolon) becomes the scope closer
    - The token is also associated with the opening and closing parenthesis of the statement
    - Any functions named "fn" will have a T_FN token for the function name, but have no scope information
    - Thanks to [Michał Bundyra][@michalbundyra] for the help with this change
- PHP 7.4 numeric separators are now tokenized in the same way when using older PHP versions
    - Previously, a number like 1_000 would tokenize as T_LNUMBER (1), T_STRING (_000)
    - Now, the number tokenizes as T_LNUMBER (1_000)
    - Sniff developers should consider how numbers with underscores impact their custom sniffs
- The PHPCS file cache now takes file permissions into account
    - The cache is now invalidated for a file when its permissions are changed
- File::getMethodParameters() now supports arrow functions
- File::getMethodProperties() now supports arrow functions
- Added Fixer::changeCodeBlockIndent() to change the indent of a code block while auto-fixing
    - Can be used to either increase or decrease the indent
    - Useful when moving the start position of something like a closure, where you want the content to also move
- Added Generic.Files.ExecutableFile sniff
    - Ensures that files are not executable
    - Thanks to [Matthew Peveler][@MasterOdin] for the contribution
- Generic.CodeAnalysis.EmptyPhpStatement now reports unnecessary semicolons after control structure closing braces
    - Thanks to [Vincent Langlet][@VincentLanglet] for the patch
- Generic.PHP.LowerCaseKeyword now enforces that the "fn" keyword is lowercase
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Generic.WhiteSpace.ScopeIndent now supports static arrow functions
- PEAR.Functions.FunctionCallSignature now adjusts the indent of function argument contents during auto-fixing
    - Previously, only the first line of an argument was changed, leading to inconsistent indents
    - This change also applies to PSR2.Methods.FunctionCallSignature
- PSR2.ControlStructures.ControlStructureSpacing now checks whitespace before the closing parenthesis of multi-line control structures
    - Previously, it incorrectly applied the whitespace check for single-line definitions only
- PSR12.Functions.ReturnTypeDeclaration now checks the return type of arrow functions
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- PSR12.Traits.UseDeclaration now ensures all trait import statements are grouped together
    - Previously, the trait import section of the class ended when the first non-import statement was found
    - Checking now continues throughout the class to ensure all statements are grouped together
    - This also ensures that empty lines are not requested after an import statement that isn't the last one
- Squiz.Functions.LowercaseFunctionKeywords now enforces that the "fn" keyword is lowercase
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch

### Fixed
- Fixed bug [#2586][sq-2586] : Generic.WhiteSpace.ScopeIndent false positives when indenting open tags at a non tab-stop
- Fixed bug [#2638][sq-2638] : Squiz.CSS.DuplicateClassDefinitionSniff sees comments as part of the class name
    - Thanks to [Raphael Horber][@rhorber] for the patch
- Fixed bug [#2640][sq-2640] : Squiz.WhiteSpace.OperatorSpacing false positives for some negation operators
    - Thanks to [Jakub Chábek][@grongor] and [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2674][sq-2674] : Squiz.Functions.FunctionDeclarationArgumentSpacing prints wrong argument name in error message
- Fixed bug [#2676][sq-2676] : PSR12.Files.FileHeader locks up when file ends with multiple inline comments
- Fixed bug [#2678][sq-2678] : PSR12.Classes.AnonClassDeclaration incorrectly enforcing that closing brace be on a line by itself
- Fixed bug [#2685][sq-2685] : File::getMethodParameters() setting typeHintEndToken for vars with no type hint
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2694][sq-2694] : AbstractArraySniff produces invalid indices when using ternary operator
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Fixed bug [#2702][sq-2702] : Generic.WhiteSpace.ScopeIndent false positive when using ternary operator with short arrays

[sq-2586]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2586
[sq-2638]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2638
[sq-2640]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2640
[sq-2674]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2674
[sq-2676]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2676
[sq-2678]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2678
[sq-2685]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2685
[sq-2694]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2694
[sq-2702]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2702

## [3.5.2] - 2019-10-28

### Changed
- Generic.ControlStructures.DisallowYodaConditions now returns less false positives
    - False positives were being returned for array comparisons, or when performing some function calls
- Squiz.WhiteSpace.SemicolonSpacing.Incorrect error message now escapes newlines and tabs
    - Provides a clearer error message as whitespace is now visible
    - Also allows for better output for report types such as CSV and XML
- The error message for PSR12.Files.FileHeader.SpacingAfterBlock has been made clearer
    - It now uses the wording from the published PSR-12 standard to indicate that blocks must be separated by a blank line
    - Thanks to [Craig Duncan][@duncan3dc] for the patch

### Fixed
- Fixed bug [#2654][sq-2654] : Incorrect indentation for arguments of multiline function calls
- Fixed bug [#2656][sq-2656] : Squiz.WhiteSpace.MemberVarSpacing removes comments before first member var during auto fixing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2663][sq-2663] : Generic.NamingConventions.ConstructorName complains about old constructor in interfaces
- Fixed bug [#2664][sq-2664] : PSR12.Files.OpenTag incorrectly identifies PHP file with only an opening tag
- Fixed bug [#2665][sq-2665] : PSR12.Files.ImportStatement should not apply to traits
- Fixed bug [#2673][sq-2673] : PSR12.Traits.UseDeclaration does not allow comments or blank lines between use statements

[sq-2654]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2654
[sq-2656]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2656
[sq-2663]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2663
[sq-2664]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2664
[sq-2665]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2665
[sq-2673]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2673

## [3.5.1] - 2019-10-17

### Changed
- Very very verbose diff report output has slightly changed to improve readability
    - Output is printed when running PHPCS with the --report=diff and -vvv command line arguments
    - Fully qualified class names have been replaced with sniff codes
    - Tokens being changed now display the line number they are on
- PSR2, PSR12, and PEAR standards now correctly check for blank lines at the start of function calls
    - This check has been missing from these standards, but has now been implemented
    - When using the PEAR standard, the error code is PEAR.Functions.FunctionCallSignature.FirstArgumentPosition
    - When using PSR2 or PSR12, the error code is PSR2.Methods.FunctionCallSignature.FirstArgumentPosition
- PSR12.ControlStructures.BooleanOperatorPlacement no longer complains when multiple expressions appear on the same line
    - Previously, boolean operators were enforced to appear at the start or end of lines only
    - Boolean operators can now appear in the middle of the line
- PSR12.Files.FileHeader no longer ignores comments preceding a use, namespace, or declare statement
- PSR12.Files.FileHeader now allows a hashbang line at the top of the file

### Fixed
- Fixed bug [#2506][sq-2506] : PSR2 standard can't auto fix multi-line function call inside a string concat statement
- Fixed bug [#2530][sq-2530] : PEAR.Commenting.FunctionComment does not support intersection types in comments
- Fixed bug [#2615][sq-2615] : Constant visibility false positive on non-class constants
- Fixed bug [#2616][sq-2616] : PSR12.Files.FileHeader false positive when file only contains docblock
- Fixed bug [#2619][sq-2619] : PSR12.Files.FileHeader locks up when inline comment is the last content in a file
- Fixed bug [#2621][sq-2621] : PSR12.Classes.AnonClassDeclaration.CloseBraceSameLine false positive for anon class passed as function argument
    - Thanks to [Martins Sipenko][@martinssipenko] for the patch
- Fixed bug [#2623][sq-2623] : PSR12.ControlStructures.ControlStructureSpacing not ignoring indentation inside multi-line string arguments
- Fixed bug [#2624][sq-2624] : PSR12.Traits.UseDeclaration doesnt apply the correct indent during auto fixing
- Fixed bug [#2626][sq-2626] : PSR12.Files.FileHeader detects @var annotations as file docblocks
- Fixed bug [#2628][sq-2628] : PSR12.Traits.UseDeclaration does not allow comments above a USE declaration
- Fixed bug [#2632][sq-2632] : Incorrect indentation of lines starting with "static" inside closures
- Fixed bug [#2641][sq-2641] : PSR12.Functions.NullableTypeDeclaration false positive when using new static()

[sq-2506]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2506
[sq-2530]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2530
[sq-2615]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2615
[sq-2616]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2616
[sq-2619]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2619
[sq-2621]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2621
[sq-2623]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2623
[sq-2624]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2624
[sq-2626]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2626
[sq-2628]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2628
[sq-2632]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2632
[sq-2641]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2641

## [3.5.0] - 2019-09-27

### Changed
- The included PSR12 standard is now complete and ready to use
    - Check your code using PSR-12 by running PHPCS with --standard=PSR12
- Added support for PHP 7.4 typed properties
    - The nullable operator is now tokenized as T_NULLABLE inside property types, as it is elsewhere
    - To get the type of a member var, use the File::getMemberProperties() method, which now contains a "type" array index
        - This contains the type of the member var, or a blank string if not specified
        - If the type is nullable, the return type will contain the leading ?
        - If a type is specified, the position of the first token in the type will be set in a "type_token" array index
        - If a type is specified, the position of the last token in the type will be set in a "type_end_token" array index
        - If the type is nullable, a "nullable_type" array index will also be set to TRUE
        - If the type contains namespace information, it will be cleaned of whitespace and comments in the return value
- The PSR1 standard now correctly bans alternate PHP tags
    - Previously, it only banned short open tags and not the pre-7.0 alternate tags
- Added support for only checking files that have been locally staged in a git repo
    - Use --filter=gitstaged to check these files
    - You still need to give PHPCS a list of files or directories in which to apply the filter
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the contribution
- JSON reports now end with a newline character
- The phpcs.xsd schema now validates phpcs-only and phpcbf-only attributes correctly
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The tokenizer now correctly identifies inline control structures in more cases
- All helper methods inside the File class now throw RuntimeException instead of TokenizerException
    - Some tokenizer methods were also throwing RuntimeException but now correctly throw TokenizerException
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The File::getMethodParameters() method now returns more information, and supports closure USE groups
    - If a type hint is specified, the position of the last token in the hint will be set in a "type_hint_end_token" array index
    - If a default is specified, the position of the first token in the default value will be set in a "default_token" array index
    - If a default is specified, the position of the equals sign will be set in a "default_equal_token" array index
    - If the param is not the last, the position of the comma will be set in a "comma_token" array index
    - If the param is passed by reference, the position of the reference operator will be set in a "reference_token" array index
    - If the param is variable length, the position of the variadic operator will be set in a "variadic_token" array index
- The T_LIST token and it's opening and closing parentheses now contain references to each other in the tokens array
    - Uses the same parenthesis_opener/closer/owner indexes as other tokens
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The T_ANON_CLASS token and it's opening and closing parentheses now contain references to each other in the tokens array
    - Uses the same parenthesis_opener/closer/owner indexes as other tokens
    - Only applicable if the anon class is passing arguments to the constructor
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The PHP 7.4 T_BAD_CHARACTER token has been made available for older versions
    - Allows you to safely look for this token, but it will not appear unless checking with PHP 7.4+
- Metrics are now available for Squiz.WhiteSpace.FunctionSpacing
    - Use the "info" report to see blank lines before/after functions
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Metrics are now available for Squiz.WhiteSpace.MemberVarSpacing
    - Use the "info" report to see blank lines before member vars
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added Generic.ControlStructures.DisallowYodaConditions sniff
    - Ban the use of Yoda conditions
    - Thanks to [Mponos George][@gmponos] for the contribution
- Added Generic.PHP.RequireStrictTypes sniff
    - Enforce the use of a strict types declaration in PHP files
- Added Generic.WhiteSpace.SpreadOperatorSpacingAfter sniff
    - Checks whitespace between the spread operator and the variable/function call it applies to
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the contribution
- Added PSR12.Classes.AnonClassDeclaration sniff
    - Enforces the formatting of anonymous classes
- Added PSR12.Classes.ClosingBrace sniff
    - Enforces that closing braces of classes/interfaces/traits/functions are not followed by a comment or statement
- Added PSR12.ControlStructures.BooleanOperatorPlacement sniff
    - Enforces that boolean operators between conditions are consistently at the start or end of the line
- Added PSR12.ControlStructures.ControlStructureSpacing sniff
    - Enforces that spacing and indents are correct inside control structure parenthesis
- Added PSR12.Files.DeclareStatement sniff
    - Enforces the formatting of declare statements within a file
- Added PSR12.Files.FileHeader sniff
    - Enforces the order and formatting of file header blocks
- Added PSR12.Files.ImportStatement sniff
    - Enforces the formatting of import statements within a file
- Added PSR12.Files.OpenTag sniff
    - Enforces that the open tag is on a line by itself when used at the start of a PHP-only file
- Added PSR12.Functions.ReturnTypeDeclaration sniff
    - Enforces the formatting of return type declarations in functions and closures
- Added PSR12.Properties.ConstantVisibility sniff
    - Enforces that constants must have their visibility defined
    - Uses a warning instead of an error due to this conditionally requiring the project to support PHP 7.1+
- Added PSR12.Traits.UseDeclaration sniff
    - Enforces the formatting of trait import statements within a class
- Generic.Files.LineLength ignoreComments property now ignores comments at the end of a line
    - Previously, this property was incorrectly causing the sniff to ignore any line that ended with a comment
    - Now, the trailing comment is not included in the line length, but the rest of the line is still checked
- Generic.Files.LineLength now only ignores unwrappable comments when the comment is on a line by itself
    - Previously, a short unwrappable comment at the end of the line would have the sniff ignore the entire line
- Generic.Functions.FunctionCallArgumentSpacing no longer checks spacing around assignment operators inside function calls
    - Use the Squiz.WhiteSpace.OperatorSpacing sniff to enforce spacing around assignment operators
        - Note that this sniff checks spacing around all assignment operators, not just inside function calls
    - The Generic.Functions.FunctionCallArgumentSpacing.NoSpaceBeforeEquals error has been removed
        - Use Squiz.WhiteSpace.OperatorSpacing.NoSpaceBefore instead
    - The Generic.Functions.FunctionCallArgumentSpacing.NoSpaceAfterEquals error has been removed
        - Use Squiz.WhiteSpace.OperatorSpacing.NoSpaceAfter instead
    - This also changes the PEAR/PSR2/PSR12 standards so they no longer check assignment operators inside function calls
        - They were previously checking these operators when they should not have
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.WhiteSpace.ScopeIndent no longer performs exact indents checking for chained method calls
    - Other sniffs can be used to enforce chained method call indent rules
    - Thanks to [Pieter Frenssen][@pfrenssen] for the patch
- PEAR.WhiteSpace.ObjectOperatorIndent now supports multi-level chained statements
    - When enabled, chained calls must be indented 1 level more or less than the previous line
    - Set the new "multilevel" setting to TRUE in a ruleset.xml file to enable this behaviour
    - Thanks to [Marcos Passos][@marcospassos] for the patch
- PSR2.ControlStructures.ControlStructureSpacing now allows whitespace after the opening parenthesis if followed by a comment
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- PSR2.Classes.PropertyDeclaration now enforces a single space after a property type keyword
    - The PSR2 standard itself excludes this new check as it is not defined in the written standard
    - Using the PSR12 standard will enforce this check
- Squiz.Commenting.BlockComment no longer requires blank line before comment if it's the first content after the PHP open tag
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Functions.FunctionDeclarationArgumentSpacing now has more accurate error messages
    - This includes renaming the SpaceAfterDefault error code to SpaceAfterEquals, which reflects the real error
- Squiz.Functions.FunctionDeclarationArgumentSpacing now checks for no space after a reference operator
    - If you don't want this new behaviour, exclude the SpacingAfterReference error message in a ruleset.xml file
- Squiz.Functions.FunctionDeclarationArgumentSpacing now checks for no space after a variadic operator
    - If you don't want this new behaviour, exclude the SpacingAfterVariadic error message in a ruleset.xml file
- Squiz.Functions.MultiLineFunctionDeclaration now has improved fixing for the FirstParamSpacing and UseFirstParamSpacing errors
- Squiz.Operators.IncrementDecrementUsage now suggests pre-increment of variables instead of post-increment
    - This change does not enforce pre-increment over post-increment; only the suggestion has changed
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.PHP.DisallowMultipleAssignments now has a second error code for when assignments are found inside control structure conditions
    - The new error code is Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
    - All other multiple assignment cases use the existing error code Squiz.PHP.DisallowMultipleAssignments.Found
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.WhiteSpace.FunctionSpacing now applies beforeFirst and afterLast spacing rules to nested functions
    - Previously, these rules only applied to the first and last function in a class, interface, or trait
    - These rules now apply to functions nested in any statement block, including other functions and conditions
- Squiz.WhiteSpace.OperatorSpacing now has improved handling of parse errors
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.WhiteSpace.OperatorSpacing now checks spacing around the instanceof operator
    - Thanks to [Jakub Chábek][@grongor] for the patch
- Squiz.WhiteSpace.OperatorSpacing can now enforce a single space before assignment operators
    - Previously, the sniff this spacing as multiple assignment operators are sometimes aligned
    - Now, you can set the ignoreSpacingBeforeAssignments sniff property to FALSE to enable checking
    - Default remains TRUE, so spacing before assignments is not checked by default
    - Thanks to [Jakub Chábek][@grongor] for the patch

### Fixed
- Fixed bug [#2391][sq-2391] : Sniff-specific ignore rules inside rulesets are filtering out too many files
    - Thanks to [Juliette Reinders Folmer][@jrfnl] and [Willington Vega][@wvega] for the patch
- Fixed bug [#2478][sq-2478] : FunctionCommentThrowTag.WrongNumber when exception is thrown once but built conditionally
- Fixed bug [#2479][sq-2479] : Generic.WhiteSpace.ScopeIndent error when using array destructing with exact indent checking
- Fixed bug [#2498][sq-2498] : Squiz.Arrays.ArrayDeclaration.MultiLineNotAllowed autofix breaks heredoc
- Fixed bug [#2502][sq-2502] : Generic.WhiteSpace.ScopeIndent false positives with nested switch indentation and case fall-through
- Fixed bug [#2504][sq-2504] : Generic.WhiteSpace.ScopeIndent false positives with nested arrays and nowdoc string
- Fixed bug [#2511][sq-2511] : PSR2 standard not checking if closing paren of single-line function declaration is on new line
- Fixed bug [#2512][sq-2512] : Squiz.PHP.NonExecutableCode does not support alternate SWITCH control structure
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2522][sq-2522] : Text generator throws error when code sample line is too long
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2526][sq-2526] : XML report format has bad syntax on Windows
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2529][sq-2529] : Generic.Formatting.MultipleStatementAlignment wrong error for assign in string concat
- Fixed bug [#2534][sq-2534] : Unresolvable installed_paths can lead to open_basedir errors
    - Thanks to [Oliver Nowak][@ndm2] for the patch
- Fixed bug [#2541][sq-2541] : Text doc generator does not allow for multi-line rule explanations
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2549][sq-2549] : Searching for a phpcs.xml file can throw warnings due to open_basedir restrictions
    - Thanks to [Matthew Peveler][@MasterOdin] for the patch
- Fixed bug [#2558][sq-2558] : PHP 7.4 throwing offset syntax with curly braces is deprecated message
    - Thanks to [Matthew Peveler][@MasterOdin] for the patch
- Fixed bug [#2561][sq-2561] : PHP 7.4 compatibility fix / implode argument order
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2562][sq-2562] : Inline WHILE triggers SpaceBeforeSemicolon incorrectly
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2565][sq-2565] : Generic.ControlStructures.InlineControlStructure confused by mixed short/long tags
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2566][sq-2566] : Author tag email validation doesn't support all TLDs
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2575][sq-2575] : Custom error messages don't have data replaced when cache is enabled
- Fixed bug [#2601][sq-2601] : Squiz.WhiteSpace.FunctionSpacing incorrect fix when spacing is 0
- Fixed bug [#2608][sq-2608] : PSR2 throws errors for use statements when multiple namespaces are defined in a file

[sq-2391]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2391
[sq-2478]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2478
[sq-2479]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2479
[sq-2498]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2498
[sq-2502]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2502
[sq-2504]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2504
[sq-2511]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2511
[sq-2512]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2512
[sq-2522]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2522
[sq-2526]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2526
[sq-2529]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2529
[sq-2534]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2534
[sq-2541]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2541
[sq-2549]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2549
[sq-2558]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2558
[sq-2561]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2561
[sq-2562]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2562
[sq-2565]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2565
[sq-2566]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2566
[sq-2575]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2575
[sq-2601]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2601
[sq-2608]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2608

## [3.4.2] - 2019-04-11

### Changed
- Squiz.Arrays.ArrayDeclaration now has improved handling of syntax errors

### Fixed
- Fixed an issue where the PCRE JIT on PHP 7.3 caused PHPCS to die when using the parallel option
    - PHPCS now disables the PCRE JIT before running
- Fixed bug [#2368][sq-2368] : MySource.PHP.AjaxNullComparison throws error when first function has no doc comment
- Fixed bug [#2414][sq-2414] : Indention false positive in switch/case/if combination
- Fixed bug [#2423][sq-2423] : Squiz.Formatting.OperatorBracket.MissingBrackets error with static
- Fixed bug [#2450][sq-2450] : Indentation false positive when closure containing nested IF conditions used as function argument
- Fixed bug [#2452][sq-2452] : LowercasePHPFunctions sniff failing on "new \File()"
- Fixed bug [#2453][sq-2453] : Squiz.CSS.SemicolonSpacingSniff false positive when style name proceeded by an asterisk
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2464][sq-2464] : Fixer conflict between Generic.WhiteSpace.ScopeIndent and Squiz.WhiteSpace.ScopeClosingBrace when class indented 1 space
- Fixed bug [#2465][sq-2465] : Excluding a sniff by path is not working
- Fixed bug [#2467][sq-2467] : PHP open/close tags inside CSS files are replaced with internal PHPCS token strings when auto fixing

[sq-2368]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2368
[sq-2414]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2414
[sq-2423]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2423
[sq-2450]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2450
[sq-2452]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2452
[sq-2453]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2453
[sq-2464]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2464
[sq-2465]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2465
[sq-2467]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2467

## [3.4.1] - 2019-03-19

### Changed
- The PEAR installable version of PHPCS was missing some files, which have been re-included in this release
    - The code report was not previously available for PEAR installs
    - The Generic.Formatting.SpaceBeforeCast sniff was not previously available for PEAR installs
    - The Generic.WhiteSpace.LanguageConstructSpacing sniff was not previously available for PEAR installs
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- PHPCS will now refuse to run if any of the required PHP extensions are not loaded
    - Previously, PHPCS only relied on requirements being checked by PEAR and Composer
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Ruleset XML parsing errors are now displayed in a readable format so they are easier to correct
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The PSR2 standard no longer throws duplicate errors for spacing around FOR loop parentheses
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- T_PHPCS_SET tokens now contain sniffCode, sniffProperty, and sniffPropertyValue indexes
    - Sniffs can use this information instead of having to parse the token content manually
- Added more guard code for syntax errors to various CSS sniffs
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.Commenting.DocComment error messages now contain the name of the comment tag that caused the error
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.ControlStructures.InlineControlStructure now handles syntax errors correctly
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.Debug.JSHint now longer requires rhino and can be run directly from the npm install
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.Files.LineEndings no longer adds superfluous new line at the end of JS and CSS files
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.Formatting.DisallowMultipleStatements no longer tries to fix lines containing phpcs:ignore statements
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.Functions.FunctionCallArgumentSpacing now has improved performance and anonymous class support
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.WhiteSpace.ScopeIndent now respects changes to the "exact" property using phpcs:set mid-way through a file
    - This allows you to change the "exact" rule for only some parts of a file
- Generic.WhiteSpace.ScopeIndent now disables exact indent checking inside all arrays
    - Previously, this was only done when using long array syntax, but it now works for short array syntax as well
- PEAR.Classes.ClassDeclaration now has improved handling of PHPCS annotations and tab indents
- PSR12.Classes.ClassInstantiation has changed its error code from MissingParenthesis to MissingParentheses
- PSR12.Keywords.ShortFormTypeKeywords now ignores all spacing inside type casts during both checking and fixing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Classes.LowercaseClassKeywords now examines the class keyword for anonymous classes
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.ControlStructures.ControlSignature now has improved handling of parse errors
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Commenting.PostStatementComment fixer no longer adds a blank line at the start of a JS file that begins with a comment
    - Fixes a conflict between this sniff and the Squiz.WhiteSpace.SuperfluousWhitespace sniff
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Commenting.PostStatementComment now ignores comments inside control structure conditions, such as FOR loops
    - Fixes a conflict between this sniff and the Squiz.ControlStructures.ForLoopDeclaration sniff
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Commenting.FunctionCommentThrowTag now has improved support for unknown exception types and namespaces
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.ControlStructures.ForLoopDeclaration has improved whitespace, closure, and empty expression support
    - The SpacingAfterSecondNoThird error code has been removed as part of these fixes
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.CSS.ClassDefinitionOpeningBraceSpace now handles comments and indentation correctly
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.CSS.ClassDefinitionClosingBrace now handles comments, indentation, and multiple statements on the same line correctly
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.CSS.Opacity now handles comments correctly
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.CSS.SemicolonSpacing now handles comments and syntax errors correctly
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.NamingConventions.ValidVariableName now supports variables inside anonymous classes correctly
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.PHP.LowercasePHPFunctions now handles use statements, namespaces, and comments correctly
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.WhiteSpace.FunctionSpacing now fixes function spacing correctly when a function is the first content in a file
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.WhiteSpace.SuperfluousWhitespace no longer throws errors for spacing between functions and properties in anon classes
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Zend.Files.ClosingTag no longer adds a semicolon during fixing of a file that only contains a comment
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Zend.NamingConventions.ValidVariableName now supports variables inside anonymous classes correctly
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

### Fixed
- Fixed bug [#2298][sq-2298] : PSR2.Classes.ClassDeclaration allows extended class on new line
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Fixed bug [#2337][sq-2337] : Generic.WhiteSpace.ScopeIndent incorrect error when multi-line function call starts on same line as open tag
- Fixed bug [#2348][sq-2348] : Cache not invalidated when changing a ruleset included by another
- Fixed bug [#2376][sq-2376] : Using __halt_compiler() breaks Generic.PHP.ForbiddenFunctions unless it's last in the function list
    - Thanks to [Sijun Zhu][@Billz95] for the patch
- Fixed bug [#2393][sq-2393] : The gitmodified filter will infinitely loop when encountering deleted file paths
    - Thanks to [Lucas Manzke][@lmanzke] for the patch
- Fixed bug [#2396][sq-2396] : Generic.WhiteSpace.ScopeIndent incorrect error when multi-line IF condition mixed with HTML
- Fixed bug [#2431][sq-2431] : Use function/const not tokenized as T_STRING when preceded by comment

[sq-2298]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2298
[sq-2337]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2337
[sq-2348]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2348
[sq-2376]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2376
[sq-2393]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2393
[sq-2396]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2396
[sq-2431]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2431

## [3.4.0] - 2018-12-20

### Deprecated
- The Generic.Formatting.NoSpaceAfterCast sniff has been deprecated and will be removed in version 4
    - The functionality of this sniff is now available in the Generic.Formatting.SpaceAfterCast sniff
        - Include the Generic.Formatting.SpaceAfterCast sniff and set the "spacing" property to "0"
    - As soon as possible, replace all instances of the old sniff code with the new sniff code and property setting
        - The existing sniff will continue to work until version 4 has been released

### Changed
- Rule include patterns in a ruleset.xml file are now evaluated as OR instead of AND
    - Previously, a file had to match every include pattern and no exclude patterns to be included
    - Now, a file must match at least one include pattern and no exclude patterns to be included
    - This is a bug fix as include patterns are already documented to work this way
- New token T_BITWISE_NOT added for the bitwise not operator
    - This token was previously tokenized as T_NONE
    - Any sniffs specifically looking for T_NONE tokens with a tilde as the contents must now also look for T_BITWISE_NOT
    - Sniffs can continue looking for T_NONE as well as T_BITWISE_NOT to support older PHP_CodeSniffer versions
- All types of binary casting are now tokenized as T_BINARY_CAST
    - Previously, the 'b' in 'b"some string with $var"' would be a T_BINARY_CAST, but only when the string contained a var
    - This change ensures the 'b' is always tokenized as T_BINARY_CAST
    - This change also converts '(binary)' from T_STRING_CAST to T_BINARY_CAST
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the help with this patch
- Array properties set inside a ruleset.xml file can now extend a previous value instead of always overwriting it
    - e.g., if you include a ruleset that defines forbidden functions, can you now add to that list instead of having to redefine it
    - To use this feature, add extend="true" to the property tag
        - e.g., property name="forbiddenFunctionNames" type="array" extend="true"
    - Thanks to [Michael Moravec][@Majkl578] for the patch
- If $XDG_CACHE_HOME is set and points to a valid directory, it will be used for caching instead of the system temp directory
- PHPCBF now disables parallel running if you are passing content on STDIN
    - Stops an error from being shown after the fixed output is printed
- The progress report now shows files with tokenizer errors as skipped (S) instead of a warning (W)
    - The tokenizer error is still displayed in reports as normal
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The Squiz standard now ensures there is no space between an increment/decrement operator and its variable
- The File::getMethodProperties() method now includes a has_body array index in the return value
    - FALSE if the method has no body (as with abstract and interface methods) or TRUE otherwise
    - Thanks to [Chris Wilkinson][@thewilkybarkid] for the patch
- The File::getTokensAsString() method now throws an exception if the $start param is invalid
    - If the $length param is invalid, an empty string will be returned
    - Stops an infinite loop when the function is passed invalid data
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added new Generic.CodeAnalysis.EmptyPHPStatement sniff
    - Warns when it finds empty PHP open/close tag combinations or superfluous semicolons
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the contribution
- Added new Generic.Formatting.SpaceBeforeCast sniff
    - Ensures there is exactly 1 space before a type cast, unless the cast statement is indented or multi-line
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the contribution
- Added new Generic.VersionControl.GitMergeConflict sniff
    - Detects merge conflict artifacts left in files
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the contribution
- Added Generic.WhiteSpace.IncrementDecrementSpacing sniff
    - Ensures there is no space between the operator and the variable it applies to
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the contribution
- Added PSR12.Functions.NullableTypeDeclaration sniff
    - Ensures there is no space after the question mark in a nullable type declaration
    - Thanks to [Timo Schinkel][@timoschinkel] for the contribution
- A number of sniffs have improved support for methods in anonymous classes
    - These sniffs would often throw the same error twice for functions in nested classes
    - Error messages have also been changed to be less confusing
    - The full list of affected sniffs is:
        - Generic.NamingConventions.CamelCapsFunctionName
        - PEAR.NamingConventions.ValidFunctionName
        - PSR1.Methods.CamelCapsMethodName
        - PSR2.Methods.MethodDeclaration
        - Squiz.Scope.MethodScope
        - Squiz.Scope.StaticThisUsage
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.CodeAnalysis.UnusedFunctionParameter now only skips functions with empty bodies when the class implements an interface
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.CodeAnalysis.UnusedFunctionParameter now has additional error codes to indicate where unused params were found
    - The new error code prefixes are:
        - FoundInExtendedClass: when the class extends another
        - FoundInImplementedInterface: when the class implements an interface
        - Found: used in all other cases, including closures
    - The new error code suffixes are:
        - BeforeLastUsed: the unused param was positioned before the last used param in the function signature
        - AfterLastUsed: the unused param was positioned after the last used param in the function signature
    - This makes the new error code list for this sniff:
        - Found
        - FoundBeforeLastUsed
        - FoundAfterLastUsed
        - FoundInExtendedClass
        - FoundInExtendedClassBeforeLastUsed
        - FoundInExtendedClassAfterLastUsed
        - FoundInImplementedInterface
        - FoundInImplementedInterfaceBeforeLastUsed
        - FoundInImplementedInterfaceAfterLastUsed
    - These errors code make it easier for specific cases to be ignored or promoted using a ruleset.xml file
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the contribution
- Generic.Classes.DuplicateClassName now inspects traits for duplicate names as well as classes and interfaces
    - Thanks to [Chris Wilkinson][@thewilkybarkid] for the patch
- Generic.Files.InlineHTML now ignores a BOM at the start of the file
    - Thanks to [Chris Wilkinson][@thewilkybarkid] for the patch
- Generic.PHP.CharacterBeforePHPOpeningTag now ignores a BOM at the start of the file
    - Thanks to [Chris Wilkinson][@thewilkybarkid] for the patch
- Generic.Formatting.SpaceAfterCast now has a setting to specify how many spaces are required after a type cast
    - Default remains 1
    - Override the "spacing" setting in a ruleset.xml file to change
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.Formatting.SpaceAfterCast now has a setting to ignore newline characters after a type cast
    - Default remains FALSE, so newlines are not allowed
    - Override the "ignoreNewlines" setting in a ruleset.xml file to change
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.Formatting.SpaceAfterNot now has a setting to specify how many spaces are required after a NOT operator
    - Default remains 1
    - Override the "spacing" setting in a ruleset.xml file to change
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.Formatting.SpaceAfterNot now has a setting to ignore newline characters after the NOT operator
    - Default remains FALSE, so newlines are not allowed
    - Override the "ignoreNewlines" setting in a ruleset.xml file to change
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- PEAR.Functions.FunctionDeclaration now checks spacing before the opening parenthesis of functions with no body
    - Thanks to [Chris Wilkinson][@thewilkybarkid] for the patch
- PEAR.Functions.FunctionDeclaration now enforces no space before the semicolon in functions with no body
    - Thanks to [Chris Wilkinson][@thewilkybarkid] for the patch
- PSR2.Classes.PropertyDeclaration now checks the order of property modifier keywords
    - This is a rule that is documented in PSR-2 but was not enforced by the included PSR2 standard until now
    - This sniff is also able to fix the order of the modifier keywords if they are incorrect
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- PSR2.Methods.MethodDeclaration now checks method declarations inside traits
    - Thanks to [Chris Wilkinson][@thewilkybarkid] for the patch
- Squiz.Commenting.InlineComment now has better detection of comment block boundaries
- Squiz.Classes.ClassFileName now checks that a trait name matches the filename
    - Thanks to [Chris Wilkinson][@thewilkybarkid] for the patch
- Squiz.Classes.SelfMemberReference now supports scoped declarations and anonymous classes
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Classes.SelfMemberReference now fixes multiple errors at once, increasing fixer performance
    - Thanks to [Gabriel Ostrolucký][@ostrolucky] for the patch
- Squiz.Functions.LowercaseFunctionKeywords now checks abstract and final prefixes, and auto-fixes errors
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Objects.ObjectMemberComma.Missing has been renamed to Squiz.Objects.ObjectMemberComma.Found
    - The error is thrown when the comma is found but not required, so the error code was incorrect
    - If you are referencing the old error code in a ruleset XML file, please use the new code instead
    - If you wish to maintain backwards compatibility, you can provide rules for both the old and new codes
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.WhiteSpace.ObjectOperatorSpacing is now more tolerant of parse errors
- Squiz.WhiteSpace.ObjectOperatorSpacing now fixes errors more efficiently
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

### Fixed
- Fixed bug [#2109][sq-2109] : Generic.Functions.CallTimePassByReference false positive for bitwise and used in function argument
- Fixed bug [#2165][sq-2165] : Conflict between Squiz.Arrays.ArrayDeclaration and ScopeIndent sniffs when heredoc used in array
- Fixed bug [#2167][sq-2167] : Generic.WhiteSpace.ScopeIndent shows invalid error when scope opener indented inside inline HTML
- Fixed bug [#2178][sq-2178] : Generic.NamingConventions.ConstructorName matches methods in anon classes with same name as containing class
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2190][sq-2190] : PEAR.Functions.FunctionCallSignature incorrect error when encountering trailing PHPCS annotation
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2194][sq-2194] : Generic.Whitespace.LanguageConstructSpacing should not be checking namespace operators
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2202][sq-2202] : Squiz.WhiteSpace.OperatorSpacing throws error for negative index when using curly braces for string access
    - Same issue fixed in Squiz.Formatting.OperatorBracket
    - Thanks to [Andreas Buchenrieder][@anbuc] for the patch
- Fixed bug [#2210][sq-2210] : Generic.NamingConventions.CamelCapsFunctionName not ignoring SoapClient __getCookies() method
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2211][sq-2211] : PSR2.Methods.MethodDeclaration gets confused over comments between modifier keywords
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2212][sq-2212] : FUNCTION and CONST in use groups being tokenized as T_FUNCTION and T_CONST
    - Thanks to [Chris Wilkinson][@thewilkybarkid] for the patch
- Fixed bug [#2214][sq-2214] : File::getMemberProperties() is recognizing method params as properties
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2236][sq-2236] : Memory info measurement unit is Mb but probably should be MB
- Fixed bug [#2246][sq-2246] : CSS tokenizer does not tokenize class names correctly when they contain the string NEW
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2278][sq-2278] : Squiz.Operators.ComparisonOperatorUsage false positive when inline IF contained in parentheses
    - Thanks to [Arnout Boks][@aboks] for the patch
- Fixed bug [#2284][sq-2284] : Squiz.Functions.FunctionDeclarationArgumentSpacing removing type hint during fixing
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Fixed bug [#2297][sq-2297] : Anonymous class not tokenized correctly when used as argument to another anon class
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch

[sq-2109]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2109
[sq-2165]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2165
[sq-2167]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2167
[sq-2178]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2178
[sq-2190]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2190
[sq-2194]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2194
[sq-2202]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2202
[sq-2210]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2210
[sq-2211]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2211
[sq-2212]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2212
[sq-2214]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2214
[sq-2236]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2236
[sq-2246]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2246
[sq-2278]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2278
[sq-2284]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2284
[sq-2297]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2297

## [2.9.2] - 2018-11-08

### Changed
- PHPCS should now run under PHP 7.3 without deprecation warnings
    - Thanks to [Nick Wilde][@NickDickinsonWilde] for the patch

### Fixed
- Fixed bug [#1496][sq-1496] : Squiz.Strings.DoubleQuoteUsage not unescaping dollar sign when fixing
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Fixed bug [#1549][sq-1549] : Squiz.PHP.EmbeddedPhp fixer conflict with // comment before PHP close tag
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1890][sq-1890] : Incorrect Squiz.WhiteSpace.ControlStructureSpacing.NoLineAfterClose error between catch and finally statements

## [3.3.2] - 2018-09-24

### Changed
- Fixed a problem where the report cache was not being cleared when the sniffs inside a standard were updated
- The info report (--report=info) now has improved formatting for metrics that span multiple lines
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The unit test runner now skips .bak files when looking for test cases
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The Squiz standard now ensures underscores are not used to indicate visibility of private members vars and methods
    - Previously, this standard enforced the use of underscores
- Generic.PHP.NoSilencedErrors error messages now contain a code snippet to show the context of the error
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Arrays.ArrayDeclaration no longer reports errors for a comma on a line new after a here/nowdoc
    - Also stops a parse error being generated when auto-fixing
    - The SpaceBeforeComma error message has been changed to only have one data value instead of two
- Squiz.Commenting.FunctionComment no longer errors when trying to fix indents of multi-line param comments
- Squiz.Formatting.OperatorBracket now correctly fixes statements that contain strings
- Squiz.PHP.CommentedOutCode now ignores more @-style annotations and includes better comment block detection
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

### Fixed
- Fixed a problem where referencing a relative file path in a ruleset XML file could add unnecessary sniff exclusions
    - This didn't actually exclude anything, but caused verbose output to list strange exclusion rules
- Fixed bug [#2110][sq-2110] : Squiz.WhiteSpace.FunctionSpacing is removing indents from the start of functions when fixing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2115][sq-2115] : Squiz.Commenting.VariableComment not checking var types when the @var line contains a comment
- Fixed bug [#2120][sq-2120] : Tokenizer fails to match T_INLINE_ELSE when used after function call containing closure
- Fixed bug [#2121][sq-2121] : Squiz.PHP.DisallowMultipleAssignments false positive in while loop conditions
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2127][sq-2127] : File::findExtendedClassName() doesn't support nested classes
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2138][sq-2138] : Tokenizer detects wrong token for PHP ::class feature with spaces
- Fixed bug [#2143][sq-2143] : PSR2.Namespaces.UseDeclaration does not properly fix "use function" and "use const" statements
    - Thanks to [Chris Wilkinson][@thewilkybarkid] for the patch
- Fixed bug [#2144][sq-2144] : Squiz.Arrays.ArrayDeclaration does incorrect align calculation in array with cyrillic keys
- Fixed bug [#2146][sq-2146] : Zend.Files.ClosingTag removes closing tag from end of file without inserting a semicolon
- Fixed bug [#2151][sq-2151] : XML schema not updated with the new array property syntax

[sq-2110]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2110
[sq-2115]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2115
[sq-2120]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2120
[sq-2121]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2121
[sq-2127]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2127
[sq-2138]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2138
[sq-2143]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2143
[sq-2144]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2144
[sq-2146]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2146
[sq-2151]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2151

## [3.3.1] - 2018-07-27

### Removed
- Support for HHVM has been dropped due to recent unfixed bugs and HHVM refocus on Hack only
    - Thanks to [Walt Sorensen][@photodude] and [Juliette Reinders Folmer][@jrfnl] for helping to remove all HHVM exceptions from the core

### Changed
- The full report (the default report) now has improved word wrapping for multi-line messages and sniff codes
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The summary report now sorts files based on their directory location instead of just a basic string sort
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The source report now orders error codes by name when they have the same number of errors
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The junit report no longer generates validation errors with the Jenkins xUnit plugin
    - Thanks to [Nikolay Geo][@nicholascus] for the patch
- Generic.Commenting.DocComment no longer generates the SpacingBeforeTags error if tags are the first content in the docblock
    - The sniff will still generate a MissingShort error if there is no short comment
    - This allows the MissingShort error to be suppressed in a ruleset to make short descriptions optional
- Generic.Functions.FunctionCallArgumentSpacing now properly fixes multi-line function calls with leading commas
    - Previously, newlines between function arguments would be removed
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.PHP.Syntax will now use PHP_BINARY instead of trying to discover the executable path
    - This ensures that the sniff will always syntax check files using the PHP version that PHPCS is running under
    - Setting the `php_path` config var will still override this value as normal
    - Thanks to [Willem Stuursma-Ruwen][@willemstuursma] for the patch
- PSR2.Namespaces.UseDeclaration now supports commas at the end of group use declarations
    - Also improves checking and fixing for use statements containing parse errors
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Arrays.ArrayDeclaration no longer removes the array opening brace while fixing
    - This could occur when the opening brace was on a new line and the first array key directly followed
    - This change also stops the KeyNotAligned error message being incorrectly reported in these cases
- Squiz.Arrays.ArrayDeclaration no longer tries to change multi-line arrays to single line when they contain comments
    - Fixes a conflict between this sniff and some indentation sniffs
- Squiz.Classes.ClassDeclaration no longer enforces spacing rules when a class is followed by a function
    - Fixes a conflict between this sniff and the Squiz.WhiteSpace.FunctionSpacing sniff
- The Squiz.Classes.ValidClassName.NotCamelCaps message now references PascalCase instead of CamelCase
    - The "CamelCase class name" metric produced by the sniff has been changed to "PascalCase class name"
    - This reflects the fact that the class name check is actually a Pascal Case check and not really Camel Case
    - Thanks to [Tom H Anderson][@TomHAnderson] for the patch
- Squiz.Commenting.InlineComment no longer enforces spacing rules when an inline comment is followed by a docblock
    - Fixes a conflict between this sniff and the Squiz.WhiteSpace.FunctionSpacing sniff
- Squiz.WhiteSpace.OperatorSpacing no longer tries to fix operator spacing if the next content is a comment on a new line
    - Fixes a conflict between this sniff and the Squiz.Commenting.PostStatementComment sniff
    - Also stops PHPCS annotations from being moved to a different line, potentially changing their meaning
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.WhiteSpace.FunctionSpacing no longer checks spacing of functions at the top of an embedded PHP block
    - Fixes a conflict between this sniff and the Squiz.PHP.EmbeddedPHP sniff
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.WhiteSpace.MemberVarSpacing no longer checks spacing before member vars that come directly after methods
    - Fixes a conflict between this sniff and the Squiz.WhiteSpace.FunctionSpacing sniff
- Squiz.WhiteSpace.SuperfluousWhitespace now recognizes unicode whitespace at the start and end of a file
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

### Fixed
- Fixed bug [#2029][sq-2029] : Squiz.Scope.MemberVarScope throws fatal error when a property is found in an interface
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2047][sq-2047] : PSR12.Classes.ClassInstantiation false positive when instantiating class from array index
- Fixed bug [#2048][sq-2048] : GenericFormatting.MultipleStatementAlignment false positive when assigning values inside an array
- Fixed bug [#2053][sq-2053] : PSR12.Classes.ClassInstantiation incorrectly fix when using member vars and some variable formats
- Fixed bug [#2065][sq-2065] : Generic.ControlStructures.InlineControlStructure fixing fails when inline control structure contains closure
- Fixed bug [#2072][sq-2072] : Squiz.Arrays.ArrayDeclaration throws NoComma error when array value is a shorthand IF statement
- Fixed bug [#2082][sq-2082] : File with "defined() or define()" syntax triggers PSR1.Files.SideEffects.FoundWithSymbols
- Fixed bug [#2095][sq-2095] : PSR2.Namespaces.NamespaceDeclaration does not handle namespaces defined over multiple lines

[sq-2029]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2029
[sq-2047]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2047
[sq-2048]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2048
[sq-2053]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2053
[sq-2065]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2065
[sq-2072]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2072
[sq-2082]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2082
[sq-2095]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2095

## [3.3.0] - 2018-06-07

### Deprecated
- The Squiz.WhiteSpace.LanguageConstructSpacing sniff has been deprecated and will be removed in version 4
    - The sniff has been moved to the Generic standard, with a new code of Generic.WhiteSpace.LanguageConstructSpacing
    - As soon as possible, replace all instances of the old sniff code with the new sniff code in your ruleset.xml files
        - The existing Squiz sniff will continue to work until version 4 has been released
    - The new Generic sniff now also checks many more language constructs to enforce additional spacing rules
        - Thanks to [Mponos George][@gmponos] for the contribution
- The current method for setting array properties in ruleset files has been deprecated and will be removed in version 4
    - Currently, setting an array value uses the string syntax "print=>echo,create_function=>null"
    - Now, individual array elements are specified using a new "element" tag with "key" and "value" attributes
        - For example, element key="print" value="echo"
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- The T_ARRAY_HINT token has been deprecated and will be removed in version 4
    - The token was used to ensure array type hints were not tokenized as T_ARRAY, but no other type hints were given a special token
    - Array type hints now use the standard T_STRING token instead
    - Sniffs referencing this token type will continue to run without error until version 4, but will not find any T_ARRAY_HINT tokens
- The T_RETURN_TYPE token has been deprecated and will be removed in version 4
    - The token was used to ensure array/self/parent/callable return types were tokenized consistently
    - For namespaced return types, only the last part of the string (the class name) was tokenized as T_RETURN_TYPE
    - This was not consistent and so return types are now left using their original token types so they are not skipped by sniffs
        - The exception are array return types, which are tokenized as T_STRING instead of T_ARRAY, as they are for type hints
    - Sniffs referencing this token type will continue to run without error until version 4, but will not find any T_RETUTN_TYPE tokens
    - To get the return type of a function, use the File::getMethodProperties() method, which now contains a "return_type" array index
        - This contains the return type of the function or closer, or a blank string if not specified
        - If the return type is nullable, the return type will contain the leading ?
            - A nullable_return_type array index in the return value will also be set to true
        - If the return type contains namespace information, it will be cleaned of whitespace and comments
            - To access the original return value string, use the main tokens array

### Added
- This release contains an incomplete version of the PSR-12 coding standard
    - Errors found using this standard should be valid, but it will miss a lot of violations until it is complete
    - If you'd like to test and help, you can use the standard by running PHPCS with --standard=PSR12

### Changed
- Config values set using --runtime-set now override any config values set in rulesets or the CodeSniffer.conf file
- You can now apply include-pattern rules to individual message codes in a ruleset like you can with exclude-pattern rules
    - Previously, include-pattern rules only applied to entire sniffs
    - If a message code has both include and exclude patterns, the exclude patterns will be ignored
- Using PHPCS annotations to selectively re-enable sniffs is now more flexible
    - Previously, you could only re-enable a sniff/category/standard using the exact same code that was disabled
    - Now, you can disable a standard and only re-enable a specific category or sniff
    - Or, you can disable a specific sniff and have it re-enable when you re-enable the category or standard
- The value of array sniff properties can now be set using phpcs:set annotations
    - e.g., phpcs:set Standard.Category.SniffName property[] key=>value,key2=>value2
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- PHPCS annotations now remain as T_PHPCS_* tokens instead of reverting to comment tokens when --ignore-annotations is used
    - This stops sniffs (especially commenting sniffs) from generating a large number of false errors when ignoring
    - Any custom sniffs that are using the T_PHPCS_* tokens to detect annotations may need to be changed to ignore them
        - Check $phpcsFile->config->annotations to see if annotations are enabled and ignore when false
- You can now use fully or partially qualified class names for custom reports instead of absolute file paths
    - To support this, you must specify an autoload file in your ruleset.xml file and use it to register an autoloader
    - Your autoloader will need to load your custom report class when requested
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The JSON report format now does escaping in error source codes as well as error messages
    - Thanks to [Martin Vasel][@marvasDE] for the patch
- Invalid installed_paths values are now ignored instead of causing a fatal error
- Improved testability of custom rulesets by allowing the installed standards to be overridden
    - Thanks to [Timo Schinkel][@timoschinkel] for the patch
- The key used for caching PHPCS runs now includes all set config values
    - This fixes a problem where changing config values (e.g., via --runtime-set) used an incorrect cache file
- The "Function opening brace placement" metric has been separated into function and closure metrics in the info report
    - Closures are no longer included in the "Function opening brace placement" metric
    - A new "Closure opening brace placement" metric now shows information for closures
- Multi-line T_YIELD_FROM statements are now replicated properly for older PHP versions
- The PSR2 standard no longer produces 2 error messages when the AS keyword in a foreach loop is not lowercase
- Specifying a path to a non-existent dir when using the `--report-[reportType]=/path/to/report` CLI option no longer throws an exception
    - This now prints a readable error message, as it does when using `--report-file`
- The File::getMethodParamaters() method now includes a type_hint_token array index in the return value
    - Provides the position in the token stack of the first token in the type hint
- The File::getMethodProperties() method now includes a return_type_token array index in the return value
    - Provides the position in the token stack of the first token in the return type
- The File::getTokensAsString() method can now optionally return original (non tab-replaced) content
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Removed Squiz.PHP.DisallowObEndFlush from the Squiz standard
    - If you use this sniff and want to continue banning ob_end_flush(), use Generic.PHP.ForbiddenFunctions instead
    - You will need to set the forbiddenFunctions property in your ruleset.xml file
- Removed Squiz.PHP.ForbiddenFunctions from the Squiz standard
    - Replaced by using the forbiddenFunctions property of Generic.PHP.ForbiddenFunctions in the Squiz ruleset.xml
    - Functionality of the Squiz standard remains the same, but the error codes are now different
    - Previously, Squiz.PHP.ForbiddenFunctions.Found and Squiz.PHP.ForbiddenFunctions.FoundWithAlternative
    - Now, Generic.PHP.ForbiddenFunctions.Found and Generic.PHP.ForbiddenFunctions.FoundWithAlternative
- Added new Generic.PHP.LowerCaseType sniff
    - Ensures PHP types used for type hints, return types, and type casting are lowercase
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the contribution
- Added new Generic.WhiteSpace.ArbitraryParenthesesSpacing sniff
    - Generates an error for whitespace inside parenthesis that don't belong to a function call/declaration or control structure
    - Generates a warning for any empty parenthesis found
    - Allows the required spacing to be set using the spacing sniff property (default is 0)
    - Allows newlines to be used by setting the ignoreNewlines sniff property (default is false)
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the contribution
- Added new PSR12.Classes.ClassInstantiation sniff
    - Ensures parenthesis are used when instantiating a new class
- Added new PSR12.Keywords.ShortFormTypeKeywords sniff
    - Ensures the short form of PHP types is used when type casting
- Added new PSR12.Namespaces.CompundNamespaceDepth sniff
    - Ensures compound namespace use statements have a max depth of 2 levels
    - The max depth can be changed by setting the 'maxDepth' sniff property in a ruleset.xml file
- Added new PSR12.Operators.OperatorSpacing sniff
    - Ensures operators are preceded and followed by at least 1 space
- Improved core support for grouped property declarations
    - Also improves support in Squiz.WhiteSpace.ScopeKeywordSpacing and Squiz.WhiteSpace.MemberVarSpacing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.Commenting.DocComment now produces a NonParamGroup error when tags are mixed in with the @param tag group
    - It would previously throw either a NonParamGroup or ParamGroup error depending on the order of tags
    - This change allows the NonParamGroup error to be suppressed in a ruleset to allow the @param group to contain other tags
    - Thanks to [Phil Davis][@phil-davis] for the patch
- Generic.Commenting.DocComment now continues checks param tags even if the doc comment short description is missing
    - This change allows the MissingShort error to be suppressed in a ruleset without all other errors being suppressed as well
    - Thanks to [Phil Davis][@phil-davis] for the patch
- Generic.CodeAnalysis.AssignmentInCondition now reports a different error code for assignments found in WHILE conditions
    - The return value of a function call is often assigned in a WHILE condition, so this change makes it easier to exclude these cases
    - The new code for this error message is Generic.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
    - The error code for all other cases remains as Generic.CodeAnalysis.AssignmentInCondition.Found
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.Functions.OpeningFunctionBraceBsdAllman now longer leaves trailing whitespace when moving the opening brace during fixing
    - Also applies to fixes made by PEAR.Functions.FunctionDeclaration and Squiz.Functions.MultiLineFunctionDeclaration
- Generic.WhiteSpace.ScopeIndent now does a better job of fixing the indent of multi-line comments
- Generic.WhiteSpace.ScopeIndent now does a better job of fixing the indent of PHP open and close tags
- PEAR.Commenting.FunctionComment now report a different error code for param comment lines with too much padding
    - Previously, any lines of a param comment that don't start at the exact comment position got the same error code
    - Now, only comment lines with too little padding use ParamCommentAlignment as they are clearly mistakes
    - Comment lines with too much padding may be using precision alignment as now use ParamCommentAlignmentExceeded
    - This allows for excessive padding to be excluded from a ruleset while continuing to enforce a minimum padding
- PEAR.WhiteSpace.ObjectOperatorIndent now checks the indent of more chained operators
    - Previously, it only checked chains beginning with a variable
    - Now, it checks chains beginning with function calls, static class names, etc
- Squiz.Arrays.ArrayDeclaration now continues checking array formatting even if the key indent is not correct
    - Allows for using different array indent rules while still checking/fixing double arrow and value alignment
- Squiz.Commenting.BlockComment has improved support for tab-indented comments
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Commenting.BlockComment auto fixing no longer breaks when two block comments follow each other
    - Also stopped single-line block comments from being auto fixed when they are embedded in other code
    - Also fixed as issue found when PHPCS annotations were used inside a block comment
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Commenting.BlockComment.LastLineIndent is now able to be fixed with phpcbf
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Commenting.BlockComment now aligns star-prefixed lines under the opening tag while fixing, instead of indenting them
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Commenting.FunctionComment.IncorrectTypeHint message no longer contains cut-off suggested type hints
- Squiz.Commenting.InlineComment now uses a new error code for inline comments at the end of a function
    - Previously, all inline comments followed by a blank line threw a Squiz.Commenting.InlineComment.SpacingAfter error
    - Now, inline comments at the end of a function will instead throw Squiz.Commenting.InlineComment.SpacingAfterAtFunctionEnd
    - If you previously excluded SpacingAfter, add an exclusion for SpacingAfterAtFunctionEnd to your ruleset as well
    - If you previously only included SpacingAfter, consider including SpacingAfterAtFunctionEnd as well
    - The Squiz standard now excludes SpacingAfterAtFunctionEnd as the blank line is checked elsewhere
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.ControlStructures.ControlSignature now errors when a comment follows the closing brace of an earlier body
    - Applies to catch, finally, else, elseif, and do/while structures
    - The included PSR2 standard now enforces this rule
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Formatting.OperatorBracket.MissingBrackets message has been changed to remove the word "arithmetic"
    - The sniff checks more than just arithmetic operators, so the message is now clearer
- Sniffs.Operators.ComparisonOperatorUsage now detects more cases of implicit true comparisons
    - It could previously be confused by comparisons used as function arguments
- Squiz.PHP.CommentedOutCode now ignores simple @-style annotation comments so they are not flagged as commented out code
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.PHP.CommentedOutCode now ignores a greater number of short comments so they are not flagged as commented out code
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.PHP.DisallowComparisonAssignment no longer errors when using the null coalescing operator
    - Given this operator is used almost exclusively to assign values, it didn't make sense to generate an error
- Squiz.WhiteSpacing.FunctionSpacing now has a property to specify how many blank lines should be before the first class method
    - Only applies when a method is the first code block in a class (i.e., there are no member vars before it)
    - Override the 'spacingBeforeFirst' property in a ruleset.xml file to change
    - If not set, the sniff will use whatever value is set for the existing 'spacing' property
- Squiz.WhiteSpacing.FunctionSpacing now has a property to specify how many blank lines should be after the last class method
    - Only applies when a method is the last code block in a class (i.e., there are no member vars after it)
    - Override the 'spacingAfterLast' property in a ruleset.xml file to change
    - If not set, the sniff will use whatever value is set for the existing 'spacing' property

### Fixed
- Fixed bug [#1863][sq-1863] : File::findEndOfStatement() not working when passed a scope opener
- Fixed bug [#1876][sq-1876] : PSR2.Namespaces.UseDeclaration not giving error for use statements before the namespace declaration
    - Adds a new PSR2.Namespaces.UseDeclaration.UseBeforeNamespace error message
- Fixed bug [#1881][sq-1881] : Generic.Arrays.ArrayIndent is indenting sub-arrays incorrectly when comma not used after the last value
- Fixed bug [#1882][sq-1882] : Conditional with missing braces confused by indirect variables
- Fixed bug [#1915][sq-1915] : JS tokenizer fails to tokenize regular expression proceeded by boolean not operator
- Fixed bug [#1920][sq-1920] : Directory exclude pattern improperly excludes files with names that start the same
    - Thanks to [Jeff Puckett][@jpuck] for the patch
- Fixed bug [#1922][sq-1922] : Equal sign alignment check broken when list syntax used before assignment operator
- Fixed bug [#1925][sq-1925] : Generic.Formatting.MultipleStatementAlignment skipping assignments within closures
- Fixed bug [#1931][sq-1931] : Generic opening brace placement sniffs do not correctly support function return types
- Fixed bug [#1932][sq-1932] : Generic.ControlStructures.InlineControlStructure fixer moves new PHPCS annotations
- Fixed bug [#1938][sq-1938] : Generic opening brace placement sniffs incorrectly move PHPCS annotations
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1939][sq-1939] : phpcs:set annotations do not cause the line they are on to be ignored
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1949][sq-1949] : Squiz.PHP.DisallowMultipleAssignments false positive when using namespaces with static assignments
- Fixed bug [#1959][sq-1959] : SquizMultiLineFunctionDeclaration error when param has trailing comment
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1963][sq-1963] : Squiz.Scope.MemberVarScope does not work for multiline member declaration
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1971][sq-1971] : Short array list syntax not correctly tokenized if short array is the first content in a file
- Fixed bug [#1979][sq-1979] : Tokenizer does not change heredoc to nowdoc token if the start tag contains spaces
- Fixed bug [#1982][sq-1982] : Squiz.Arrays.ArrayDeclaration fixer sometimes puts a comma in front of the last array value
- Fixed bug [#1993][sq-1993] : PSR1/PSR2 not reporting or fixing short open tags
- Fixed bug [#1996][sq-1996] : Custom report paths don't work on case-sensitive filesystems
- Fixed bug [#2006][sq-2006] : Squiz.Functions.FunctionDeclarationArgumentSpacing fixer removes comment between parens when no args
    - The SpacingAfterOpenHint error message has been removed
        - It is replaced by the existing SpacingAfterOpen message
    - The error message format for the SpacingAfterOpen and SpacingBeforeClose messages has been changed
        - These used to contain 3 pieces of data, but now only contain 2
    - If you have customised the error messages of this sniff, please review your ruleset after upgrading
- Fixed bug [#2018][sq-2018] : Generic.Formatting.MultipleStatementAlignment does see PHP close tag as end of statement block
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#2027][sq-2027] : PEAR.NamingConventions.ValidFunctionName error when function name includes double underscore
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

[sq-1863]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1863
[sq-1876]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1876
[sq-1881]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1881
[sq-1882]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1882
[sq-1915]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1915
[sq-1920]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1920
[sq-1922]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1922
[sq-1925]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1925
[sq-1931]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1931
[sq-1932]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1932
[sq-1938]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1938
[sq-1939]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1939
[sq-1949]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1949
[sq-1959]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1959
[sq-1963]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1963
[sq-1971]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1971
[sq-1979]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1979
[sq-1982]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1982
[sq-1993]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1993
[sq-1996]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1996
[sq-2006]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2006
[sq-2018]: https://github.com/squizlabs/PHP_CodeSniffer/pull/2018
[sq-2027]: https://github.com/squizlabs/PHP_CodeSniffer/issues/2027

## [3.2.3] - 2018-02-21

### Changed
- The new phpcs: comment syntax can now be prefixed with an at symbol ( @phpcs: )
    - This restores the behaviour of the previous syntax where these comments are ignored by doc generators
- The current PHP version ID is now used to generate cache files
    - This ensures that only cache files generated by the current PHP version are selected
    - This change fixes caching issues when using sniffs that produce errors based on the current PHP version
- A new Tokens::$phpcsCommentTokens array is now available for sniff developers to detect phpcs: comment syntax
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The PEAR.Commenting.FunctionComment.Missing error message now includes the name of the function
    - Thanks to [Yorman Arias][@cixtor] for the patch
- The PEAR.Commenting.ClassComment.Missing and Squiz.Commenting.ClassComment.Missing error messages now include the name of the class
    - Thanks to [Yorman Arias][@cixtor] for the patch
- PEAR.Functions.FunctionCallSignature now only forces alignment at a specific tab stop while fixing
    - It was enforcing this during checking, but this meant invalid errors if the OpeningIndent message was being muted
    - This fixes incorrect errors when using the PSR2 standard with some code blocks
- Generic.Files.LineLength now ignores lines that only contain phpcs: annotation comments
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.Formatting.MultipleStatementAlignment now skips over arrays containing comments
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.PHP.Syntax now forces display_errors to ON when linting
    - Thanks to [Raúl Arellano][@raul338] for the patch
- PSR2.Namespaces.UseDeclaration has improved syntax error handling and closure detection
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.PHP.CommentedOutCode now has improved comment block detection for improved accuracy
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.PHP.NonExecutableCode could fatal error while fixing file with syntax error
- Squiz.PHP.NonExecutableCode now detects unreachable code after a goto statement
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.WhiteSpace.LanguageConstructSpacing has improved syntax error handling while fixing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Improved phpcs: annotation syntax handling for a number of sniffs
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Improved auto-fixing of files with incomplete comment blocks for various commenting sniffs
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

### Fixed
- Fixed test suite compatibility with PHPUnit 7
- Fixed bug [#1793][sq-1793] : PSR2 forcing exact indent for function call opening statements
- Fixed bug [#1803][sq-1803] : Squiz.WhiteSpace.ScopeKeywordSpacing removes member var name while fixing if no space after scope keyword
- Fixed bug [#1817][sq-1817] : Blank line not enforced after control structure if comment on same line as closing brace
- Fixed bug [#1827][sq-1827] : A phpcs:enable comment is not tokenized correctly if it is outside a phpcs:disable block
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1828][sq-1828] : Squiz.WhiteSpace.SuperfluousWhiteSpace ignoreBlankLines property ignores whitespace after single line comments
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1840][sq-1840] : When a comment has too many asterisks, phpcbf gives FAILED TO FIX error
- Fixed bug [#1867][sq-1867] : Can't use phpcs:ignore where the next line is HTML
- Fixed bug [#1870][sq-1870] : Invalid warning in multiple assignments alignment with closure or anon class
- Fixed bug [#1890][sq-1890] : Incorrect Squiz.WhiteSpace.ControlStructureSpacing.NoLineAfterClose error between catch and finally statements
- Fixed bug [#1891][sq-1891] : Comment on last USE statement causes false positive for PSR2.Namespaces.UseDeclaration.SpaceAfterLastUse
    - Thanks to [Matt Coleman][@iammattcoleman], [Daniel Hensby][@dhensby], and [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1901][sq-1901] : Fixed PHPCS annotations in multi-line tab-indented comments + not ignoring whole line for phpcs:set
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

[sq-1793]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1793
[sq-1803]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1803
[sq-1817]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1817
[sq-1827]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1827
[sq-1828]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1828
[sq-1840]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1840
[sq-1867]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1867
[sq-1870]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1870
[sq-1890]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1890
[sq-1891]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1891
[sq-1901]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1901

## [3.2.2] - 2017-12-20

### Changed
- Disabled STDIN detection on Windows
    - This fixes a problem with IDE plugins (e.g., PHPStorm) hanging on Windows

## [3.2.1] - 2017-12-18

### Changed
- Empty diffs are no longer followed by a newline character (request [#1781][sq-1781])
- Generic.Functions.OpeningFunctionBraceKernighanRitchie no longer complains when the open brace is followed by a close tag
    - This makes the sniff more useful when used in templates
    - Thanks to [Joseph Zidell][@josephzidell] for the patch

### Fixed
- Fixed problems with some scripts and plugins waiting for STDIN
    - This was a notable problem with IDE plugins (e.g., PHPStorm) and build systems
- Fixed bug [#1782][sq-1782] : Incorrect detection of operator in ternary + anonymous function

[sq-1781]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1781
[sq-1782]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1782

## [3.2.0] - 2017-12-13

### Deprecated
- This release deprecates the @codingStandards comment syntax used for sending commands to PHP_CodeSniffer
    - The existing syntax will continue to work in all version 3 releases, but will be removed in version 4
    - The comment formats have been replaced by a shorter syntax:
        - @codingStandardsIgnoreFile becomes phpcs:ignoreFile
        - @codingStandardsIgnoreStart becomes phpcs:disable
        - @codingStandardsIgnoreEnd becomes phpcs:enable
        - @codingStandardsIgnoreLine becomes phpcs:ignore
        - @codingStandardsChangeSetting becomes phpcs:set
    - The new syntax allows for additional developer comments to be added after a -- separator
        - This is useful for describing why a code block is being ignored, or why a setting is being changed
        - E.g., phpcs:disable -- This code block must be left as-is.
    - Comments using the new syntax are assigned new comment token types to allow them to be detected:
        - phpcs:ignoreFile has the token T_PHPCS_IGNORE_FILE
        - phpcs:disable has the token T_PHPCS_DISABLE
        - phpcs:enable has the token T_PHPCS_ENABLE
        - phpcs:ignore has the token T_PHPCS_IGNORE
        - phpcs:set has the token T_PHPCS_SET

### Changed
- The phpcs:disable and phpcs:ignore comments can now selectively ignore specific sniffs (request [#604][sq-604])
    - E.g., phpcs:disable Generic.Commenting.Todo.Found for a specific message
    - E.g., phpcs:disable Generic.Commenting.Todo for a whole sniff
    - E.g., phpcs:disable Generic.Commenting for a whole category of sniffs
    - E.g., phpcs:disable Generic for a whole standard
    - Multiple sniff codes can be specified by comma separating them
        - E.g., phpcs:disable Generic.Commenting.Todo,PSR1.Files
- @codingStandardsIgnoreLine comments now only ignore the following line if they are on a line by themselves
    - If they are at the end of an existing line, they will only ignore the line they are on
    - Stops some lines from accidentally being ignored
    - Same rule applies for the new phpcs:ignore comment syntax
- PSR1.Files.SideEffects now respects the new phpcs:disable comment syntax
    - The sniff will no longer check any code that is between phpcs:disable and phpcs:enable comments
    - The sniff does not support phpcs:ignore; you must wrap code structures with disable/enable comments
    - Previously, there was no way to have this sniff ignore parts of a file
- Fixed a problem where PHPCS would sometimes hang waiting for STDIN, or read incomplete versions of large files
    - Thanks to [Arne Jørgensen][@arnested] for the patch
- Array properties specified in ruleset files now have their keys and values trimmed
    - This saves having to do this in individual sniffs and stops errors introduced by whitespace in rulesets
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added phpcs.xsd to allow validation of ruleset XML files
    - Thanks to [Renaat De Muynck][@renaatdemuynck] for the contribution
- File paths specified using --stdin-path can now point to fake file locations (request [#1488][sq-1488])
    - Previously, STDIN files using fake file paths were excluded from checking
- Setting an empty basepath (--basepath=) on the CLI will now clear a basepath set directly in a ruleset
    - Thanks to [Xaver Loppenstedt][@xalopp] for the patch
- Ignore patterns are now checked on symlink target paths instead of symlink source paths
    - Restores previous behaviour of this feature
- Metrics were being double counted when multiple sniffs were recording the same metric
- Added support for bash process substitution
    - Thanks to [Scott Dutton][@exussum12] for the contribution
- Files included in the cache file code hash are now sorted to aid in cache file reuse across servers
- Windows BAT files can now be used outside a PEAR install
    - You must have the path to PHP set in your PATH environment variable
    - Thanks to [Joris Debonnet][@JorisDebonnet] for the patch
- The JS unsigned right shift assignment operator is now properly classified as an assignment operator
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The AbstractVariableSniff abstract sniff now supports anonymous classes and nested functions
    - Also fixes an issue with Squiz.Scope.MemberVarScope where member vars of anonymous classes were not being checked
- Added AbstractArraySniff to make it easier to create sniffs that check array formatting
    - Allows for checking of single and multi line arrays easily
    - Provides a parsed structure of the array including positions of keys, values, and double arrows
- Added Generic.Arrays.ArrayIndent to enforce a single tab stop indent for array keys in multi-line arrays
    - Also ensures the close brace is on a new line and indented to the same level as the original statement
    - Allows for the indent size to be set using an "indent" property of the sniff
- Added Generic.PHP.DiscourageGoto to warn about the use of the GOTO language construct
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the contribution
- Generic.Debug.ClosureLinter was not running the gjslint command
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Generic.WhiteSpace.DisallowSpaceIndent now fixes space indents in multi-line block comments
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.WhiteSpace.DisallowSpaceIndent now fixes mixed space/tab indents more accurately
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.WhiteSpace.DisallowTabIndent now fixes tab indents in multi-line block comments
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- PEAR.Functions.FunctionDeclaration no longer errors when a function declaration is the first content in a JS file
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- PEAR.Functions.FunctionCallSignature now requires the function name to be indented to an exact tab stop
    - If the function name is not the start of the statement, the opening statement must be indented correctly instead
    - Added a new fixable error code PEAR.Functions.FunctionCallSignature.OpeningIndent for this error
- Squiz.Functions.FunctionDeclarationArgumentSpacing is no longer confused about comments in function declarations
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.PHP.NonExecutableCode error messages now indicate which line the code block ending is on
    - Makes it easier to identify where the code block exited or returned
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Commenting.FunctionComment now supports nullable type hints
- Squiz.Commenting.FunctionCommentThrowTag no longer assigns throw tags inside anon classes to the enclosing function
- Squiz.WhiteSpace.SemicolonSpacing now ignores semicolons used for empty statements inside FOR conditions
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.ControlStructures.ControlSignature now allows configuring the number of spaces before the colon in alternative syntax
    - Override the 'requiredSpacesBeforeColon' setting in a ruleset.xml file to change
    - Default remains at 1
    - Thanks to [Nikola Kovacs][@nkovacs] for the patch
- The Squiz standard now ensures array keys are indented 4 spaces from the main statement
    - Previously, this standard aligned keys 1 space from the start of the array keyword
- The Squiz standard now ensures array end braces are aligned with the main statement
    - Previously, this standard aligned the close brace with the start of the array keyword
- The standard for PHP_CodeSniffer itself now enforces short array syntax
- The standard for PHP_CodeSniffer itself now uses the Generic.Arrays/ArrayIndent sniff rules
- Improved fixer conflicts and syntax error handling for a number of sniffs
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

### Fixed
- Fixed bug [#1462][sq-1462] : Error processing cyrillic strings in Tokenizer
- Fixed bug [#1573][sq-1573] : Squiz.WhiteSpace.LanguageConstructSpacing does not properly check for tabs and newlines
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Fixed bug [#1590][sq-1590] : InlineControlStructure CBF issue while adding braces to an if that's returning a nested function
- Fixed bug [#1718][sq-1718] : Unclosed strings at EOF sometimes tokenized as T_WHITESPACE by the JS tokenizer
- Fixed bug [#1731][sq-1731] : Directory exclusions do not work as expected when a single file name is passed to phpcs
- Fixed bug [#1737][sq-1737] : Squiz.CSS.EmptyStyleDefinition sees comment as style definition and fails to report error
- Fixed bug [#1746][sq-1746] : Very large reports can sometimes become garbled when using the parallel option
- Fixed bug [#1747][sq-1747] : Squiz.Scope.StaticThisUsage incorrectly looking inside closures
- Fixed bug [#1757][sq-1757] : Unknown type hint "object" in Squiz.Commenting.FunctionComment
- Fixed bug [#1758][sq-1758] : PHPCS gets stuck creating file list when processing circular symlinks
- Fixed bug [#1761][sq-1761] : Generic.WhiteSpace.ScopeIndent error on multi-line function call with static closure argument
- Fixed bug [#1762][sq-1762] : `Generic.WhiteSpace.Disallow[Space/Tab]Indent` not inspecting content before open tag
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1769][sq-1769] : Custom "define" function triggers a warning about declaring new symbols
- Fixed bug [#1776][sq-1776] : Squiz.Scope.StaticThisUsage incorrectly looking inside anon classes
- Fixed bug [#1777][sq-1777] : Generic.WhiteSpace.ScopeIndent incorrect indent errors when self called function proceeded by comment

[sq-604]: https://github.com/squizlabs/PHP_CodeSniffer/issues/604
[sq-1462]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1462
[sq-1488]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1488
[sq-1573]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1573
[sq-1590]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1590
[sq-1718]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1718
[sq-1731]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1731
[sq-1737]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1737
[sq-1746]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1746
[sq-1747]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1747
[sq-1757]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1757
[sq-1758]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1758
[sq-1761]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1761
[sq-1762]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1762
[sq-1769]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1769
[sq-1776]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1776
[sq-1777]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1777

## [3.1.1] - 2017-10-17

### Changed
- Restored preference of non-dist files over dist files for phpcs.xml and phpcs.xml.dist
    - The order that the files are searched is now: .phpcs.xml, phpcs.xml, .phpcs.xml.dist, phpcs.xml.dist
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Progress output now correctly shows skipped files
- Progress output now shows 100% when the file list has finished processing (request [#1697][sq-1697])
- Stopped some IDEs complaining about testing class aliases
    - Thanks to [Vytautas Stankus][@svycka] for the patch
- Squiz.Commenting.InlineComment incorrectly identified comment blocks in some cases, muting some errors
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

### Fixed
- Fixed bug [#1512][sq-1512] : PEAR.Functions.FunctionCallSignature enforces spaces when no arguments if required spaces is not 0
- Fixed bug [#1522][sq-1522] : Squiz Arrays.ArrayDeclaration and Strings.ConcatenationSpacing fixers causing parse errors with here/nowdocs
- Fixed bug [#1570][sq-1570] : Squiz.Arrays.ArrayDeclaration fixer removes comments between array keyword and open parentheses
- Fixed bug [#1604][sq-1604] : File::isReference has problems with some bitwise operators and class property references
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1645][sq-1645] : Squiz.Commenting.InlineComment will fail to fix comments at the end of the file
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1656][sq-1656] : Using the --sniffs argument has a problem with case sensitivity
- Fixed bug [#1657][sq-1657] : Uninitialized string offset: 0 when sniffing CSS
- Fixed bug [#1669][sq-1669] : Temporary expression proceeded by curly brace is detected as function call
- Fixed bug [#1681][sq-1681] : Huge arrays are super slow to scan with Squiz.Arrays.ArrayDeclaration sniff
- Fixed bug [#1694][sq-1694] : Squiz.Arrays.ArrayBracketSpacing is removing some comments during fixing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1702][sq-1702] : Generic.WhiteSpaceDisallowSpaceIndent fixer bug when line only contains superfluous whitespace

[sq-1512]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1512
[sq-1522]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1522
[sq-1570]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1570
[sq-1604]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1604
[sq-1645]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1645
[sq-1656]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1656
[sq-1657]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1657
[sq-1669]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1669
[sq-1681]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1681
[sq-1694]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1694
[sq-1697]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1697
[sq-1702]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1702

## [3.1.0] - 2017-09-20

### Changed
- This release includes a change to support newer versions of PHPUnit (versions 4, 5, and 6 are now supported)
    - The custom PHP_CodeSniffer test runner now requires a bootstrap file
    - Developers with custom standards using the PHP_CodeSniffer test runner will need to do one of the following:
        - run your unit tests from the PHP_CodeSniffer root dir so the bootstrap file is included
        - specify the PHP_CodeSniffer bootstrap file on the command line: `phpunit --bootstrap=/path/to/phpcs/tests/bootstrap.php`
        - require the PHP_CodeSniffer bootstrap file from your own bootstrap file
    - If you don't run PHP_CodeSniffer unit tests, this change will not affect you
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- A phpcs.xml or phpcs.xml.dist file now takes precedence over the default_standard config setting
    - Thanks to [Björn Fischer][@Fischer-Bjoern] for the patch
- Both phpcs.xml and phpcs.xml.dist files can now be prefixed with a dot (request [#1566][sq-1566])
    - The order that the files are searched is: .phpcs.xml, .phpcs.xml.dist, phpcs.xml, phpcs.xml.dist
- The autoloader will now search for files during unit tests runs from the same locations as during normal phpcs runs
    - Allows for easier unit testing of custom standards that use helper classes or custom namespaces
- Include patterns for sniffs now use OR logic instead of AND logic
    - Previously, a file had to be in each of the include patterns to be processed by a sniff
    - Now, a file has to only be in at least one of the patterns
    - This change reflects the original intention of the feature
- PHPCS will now follow symlinks under the list of checked directories
    - This previously only worked if you specified the path to a symlink on the command line
- Output from --config-show, --config-set, and --config-delete now includes the path to the loaded config file
- PHPCS now cleanly exits if its config file is not readable
    - Previously, a combination of PHP notices and PHPCS errors would be generated
- Comment tokens that start with /** are now always tokenized as docblocks
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- The PHP-supplied T_YIELD and T_YIELD_FROM token have been replicated for older PHP versions
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Added new Generic.CodeAnalysis.AssignmentInCondition sniff to warn about variable assignments inside conditions
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the contribution
- Added Generic.Files.OneObjectStructurePerFile sniff to ensure there is a single class/interface/trait per file
    - Thanks to [Mponos George][@gmponos] for the contribution
- Function call sniffs now check variable function names and self/static object creation
    - Specific sniffs are Generic.Functions.FunctionCallArgumentSpacing, PEAR.Functions.FunctionCallSignature, and PSR2.Methods.FunctionCallSignature
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Generic.Files.LineLength can now be configured to ignore all comment lines, no matter their length
    - Set the ignoreComments property to TRUE (default is FALSE) in your ruleset.xml file to enable this
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.PHP.LowerCaseKeyword now checks self, parent, yield, yield from, and closure (function) keywords
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- PEAR.Functions.FunctionDeclaration now removes a blank line if it creates one by moving the curly brace during fixing
- Squiz.Commenting.FunctionCommentThrowTag now supports PHP 7.1 multi catch exceptions
- Squiz.Formatting.OperatorBracket no longer throws errors for PHP 7.1 multi catch exceptions
- Squiz.Commenting.LongConditionClosingComment now supports finally statements
- Squiz.Formatting.OperatorBracket now correctly fixes pipe separated flags
- Squiz.Formatting.OperatorBracket now correctly fixes statements containing short array syntax
- Squiz.PHP.EmbeddedPhp now properly fixes cases where the only content in an embedded PHP block is a comment
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.WhiteSpace.ControlStructureSpacing now ignores comments when checking blank lines at the top of control structures
- Squiz.WhiteSpace.ObjectOperatorSpacing now detects and fixes spaces around double colons
    - Thanks to [Julius Šmatavičius][@bondas83] for the patch
- Squiz.WhiteSpace.MemberVarSpacing can now be configured to check any number of blank lines between member vars
    - Set the spacing property (default is 1) in your ruleset.xml file to set the spacing
- Squiz.WhiteSpace.MemberVarSpacing can now be configured to check a different number of blank lines before the first member var
    - Set the spacingBeforeFirst property (default is 1) in your ruleset.xml file to set the spacing
- Added a new PHP_CodeSniffer\Util\Tokens::$ooScopeTokens static member var for quickly checking object scope
    - Includes T_CLASS, T_ANON_CLASS, T_INTERFACE, and T_TRAIT
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- PHP_CodeSniffer\Files\File::findExtendedClassName() now supports extended interfaces
    - Thanks to [Martin Hujer][@mhujer] for the patch

### Fixed
- Fixed bug [#1550][sq-1550] : Squiz.Commenting.FunctionComment false positive when function contains closure
- Fixed bug [#1577][sq-1577] : Generic.InlineControlStructureSniff breaks with a comment between body and condition in do while loops
- Fixed bug [#1581][sq-1581] : Sniffs not loaded when one-standard directories are being registered in installed_paths
- Fixed bug [#1591][sq-1591] : Autoloader failing to load arbitrary files when installed_paths only set via a custom ruleset
- Fixed bug [#1605][sq-1605] : Squiz.WhiteSpace.OperatorSpacing false positive on unary minus after comment
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1615][sq-1615] : Uncaught RuntimeException when phpcbf fails to fix files
- Fixed bug [#1637][sq-1637] : Generic.WhiteSpaceScopeIndent closure argument indenting incorrect with multi-line strings
- Fixed bug [#1638][sq-1638] : Squiz.WhiteSpace.ScopeClosingBrace closure argument indenting incorrect with multi-line strings
- Fixed bug [#1640][sq-1640] : Squiz.Strings.DoubleQuoteUsage replaces tabs with spaces when fixing
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

[sq-1550]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1550
[sq-1566]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1566
[sq-1577]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1577
[sq-1581]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1581
[sq-1591]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1591
[sq-1605]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1605
[sq-1615]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1615
[sq-1637]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1637
[sq-1638]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1638
[sq-1640]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1640

## [3.0.2] - 2017-07-18

### Changed
- The code report now gracefully handles tokenizer exceptions
- The phpcs and phpcbf scripts are now the only places that exit() in the code
    - This allows for easier usage of core PHPCS functions from external scripts
    - If you are calling Runner::runPHPCS() or Runner::runPHPCBF() directly, you will get back the full range of exit codes
    - If not, catch the new DeepExitException to get the error message ($e->getMessage()) and exit code ($e->getCode());
- NOWDOC tokens are now considered conditions, just as HEREDOC tokens are
    - This makes it easier to find the start and end of a NOWDOC from any token within it
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Custom autoloaders are now only included once in case multiple standards are using the same one
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Improved tokenizing of fallthrough CASE and DEFAULT statements that share a closing statement and use curly braces
- Improved the error message when Squiz.ControlStructures.ControlSignature detects a newline after the closing parenthesis

### Fixed
- Fixed a problem where the source report was not printing the correct number of errors found
- Fixed a problem where the --cache=/path/to/cachefile CLI argument was not working
- Fixed bug [#1465][sq-1465] : Generic.WhiteSpace.ScopeIndent reports incorrect errors when indenting double arrows in short arrays
- Fixed bug [#1478][sq-1478] : Indentation in fallthrough CASE that contains a closure
- Fixed bug [#1497][sq-1497] : Fatal error if composer prepend-autoloader is set to false
    - Thanks to [Kunal Mehta][@legoktm] for the patch
- Fixed bug [#1503][sq-1503] : Alternative control structure syntax not always recognized as scoped
- Fixed bug [#1523][sq-1523] : Fatal error when using the --suffix argument
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1526][sq-1526] : Use of basepath setting can stop PHPCBF being able to write fixed files
- Fixed bug [#1530][sq-1530] : Generic.WhiteSpace.ScopeIndent can increase indent too much for lines within code blocks
- Fixed bug [#1547][sq-1547] : Wrong token type for backslash in use function
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Fixed bug [#1549][sq-1549] : Squiz.PHP.EmbeddedPhp fixer conflict with // comment before PHP close tag
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1560][sq-1560] : Squiz.Commenting.FunctionComment fatal error when fixing additional param comment lines that have no indent

[sq-1465]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1465
[sq-1478]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1478
[sq-1497]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1497
[sq-1503]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1503
[sq-1523]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1523
[sq-1526]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1526
[sq-1530]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1530
[sq-1547]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1547
[sq-1549]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1549
[sq-1560]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1560

## [3.0.1] - 2017-06-14

### Security
- This release contains a fix for a security advisory related to the improper handling of a shell command
    - A properly crafted filename would allow for arbitrary code execution when using the --filter=gitmodified command line option
    - All version 3 users are encouraged to upgrade to this version, especially if you are checking 3rd-party code
        - e.g., you run PHPCS over libraries that you did not write
        - e.g., you provide a web service that runs PHPCS over user-uploaded files or 3rd-party repositories
        - e.g., you allow external tool paths to be set by user-defined values
    - If you are unable to upgrade but you check 3rd-party code, ensure you are not using the Git modified filter
    - This advisory does not affect PHP_CodeSniffer version 2.
    - Thanks to [Sergei Morozov][@morozov] for the report and patch

### Changed
- Arguments on the command line now override or merge with those specified in a ruleset.xml file in all cases
- PHPCS now stops looking for a phpcs.xml file as soon as one is found, favoring the closest one to the current dir
- Added missing help text for the --stdin-path CLI option to --help
- Re-added missing help text for the --file-list and --bootstrap CLI options to --help
- Runner::runPHPCS() and Runner::runPHPCBF() now return an exit code instead of exiting directly (request [#1484][sq-1484])
- The Squiz standard now enforces short array syntax by default
- The autoloader is now working correctly with classes created with class_alias()
- The autoloader will now search for files inside all directories in the installed_paths config var
    - This allows autoloading of files inside included custom coding standards without manually requiring them
- You can now specify a namespace for a custom coding standard, used by the autoloader to load non-sniff helper files
    - Also used by the autoloader to help other standards directly include sniffs for your standard
    - Set the value to the namespace prefix you are using for sniff files (everything up to \Sniffs\)
    - e.g., if your namespace format is MyProject\CS\Standard\Sniffs\Category set the namespace to MyProject\CS\Standard
    - If omitted, the namespace is assumed to be the same as the directory name containing the ruleset.xml file
    - The namespace is set in the ruleset tag of the ruleset.xml file
    - e.g., ruleset name="My Coding Standard" namespace="MyProject\CS\Standard"
- Rulesets can now specify custom autoloaders using the new autoload tag
    - Autoloaders are included while the ruleset is being processed and before any custom sniffs are included
    - Allows for very custom autoloading of helper classes well before the boostrap files are included
- The PEAR standard now includes Squiz.Commenting.DocCommentAlignment
    - It previously broke comments onto multiple lines, but didn't align them

### Fixed
- Fixed a problem where excluding a message from a custom standard's own sniff would exclude the whole sniff
    - This caused some PSR2 errors to be under-reported
- Fixed bug [#1442][sq-1442] : T_NULLABLE detection not working for nullable parameters and return type hints in some cases
- Fixed bug [#1447][sq-1447] : Running the unit tests with a PHPUnit config file breaks the test suite
    - Unknown arguments were not being handled correctly, but are now stored in $config->unknown
- Fixed bug [#1449][sq-1449] : Generic.Classes.OpeningBraceSameLine doesn't detect comment before opening brace
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1450][sq-1450] : Coding standard located under an installed_path with the same directory name throws an error
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1451][sq-1451] : Sniff exclusions/restrictions don't work with custom sniffs unless they use the PHP_CodeSniffer NS
- Fixed bug [#1454][sq-1454] : Squiz.WhiteSpace.OperatorSpacing is not checking spacing on either side of a short ternary operator
    - Thanks to [Mponos George][@gmponos] for the patch
- Fixed bug [#1495][sq-1495] : Setting an invalid installed path breaks all commands
- Fixed bug [#1496][sq-1496] : Squiz.Strings.DoubleQuoteUsage not unescaping dollar sign when fixing
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Fixed bug [#1501][sq-1501] : Interactive mode is broken
- Fixed bug [#1504][sq-1504] : PSR2.Namespaces.UseDeclaration hangs fixing use statement with no trailing code

[sq-1447]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1447
[sq-1449]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1449
[sq-1450]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1450
[sq-1451]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1451
[sq-1454]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1454
[sq-1484]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1484
[sq-1495]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1495
[sq-1496]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1496
[sq-1501]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1501
[sq-1504]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1504

## [2.9.1] - 2017-05-22

### Fixed
- Fixed bug [#1442][sq-1442] : T_NULLABLE detection not working for nullable parameters and return type hints in some cases
- Fixed bug [#1448][sq-1448] : Generic.Classes.OpeningBraceSameLine doesn't detect comment before opening brace
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch

[sq-1442]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1442
[sq-1448]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1448

## [3.0.0] - 2017-05-04

### Changed
- Added an --ignore-annotations command line argument to ignore all @codingStandards annotations in code comments (request [#811][sq-811])
- This allows you to force errors to be shown that would otherwise be ignored by code comments
    - Also stop files being able to change sniff properties midway through processing
- An error is now reported if no sniffs were registered to be run (request [#1129][sq-1129])
- The autoloader will now search for files inside the directory of any loaded coding standard
    - This allows autoloading of any file inside a custom coding standard without manually requiring them
    - Ensure your namespace begins with your coding standard's directory name and follows PSR-4
    - e.g., StandardName\Sniffs\CategoryName\AbstractHelper or StandardName\Helpers\StringSniffHelper
- Fixed an error where STDIN was sometimes not checked when using the --parallel CLI option
- The is_closure index has been removed from the return value of File::getMethodProperties()
    - This value was always false because T_FUNCTION tokens are never closures
    - Closures have a token type of T_CLOSURE
- The File::isAnonymousFunction() method has been removed
    - This function always returned false because it only accepted T_FUNCTION tokens, which are never closures
    - Closures have a token type of T_CLOSURE
- Includes all changes from the 2.9.0 release

### Fixed
- Fixed bug [#834][sq-834] : PSR2.ControlStructures.SwitchDeclaration does not handle if branches with returns
    - Thanks to [Fabian Wiget][@fabacino] for the patch

[sq-811]: https://github.com/squizlabs/PHP_CodeSniffer/issues/811
[sq-834]: https://github.com/squizlabs/PHP_CodeSniffer/issues/834
[sq-1129]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1129

## [3.0.0RC4] - 2017-03-02

### Security
- This release contains a fix for a security advisory related to the improper handling of shell commands
    - Uses of shell_exec() and exec() were not escaping filenames and configuration settings in most cases
    - A properly crafted filename or configuration option would allow for arbitrary code execution when using some features
    - All users are encouraged to upgrade to this version, especially if you are checking 3rd-party code
        - e.g., you run PHPCS over libraries that you did not write
        - e.g., you provide a web service that runs PHPCS over user-uploaded files or 3rd-party repositories
        - e.g., you allow external tool paths to be set by user-defined values
    - If you are unable to upgrade but you check 3rd-party code, ensure you are not using the following features:
        - The diff report
        - The notify-send report
        - The Generic.PHP.Syntax sniff
        - The Generic.Debug.CSSLint sniff
        - The Generic.Debug.ClosureLinter sniff
        - The Generic.Debug.JSHint sniff
        - The Squiz.Debug.JSLint sniff
        - The Squiz.Debug.JavaScriptLint sniff
        - The Zend.Debug.CodeAnalyzer sniff
    - Thanks to [Klaus Purer][@klausi] for the report

### Changed
- The indent property of PEAR.Classes.ClassDeclaration has been removed
    - Instead of calculating the indent of the brace, it just ensures the brace is aligned with the class keyword
    - Other sniffs can be used to ensure the class itself is indented correctly
- Invalid exclude rules inside a ruleset.xml file are now ignored instead of potentially causing out of memory errors
    - Using the -vv command line argument now also shows the invalid exclude rule as XML
- Includes all changes from the 2.8.1 release

### Fixed
- Fixed bug [#1333][sq-1333] : The new autoloader breaks some frameworks with custom autoloaders
- Fixed bug [#1334][sq-1334] : Undefined offset when explaining standard with custom sniffs

[sq-1333]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1333
[sq-1334]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1334

## [3.0.0RC3] - 2017-02-02

### Changed
- Added support for ES6 class declarations
    - Previously, these class were tokenized as JS objects but are now tokenized as normal T_CLASS structures
- Added support for ES6 method declarations, where the "function" keyword is not used
    - Previously, these methods were tokenized as JS objects (fixes bug [#1251][sq-1251])
    - The name of the ES6 method is now assigned the T_FUNCTION keyword and treated like a normal function
    - Custom sniffs that support JS and listen for T_FUNCTION tokens can't assume the token represents the word "function"
    - Check the contents of the token first, or use $phpcsFile->getDeclarationName($stackPtr) if you just want its name
    - There is no change for custom sniffs that only check PHP code
- PHPCBF exit codes have been changed so they are now more useful (request [#1270][sq-1270])
    - Exit code 0 is now used to indicate that no fixable errors were found, and so nothing was fixed
    - Exit code 1 is now used to indicate that all fixable errors were fixed correctly
    - Exit code 2 is now used to indicate that PHPCBF failed to fix some of the fixable errors it found
    - Exit code 3 is now used for general script execution errors
- Added PEAR.Commenting.FileComment.ParamCommentAlignment to check alignment of multi-line param comments
- Includes all changes from the 2.8.0 release

### Fixed
- Fixed an issue where excluding a file using a @codingStandardsIgnoreFile comment would produce errors
    - For PHPCS, it would show empty files being processed
    - For PHPCBF, it would produce a PHP error
- Fixed bug [#1233][sq-1233] : Can't set config data inside ruleset.xml file
- Fixed bug [#1241][sq-1241] : CodeSniffer.conf not working with 3.x PHAR file

[sq-1233]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1233
[sq-1241]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1241
[sq-1251]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1251
[sq-1270]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1270

## [3.0.0RC2] - 2016-11-30

### Changed
- Made the Runner class easier to use with wrapper scripts
- Full usage information is no longer printed when a usage error is encountered (request [#1186][sq-1186])
    - Makes it a lot easier to find and read the error message that was printed
- Includes all changes from the 2.7.1 release

### Fixed
- Fixed an undefined var name error that could be produced while running PHPCBF
- Fixed bug [#1167][sq-1167] : 3.0.0RC1 PHAR does not work with PEAR standard
- Fixed bug [#1208][sq-1208] : Excluding files doesn't work when using STDIN with a filename specified

[sq-1167]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1167
[sq-1186]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1186
[sq-1208]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1208

## [3.0.0RC1] - 2016-09-02

### Changed
- Progress output now shows E and W in green when a file has fixable errors or warnings
    - Only supported if colors are enabled
- PHPCBF no longer produces verbose output by default (request [#699][sq-699])
    - Use the -v command line argument to show verbose fixing output
    - Use the -q command line argument to disable verbose information if enabled by default
- PHPBF now prints a summary report after fixing files
    - Report shows files that were fixed, how many errors were fixed, and how many remain
- PHPCBF now supports the -p command line argument to print progress information
    - Prints a green F for files where fixes occurred
    - Prints a red E for files that could not be fixed due to an error
    - Use the -q command line argument to disable progress information if enabled by default
- Running unit tests using --verbose no longer throws errors
- Includes all changes from the 2.7.0 release

### Fixed
- Fixed shell error appearing on some systems when trying to find executable paths

[sq-699]: https://github.com/squizlabs/PHP_CodeSniffer/issues/699

## [3.0.0a1] - 2016-07-20

### Changed
- Min PHP version increased from 5.1.2 to 5.4.0
- Added optional caching of results between runs (request [#530][sq-530])
    - Enable the cache by using the --cache command line argument
    - If you want the cache file written somewhere specific, use --cache=/path/to/cacheFile
    - Use the command "phpcs --config-set cache true" to turn caching on by default
    - Use the --no-cache command line argument to disable caching if it is being turned on automatically
- Add support for checking file in parallel (request [#421][sq-421])
    - Tell PHPCS how many files to check at once using the --parallel command line argument
    - To check 100 files at once, using --parallel=100
    - To disable parallel checking if it is being turned on automatically, use --parallel=1
    - Requires PHP to be compiled with the PCNTL package
- The default encoding has been changed from iso-8859-1 to utf-8 (request [#760][sq-760])
    - The --encoding command line argument still works, but you no longer have to set it to process files as utf-8
    - If encoding is being set to utf-8 in a ruleset or on the CLI, it can be safely removed
    - If the iconv PHP extension is not installed, standard non-multibyte aware functions will be used
- Added a new "code" report type to show a code snippet for each error (request [#419][sq-419])
    - The line containing the error is printed, along with 2 lines above and below it to show context
    - The location of the errors is underlined in the code snippet if you also use --colors
    - Use --report=code to generate this report
- Added support for custom filtering of the file list
    - Developers can write their own filter classes to perform custom filtering of the list before the run starts
    - Use the command line arg `--filter=/path/to/filter.php` to specify a filter to use
    - Extend \PHP_CodeSniffer\Filters\Filter to also support the core PHPCS extension and path filtering
    - Extend \PHP_CodeSniffer\Filters\ExactMatch to get the core filtering and the ability to use blacklists and whitelists
    - The included \PHP_CodeSniffer\Filters\GitModified filter is a good example of an ExactMatch filter
- Added support for only checking files that have been locally modified or added in a git repo
    - Use --filter=gitmodified to check these files
    - You still need to give PHPCS a list of files or directories in which to check
- Added automatic discovery of executable paths (request [#571][sq-571])
    - Thanks to [Sergei Morozov][@morozov] for the patch
- You must now pass "-" on the command line to have PHPCS wait for STDIN
    - E.g., phpcs --standard=PSR2 -
    - You can still pipe content via STDIN as normal as PHPCS will see this and process it
    - But without the "-", PHPCS will throw an error if no content or files are passed to it
- All PHP errors generated by sniffs are caught, re-thrown as exceptions, and reported in the standard error reports
    - This should stop bugs inside sniffs causing infinite loops
    - Also stops invalid reports being produced as errors don't print to the screen directly
- Sniff codes are no longer optional
    - If a sniff throws an error or a warning, it must specify an internal code for that message
- The installed_paths config setting can now point directly to a standard
    - Previously, it had to always point to the directory in which the standard lives
- Multiple reports can now be specified using the --report command line argument
    - Report types are separated by commas
    - E.g., --report=full,summary,info
    - Previously, you had to use one argument for each report such as --report=full --report=summary --report=info
- You can now set the severity, message type, and exclude patterns for an entire sniff, category, or standard
    - Previously, this was only available for a single message
- You can now include a single sniff code in a ruleset instead of having to include an entire sniff
    - Including a sniff code will automatically exclude all other messages from that sniff
    - If the sniff is already included by an imported standard, set the sniff severity to 0 and include the specific message you want
- PHPCBF no longer uses patch
    - Files are now always overwritten
    - The --no-patch option has been removed
- Added a --basepath option to strip a directory from the front of file paths in output (request [#470][sq-470])
    - The basepath is absolute or relative to the current directory
    - E.g., to output paths relative to current dir in reports, use --basepath=.
- Ignore rules are now checked when using STDIN (request [#733][sq-733])
- Added an include-pattern tag to rulesets to include a sniff for specific files and folders only (request [#656][sq-656])
    - This is the exact opposite of the exclude-pattern tag
    - This option is only usable within sniffs, not globally like exclude-patterns are
- Added a new -m option to stop error messages from being recorded, which saves a lot of memory
    - PHPCBF always uses this setting to reduce memory as it never outputs error messages
    - Setting the $recordErrors member var inside custom report classes is no longer supported (use -m instead)
- Exit code 2 is now used to indicate fixable errors were found (request [#930][sq-930])
    - Exit code 3 is now used for general script execution errors
    - Exit code 1 is used to indicate that coding standard errors were found, but none are fixable
    - Exit code 0 is unchanged and continues to mean no coding standard errors found

### Removed
- The included PHPCS standard has been removed
    - All rules are now found inside the phpcs.xml.dist file
    - Running "phpcs" without any arguments from a git clone will use this ruleset
- The included SVN pre-commit hook has been removed
    - Hooks for version control systems will no longer be maintained within the PHPCS project

[sq-419]: https://github.com/squizlabs/PHP_CodeSniffer/issues/419
[sq-421]: https://github.com/squizlabs/PHP_CodeSniffer/issues/421
[sq-470]: https://github.com/squizlabs/PHP_CodeSniffer/issues/470
[sq-530]: https://github.com/squizlabs/PHP_CodeSniffer/issues/530
[sq-571]: https://github.com/squizlabs/PHP_CodeSniffer/pull/571
[sq-656]: https://github.com/squizlabs/PHP_CodeSniffer/issues/656
[sq-733]: https://github.com/squizlabs/PHP_CodeSniffer/issues/733
[sq-760]: https://github.com/squizlabs/PHP_CodeSniffer/issues/760
[sq-930]: https://github.com/squizlabs/PHP_CodeSniffer/issues/930

## [2.9.0] - 2017-05-04

### Changed
- Added Generic.Debug.ESLint sniff to run ESLint over JS files and report errors
    - Set eslint path using: phpcs --config-set eslint_path /path/to/eslint
    - Thanks to [Ryan McCue][@rmccue] for the contribution
- T_POW is now properly considered an arithmetic operator, and will be checked as such
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- T_SPACESHIP and T_COALESCE are now properly considered comparison operators, and will be checked as such
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.PHP.DisallowShortOpenTag now warns about possible short open tags even when short_open_tag is set to OFF
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.WhiteSpace.DisallowTabIndent now finds and fixes improper use of spaces anywhere inside the line indent
    - Previously, only the first part of the indent was used to determine the indent type
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- PEAR.Commenting.ClassComment now supports checking of traits as well as classes and interfaces
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Commenting.FunctionCommentThrowTag now supports re-throwing exceptions (request [#946][sq-946])
    - Thanks to [Samuel Levy][@samlev] for the patch
- Squiz.PHP.DisallowMultipleAssignments now ignores PHP4-style member var assignments
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.WhiteSpace.FunctionSpacing now ignores spacing above functions when they are preceded by inline comments
    - Stops conflicts between this sniff and comment spacing sniffs
- Squiz.WhiteSpace.OperatorSpacing no longer checks the equal sign in declare statements
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added missing error codes for a couple of sniffs so they can now be customised as normal

### Fixed
- Fixed bug [#1266][sq-1266] : PEAR.WhiteSpace.ScopeClosingBrace can throw an error while fixing mixed PHP/HTML
- Fixed bug [#1364][sq-1364] : Yield From values are not recognised as returned values in Squiz FunctionComment sniff
- Fixed bug [#1373][sq-1373] : Error in tab expansion results in white-space of incorrect size
    - Thanks to [Mark Clements][@MarkMaldaba] for the patch
- Fixed bug [#1381][sq-1381] : Tokenizer: dereferencing incorrectly identified as short array
- Fixed bug [#1387][sq-1387] : Squiz.ControlStructures.ControlSignature does not handle alt syntax when checking space after closing brace
- Fixed bug [#1392][sq-1392] : Scope indent calculated incorrectly when using array destructuring
- Fixed bug [#1394][sq-1394] : integer type hints appearing as TypeHintMissing instead of ScalarTypeHintMissing
    - PHP 7 type hints were also being shown when run under PHP 5 in some cases
- Fixed bug [#1405][sq-1405] : Squiz.WhiteSpace.ScopeClosingBrace fails to fix closing brace within indented PHP tags
- Fixed bug [#1421][sq-1421] : Ternaries used in constant scalar expression for param default misidentified by tokenizer
- Fixed bug [#1431][sq-1431] : PHPCBF can't fix short open tags when they are not followed by a space
    - Thanks to [Gonçalo Queirós][@ghunti] for the patch
- Fixed bug [#1432][sq-1432] : PHPCBF can make invalid fixes to inline JS control structures that make use of JS objects

[sq-946]: https://github.com/squizlabs/PHP_CodeSniffer/pull/946
[sq-1266]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1266
[sq-1364]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1364
[sq-1373]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1373
[sq-1381]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1381
[sq-1387]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1387
[sq-1392]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1392
[sq-1394]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1394
[sq-1405]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1405
[sq-1421]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1421
[sq-1431]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1431
[sq-1432]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1432

## [2.8.1] - 2017-03-02

### Security
- This release contains a fix for a security advisory related to the improper handling of shell commands
    - Uses of shell_exec() and exec() were not escaping filenames and configuration settings in most cases
    - A properly crafted filename or configuration option would allow for arbitrary code execution when using some features
    - All users are encouraged to upgrade to this version, especially if you are checking 3rd-party code
          - e.g., you run PHPCS over libraries that you did not write
          - e.g., you provide a web service that runs PHPCS over user-uploaded files or 3rd-party repositories
          - e.g., you allow external tool paths to be set by user-defined values
    - If you are unable to upgrade but you check 3rd-party code, ensure you are not using the following features:
          - The diff report
          - The notify-send report
          - The Generic.PHP.Syntax sniff
          - The Generic.Debug.CSSLint sniff
          - The Generic.Debug.ClosureLinter sniff
          - The Generic.Debug.JSHint sniff
          - The Squiz.Debug.JSLint sniff
          - The Squiz.Debug.JavaScriptLint sniff
          - The Zend.Debug.CodeAnalyzer sniff
    - Thanks to [Klaus Purer][@klausi] for the report

### Changed
- The PHP-supplied T_COALESCE_EQUAL token has been replicated for PHP versions before 7.2
- PEAR.Functions.FunctionDeclaration now reports an error for blank lines found inside a function declaration
- PEAR.Functions.FunctionDeclaration no longer reports indent errors for blank lines in a function declaration
- Squiz.Functions.MultiLineFunctionDeclaration no longer reports errors for blank lines in a function declaration
    - It would previously report that only one argument is allowed per line
- Squiz.Commenting.FunctionComment now corrects multi-line param comment padding more accurately
- Squiz.Commenting.FunctionComment now properly fixes pipe-separated param types
- Squiz.Commenting.FunctionComment now works correctly when function return types also contain a comment
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.ControlStructures.InlineIfDeclaration now supports the elvis operator
    - As this is not a real PHP operator, it enforces no spaces between ? and : when the THEN statement is empty
- Squiz.ControlStructures.InlineIfDeclaration is now able to fix the spacing errors it reports

### Fixed
- Fixed bug [#1340][sq-1340] : STDIN file contents not being populated in some cases
    - Thanks to [David Biňovec][@david-binda] for the patch
- Fixed bug [#1344][sq-1344] : PEAR.Functions.FunctionCallSignatureSniff throws error for blank comment lines
- Fixed bug [#1347][sq-1347] : PSR2.Methods.FunctionCallSignature strips some comments during fixing
    - Thanks to [Algirdas Gurevicius][@uniquexor] for the patch
- Fixed bug [#1349][sq-1349] : Squiz.Strings.DoubleQuoteUsage.NotRequired message is badly formatted when string contains a CR newline char
    - Thanks to [Algirdas Gurevicius][@uniquexor] for the patch
- Fixed bug [#1350][sq-1350] : Invalid Squiz.Formatting.OperatorBracket error when using namespaces
- Fixed bug [#1369][sq-1369] : Empty line in multi-line function declaration cause infinite loop

[sq-1340]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1340
[sq-1344]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1344
[sq-1347]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1347
[sq-1349]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1349
[sq-1350]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1350
[sq-1369]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1369

## [2.8.0] - 2017-02-02

### Changed
- The Internal.NoCodeFound error is no longer generated for content sourced from STDIN
    - This should stop some Git hooks generating errors because PHPCS is trying to process the refs passed on STDIN
- Squiz.Commenting.DocCommentAlignment now checks comments on class properties defined using the VAR keyword
    - Thanks to [Klaus Purer][@klausi] for the patch
- The getMethodParameters() method now recognises "self" as a valid type hint
    - The return array now contains a new "content" index containing the raw content of the param definition
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The getMethodParameters() method now supports nullable types
    - The return array now contains a new "nullable_type" index set to true or false for each method param
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The getMethodParameters() method now supports closures
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added more guard code for JS files with syntax errors (request [#1271][sq-1271] and request [#1272][sq-1272])
- Added more guard code for CSS files with syntax errors (request [#1304][sq-1304])
- PEAR.Commenting.FunctionComment fixers now correctly handle multi-line param comments
- AbstractVariableSniff now supports anonymous classes
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.NamingConventions.ConstructorName and PEAR.NamingConventions.ValidVariable now support anonymous classes
- Generic.NamingConventions.CamelCapsFunctionName and PEAR.NamingConventions.ValidFunctionName now support anonymous classes
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.CodeAnalysis.UnusedFunctionParameter and PEAR.Functions.ValidDefaultValue now support closures
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- PEAR.NamingConventions.ValidClassName and Squiz.Classes.ValidClassName now support traits
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.Functions.FunctionCallArgumentSpacing now supports closures other PHP-provided functions
    - Thanks to [Algirdas Gurevicius][@uniquexor] for the patch
- Fixed an error where a nullable type character was detected as an inline then token
    - A new T_NULLABLE token has been added to represent the ? nullable type character
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- Squiz.WhiteSpace.SemicolonSpacing no longer removes comments while fixing the placement of semicolons
    - Thanks to [Algirdas Gurevicius][@uniquexor] for the patch

### Fixed
- Fixed bug [#1230][sq-1230] : JS tokeniser incorrectly tokenises bitwise shifts as comparison
    - Thanks to [Ryan McCue][@rmccue] for the patch
- Fixed bug [#1237][sq-1237] : Uninitialized string offset in PHP Tokenizer on PHP 5.2
- Fixed bug [#1239][sq-1239] : Warning when static method name is 'default'
- Fixed bug [#1240][sq-1240] : False positive for function names starting with triple underscore
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1245][sq-1245] : SELF is not recognised as T_SELF token in: return new self
- Fixed bug [#1246][sq-1246] : A mix of USE statements with and without braces can cause the tokenizer to mismatch brace tokens
    - Thanks to [Michał Bundyra][@michalbundyra] for the patch
- Fixed bug [#1249][sq-1249] : GitBlame report requires a .git directory
- Fixed bug [#1252][sq-1252] : Squiz.Strings.ConcatenationSpacing fix creates syntax error when joining a number to a string
- Fixed bug [#1253][sq-1253] : Generic.ControlStructures.InlineControlStructure fix creates syntax error fixing if-try/catch
- Fixed bug [#1255][sq-1255] : Inconsistent indentation check results when ELSE on new line
- Fixed bug [#1257][sq-1257] : Double dash in CSS class name can lead to "Named colours are forbidden" false positives
- Fixed bug [#1260][sq-1260] : Syntax errors not being shown when error_prepend_string is set
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Fixed bug [#1264][sq-1264] : Array return type hint is sometimes detected as T_ARRAY_HINT instead of T_RETURN_TYPE
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- Fixed bug [#1265][sq-1265] : ES6 arrow function raises unexpected operator spacing errors
- Fixed bug [#1267][sq-1267] : Fixer incorrectly handles filepaths with repeated dir names
    - Thanks to [Sergey Ovchinnikov][@orx0r] for the patch
- Fixed bug [#1276][sq-1276] : Commenting.FunctionComment.InvalidReturnVoid conditional issue with anonymous classes
- Fixed bug [#1277][sq-1277] : Squiz.PHP.DisallowMultipleAssignments.Found error when var assignment is on the same line as an open tag
- Fixed bug [#1284][sq-1284] : Squiz.Arrays.ArrayBracketSpacing.SpaceBeforeBracket false positive match for short list syntax

[sq-1230]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1230
[sq-1237]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1237
[sq-1239]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1239
[sq-1240]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1240
[sq-1245]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1245
[sq-1246]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1246
[sq-1249]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1249
[sq-1252]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1252
[sq-1253]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1253
[sq-1255]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1255
[sq-1257]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1257
[sq-1260]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1260
[sq-1264]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1264
[sq-1265]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1265
[sq-1267]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1267
[sq-1271]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1271
[sq-1272]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1272
[sq-1276]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1276
[sq-1277]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1277
[sq-1284]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1284
[sq-1304]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1304

## [2.7.1] - 2016-11-30

### Changed
- Squiz.ControlStructures.ControlSignature.SpaceAfterCloseParenthesis fix now removes unnecessary whitespace
- Squiz.Formatting.OperatorBracket no longer errors for negative array indexes used within a function call
- Squiz.PHP.EmbeddedPhp no longer expects a semicolon after statements that are only opening a scope
- Fixed a problem where the content of T_DOC_COMMENT_CLOSE_TAG tokens could sometimes be (boolean) false
- Developers of custom standards with custom test runners can now have their standards ignored by the built-in test runner
    - Set the value of an environment variable called PHPCS_IGNORE_TESTS with a comma separated list of your standard names
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- The unit test runner now loads the test sniff outside of the standard's ruleset so that exclude rules do not get applied
    - This may have caused problems when testing custom sniffs inside custom standards
    - Also makes the unit tests runs a little faster
- The SVN pre-commit hook now works correctly when installed via composer
    - Thanks to [Sergey][@sserbin] for the patch

### Fixed
- Fixed bug [#1135][sq-1135] : PEAR.ControlStructures.MultiLineCondition.CloseBracketNewLine not detected if preceded by multiline function call
- Fixed bug [#1138][sq-1138] : PEAR.ControlStructures.MultiLineCondition.Alignment not detected if closing brace is first token on line
- Fixed bug [#1141][sq-1141] : Sniffs that check EOF newlines don't detect newlines properly when the last token is a doc block
- Fixed bug [#1150][sq-1150] : Squiz.Strings.EchoedStrings does not properly fix bracketed statements
- Fixed bug [#1156][sq-1156] : Generic.Formatting.DisallowMultipleStatements errors when multiple short echo tags are used on the same line
    - Thanks to [Nikola Kovacs][@nkovacs] for the patch
- Fixed bug [#1161][sq-1161] : Absolute report path is treated like a relative path if it also exists within the current directory
- Fixed bug [#1170][sq-1170] : Javascript regular expression literal not recognized after comparison operator
- Fixed bug [#1180][sq-1180] : Class constant named FUNCTION is incorrectly tokenized
- Fixed bug [#1181][sq-1181] : Squiz.Operators.IncrementDecrementUsage.NoBrackets false positive when incrementing properties
    - Thanks to [Jürgen Henge-Ernst][@hernst42] for the patch
- Fixed bug [#1188][sq-1188] : Generic.WhiteSpace.ScopeIndent issues with inline HTML and multi-line function signatures
- Fixed bug [#1190][sq-1190] : phpcbf on if/else with trailing comment generates erroneous code
- Fixed bug [#1191][sq-1191] : Javascript sniffer fails with function called "Function"
- Fixed bug [#1203][sq-1203] : Inconsistent behavior of PHP_CodeSniffer_File::findEndOfStatement
- Fixed bug [#1218][sq-1218] : CASE conditions using class constants named NAMESPACE/INTERFACE/TRAIT etc are incorrectly tokenized
- Fixed bug [#1221][sq-1221] : Indented function call with multiple closure arguments can cause scope indent error
- Fixed bug [#1224][sq-1224] : PHPCBF fails to fix code with heredoc/nowdoc as first argument to a function

[sq-1135]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1135
[sq-1138]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1138
[sq-1141]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1141
[sq-1150]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1150
[sq-1156]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1156
[sq-1161]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1161
[sq-1170]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1170
[sq-1180]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1180
[sq-1181]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1181
[sq-1188]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1188
[sq-1190]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1190
[sq-1191]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1191
[sq-1203]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1203
[sq-1218]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1218
[sq-1221]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1221
[sq-1224]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1224

## [2.7.0] - 2016-09-02

### Changed
- Added --file-list command line argument to allow a list of files and directories to be specified in an external file
    - Useful if you have a generated list of files to check that would be too long for the command line
    - File and directory paths are listed one per line
    - Usage is: phpcs --file-list=/path/to/file-list ...
    - Thanks to [Blotzu][@andrei-propertyguru] for the patch
- Values set using @codingStandardsChangeSetting comments can now contain spaces
- Sniff unit tests can now specify a list of test files instead of letting the runner pick them (request [#1078][sq-1078])
    - Useful if a sniff needs to exclude files based on the environment, or is checking filenames
    - Override the new getTestFiles() method to specify your own list of test files
- Generic.Functions.OpeningFunctionBraceKernighanRitchie now ignores spacing for function return types
    - The sniff code Generic.Functions.OpeningFunctionBraceKernighanRitchie.SpaceAfterBracket has been removed
    - Replaced by Generic.Functions.OpeningFunctionBraceKernighanRitchie.SpaceBeforeBrace
    - The new error message is slightly clearer as it indicates that a single space is needed before the brace
- Squiz.Commenting.LongConditionClosingComment now allows for the length of a code block to be configured
    - Set the lineLimit property (default is 20) in your ruleset.xml file to set the code block length
    - When the code block length is reached, the sniff will enforce a closing comment after the closing brace
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.Commenting.LongConditionClosingComment now allows for the end comment format to be configured
    - Set the commentFormat property (default is "//end %s") in your ruleset.xml file to set the format
    - The placeholder %s will be replaced with the type of condition opener, e.g., "//end foreach"
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Generic.PHPForbiddenFunctions now allows forbidden functions to have mixed case
    - Previously, it would only do a strtolower comparison
    - Error message now shows what case was found in the code and what the correct case should be
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added Generic.Classes.OpeningBraceSameLine to ensure opening brace of class/interface/trait is on the same line as the declaration
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added Generic.PHP.BacktickOperator to ban the use of the backtick operator for running shell commands
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Added Generic.PHP.DisallowAlternativePHPTags to ban the use of alternate PHP tags
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Squiz.WhiteSpace.LanguageConstructSpacing no longer checks for spaces if parenthesis are being used (request [#1062][sq-1062])
    - Makes this sniff more compatible with those that check parenthesis spacing of function calls
- Squiz.WhiteSpace.ObjectOperatorSpacing now has a setting to ignore newline characters around object operators
    - Default remains FALSE, so newlines are not allowed
    - Override the "ignoreNewlines" setting in a ruleset.xml file to change
    - Thanks to [Alex Howansky][@AlexHowansky] for the patch
- Squiz.Scope.MethodScope now sniffs traits as well as classes and interfaces
    - Thanks to [Jesse Donat][@donatj] for the patch
- PHPCBF is now able to fix Squiz.SelfMemberReference.IncorrectCase errors
    - Thanks to [Nikola Kovacs][@nkovacs] for the patch
- PHPCBF is now able to fix Squiz.Commenting.VariableComment.IncorrectVarType
    - Thanks to [Walt Sorensen][@photodude] for the patch
- PHPCBF is now able to fix Generic.PHP.DisallowShortOpenTag
    - Thanks to [Juliette Reinders Folmer][@jrfnl] for the patch
- Improved the formatting of the end brace when auto fixing InlineControlStructure errors (request [#1121][sq-1121])
- Generic.Functions.OpeningFunctionBraceKernighanRitchie.BraceOnNewLine fix no longer leaves blank line after brace (request [#1085][sq-1085])
- Generic UpperCaseConstantNameSniff now allows lowercase namespaces in constant definitions
    - Thanks to [Daniel Schniepp][@dschniepp] for the patch
- Squiz DoubleQuoteUsageSniff is now more tolerant of syntax errors caused by mismatched string tokens
- A few sniffs that produce errors based on the current PHP version can now be told to run using a specific PHP version
    - Set the `php_version` config var using `--config-set`, `--runtime-set`, or in a ruleset to specify a specific PHP version
    - The format of the PHP version is the same as the `PHP_VERSION_ID` constant (e.g., 50403 for version 5.4.3)
    - Supported sniffs are Generic.PHP.DisallowAlternativePHPTags, PSR1.Classes.ClassDeclaration, Squiz.Commenting.FunctionComment
    - Thanks to [Finlay Beaton][@ofbeaton] for the patch

### Fixed
- Fixed bug [#985][sq-985] : Duplicate class definition detection generates false-positives in media queries
    - Thanks to [Raphael Horber][@rhorber] for the patch
- Fixed bug [#1014][sq-1014] : Squiz VariableCommentSniff doesn't always detect a missing comment
- Fixed bug [#1066][sq-1066] : Undefined index: quiet in `CLI.php` during unit test run with `-v` command line arg
- Fixed bug [#1072][sq-1072] : Squiz.SelfMemberReference.NotUsed not detected if leading namespace separator is used
- Fixed bug [#1089][sq-1089] : Rulesets cannot be loaded if the path contains urlencoded characters
- Fixed bug [#1091][sq-1091] : PEAR and Squiz FunctionComment sniffs throw errors for some invalid @param line formats
- Fixed bug [#1092][sq-1092] : PEAR.Functions.ValidDefaultValue should not flag type hinted methods with a NULL default argument
- Fixed bug [#1095][sq-1095] : Generic LineEndings sniff replaces tabs with spaces with --tab-width is set
- Fixed bug [#1096][sq-1096] : Squiz FunctionDeclarationArgumentSpacing gives incorrect error/fix when variadic operator is followed by a space
- Fixed bug [#1099][sq-1099] : Group use declarations are incorrectly fixed by the PSR2 standard
    - Thanks to [Jason McCreary][@jasonmccreary] for the patch
- Fixed bug [#1101][sq-1101] : Incorrect indent errors when breaking out of PHP inside an IF statement
- Fixed bug [#1102][sq-1102] : Squiz.Formatting.OperatorBracket.MissingBrackets faulty bracketing fix
- Fixed bug [#1109][sq-1109] : Wrong scope indent reported in anonymous class
- Fixed bug [#1112][sq-1112] : File docblock not recognized when require_once follows it
- Fixed bug [#1120][sq-1120] : InlineControlStructureSniff does not handle auto-fixing for control structures that make function calls
- Fixed bug [#1124][sq-1124] : Squiz.Operators.ComparisonOperatorUsage does not detect bracketed conditions for inline IF statements
    - Thanks to [Raphael Horber][@rhorber] for the patch

[sq-985]: https://github.com/squizlabs/PHP_CodeSniffer/issues/985
[sq-1014]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1014
[sq-1062]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1062
[sq-1066]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1066
[sq-1072]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1072
[sq-1078]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1078
[sq-1085]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1085
[sq-1089]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1089
[sq-1091]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1091
[sq-1092]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1092
[sq-1095]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1095
[sq-1096]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1096
[sq-1099]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1099
[sq-1101]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1101
[sq-1102]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1102
[sq-1109]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1109
[sq-1112]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1112
[sq-1120]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1120
[sq-1121]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1121
[sq-1124]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1124

## [2.6.2] - 2016-07-14

### Changed
- Added a new --exclude CLI argument to exclude a list of sniffs from checking and fixing (request [#904][sq-904])
    - Accepts the same sniff codes as the --sniffs command line argument, but provides the opposite functionality
- Added a new -q command line argument to disable progress and verbose information from being printed (request [#969][sq-969])
    - Useful if a coding standard hard-codes progress or verbose output but you want PHPCS to be quiet
    - Use the command "phpcs --config-set quiet true" to turn quiet mode on by default
- Generic LineLength sniff no longer errors for comments that cannot be broken out onto a new line (request [#766][sq-766])
    - A typical case is a comment that contains a very long URL
    - The comment is ignored if putting the URL on an indented new comment line would be longer than the allowed length
- Settings extensions in a ruleset no longer causes PHP notices during unit testing
    - Thanks to [Klaus Purer][@klausi] for the patch
- Version control reports now show which errors are fixable if you are showing sources
- Added a new sniff to enforce a single space after a NOT operator (request [#1051][sq-1051])
    - Include in a ruleset using the code Generic.Formatting.SpaceAfterNot
- The Squiz.Commenting.BlockComment sniff now supports tabs for indenting comment lines (request [#1056][sq-1056])

### Fixed
- Fixed bug [#790][sq-790] : Incorrect missing @throws error in methods that use closures
- Fixed bug [#908][sq-908] : PSR2 standard is not checking that closing brace is on line following the body
- Fixed bug [#945][sq-945] : Incorrect indent behavior using deep-nested function and arrays
- Fixed bug [#961][sq-961] : Two anonymous functions passed as function/method arguments cause indentation false positive
- Fixed bug [#1005][sq-1005] : Using global composer vendor autoload breaks PHP lowercase built-in function sniff
    - Thanks to [Michael Butler][@michaelbutler] for the patch
- Fixed bug [#1007][sq-1007] : Squiz Unreachable code detection is not working properly with a closure inside a case
- Fixed bug [#1023][sq-1023] : PSR2.Classes.ClassDeclaration fails if class extends base class and "implements" is on trailing line
- Fixed bug [#1026][sq-1026] : Arrays in comma delimited class properties cause ScopeIndent to increase indent
- Fixed bug [#1028][sq-1028] : Squiz ArrayDeclaration incorrectly fixes multi-line array where end bracket is not on a new line
- Fixed bug [#1034][sq-1034] : Squiz FunctionDeclarationArgumentSpacing gives incorrect error when first arg is a variadic
- Fixed bug [#1036][sq-1036] : Adjacent assignments aligned analysis statement wrong
- Fixed bug [#1049][sq-1049] : Version control reports can show notices when the report width is very small
- Fixed bug [#21050][pear-21050] : PEAR MultiLineCondition sniff suppresses errors on last condition line

[sq-766]: https://github.com/squizlabs/PHP_CodeSniffer/issues/766
[sq-790]: https://github.com/squizlabs/PHP_CodeSniffer/issues/790
[sq-904]: https://github.com/squizlabs/PHP_CodeSniffer/issues/904
[sq-908]: https://github.com/squizlabs/PHP_CodeSniffer/issues/908
[sq-945]: https://github.com/squizlabs/PHP_CodeSniffer/issues/945
[sq-961]: https://github.com/squizlabs/PHP_CodeSniffer/issues/961
[sq-969]: https://github.com/squizlabs/PHP_CodeSniffer/issues/969
[sq-1005]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1005
[sq-1007]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1007
[sq-1023]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1023
[sq-1026]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1026
[sq-1028]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1028
[sq-1034]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1034
[sq-1036]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1036
[sq-1049]: https://github.com/squizlabs/PHP_CodeSniffer/pull/1049
[sq-1051]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1051
[sq-1056]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1056
[pear-21050]: https://pear.php.net/bugs/bug.php?id=21050

## [2.6.1] - 2016-05-31

### Changed
- The PHP-supplied T_COALESCE token has been replicated for PHP versions before 7.0
- Function return types of self, parent and callable are now tokenized as T_RETURN_TYPE
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- The default_standard config setting now allows multiple standards to be listed, like on the command line
    - Thanks to [Michael Mayer][@schnittstabil] for the patch
- Installations done via composer now only include the composer autoloader for PHP 5.3.2+ (request [#942][sq-942])
- Added a rollbackChangeset() method to the Fixer class to purposely rollback the active changeset

### Fixed
- Fixed bug [#940][sq-940] : Auto-fixing issue encountered with inconsistent use of braces
- Fixed bug [#943][sq-943] : Squiz.PHP.InnerFunctions.NotAllowed reported in anonymous classes
- Fixed bug [#944][sq-944] : PHP warning when running the latest phar
- Fixed bug [#951][sq-951] : InlineIfDeclaration: invalid error produced with UTF-8 string
- Fixed bug [#957][sq-957] : Operator spacing sniff errors when plus is used as part of a number
    - Thanks to [Klaus Purer][@klausi] for the patch
- Fixed bug [#959][sq-959] : Call-time pass-by-reference false positive if there is a square bracket before the ampersand
    - Thanks to [Konstantin Leboev][@realmfoo] for the patch
- Fixed bug [#962][sq-962] : Null coalescing operator (??) not detected as a token
    - Thanks to [Joel Posti][@joelposti] for the patch
- Fixed bug [#973][sq-973] : Anonymous class declaration and PSR1.Files.SideEffects.FoundWithSymbols
- Fixed bug [#974][sq-974] : Error when file ends with "function"
- Fixed bug [#979][sq-979] : Anonymous function with return type hint is not refactored as expected
- Fixed bug [#983][sq-983] : Squiz.WhiteSpace.MemberVarSpacing.AfterComment fails to fix error when comment is not a docblock
- Fixed bug [#1010][sq-1010] : Squiz NonExecutableCode sniff does not detect boolean OR
    - Thanks to [Derek Henderson][@2shediac] for the patch
- Fixed bug [#1015][sq-1015] : The Squiz.Commenting.FunctionComment sniff doesn't allow description in @return tag
    - Thanks to [Alexander Obuhovich][@aik099] for the patch
- Fixed bug [#1022][sq-1022] : Duplicate spaces after opening bracket error with PSR2 standard
- Fixed bug [#1025][sq-1025] : Syntax error in JS file can cause undefined index for parenthesis_closer

[sq-940]: https://github.com/squizlabs/PHP_CodeSniffer/issues/940
[sq-942]: https://github.com/squizlabs/PHP_CodeSniffer/issues/942
[sq-943]: https://github.com/squizlabs/PHP_CodeSniffer/issues/943
[sq-944]: https://github.com/squizlabs/PHP_CodeSniffer/issues/944
[sq-951]: https://github.com/squizlabs/PHP_CodeSniffer/issues/951
[sq-957]: https://github.com/squizlabs/PHP_CodeSniffer/pull/957
[sq-959]: https://github.com/squizlabs/PHP_CodeSniffer/issues/959
[sq-962]: https://github.com/squizlabs/PHP_CodeSniffer/issues/962
[sq-973]: https://github.com/squizlabs/PHP_CodeSniffer/issues/973
[sq-974]: https://github.com/squizlabs/PHP_CodeSniffer/issues/974
[sq-979]: https://github.com/squizlabs/PHP_CodeSniffer/issues/979
[sq-983]: https://github.com/squizlabs/PHP_CodeSniffer/issues/983
[sq-1010]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1010
[sq-1015]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1015
[sq-1022]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1022
[sq-1025]: https://github.com/squizlabs/PHP_CodeSniffer/issues/1025

## [2.6.0] - 2016-04-04

### Changed
- Paths used when setting CLI arguments inside ruleset.xml files are now relative to the ruleset location (request [#847][sq-847])
    - This change only applies to paths within ARG tags, used to set CLI arguments
    - Previously, the paths were relative to the directory PHPCS was being run from
    - Absolute paths are still allowed and work the same way they always have
    - This change allows ruleset.xml files to be more portable
- Content passed via STDIN will now be processed even if files are specified on the command line or in a ruleset
- When passing content via STDIN, you can now specify the file path to use on the command line (request [#934][sq-934])
    - This allows sniffs that check file paths to work correctly
    - This is the same functionality provided by the phpcs_input_file line, except it is available on the command line
- Files processed with custom tokenizers will no longer be skipped if they appear minified (request [#877][sq-877])
    - If the custom tokenizer wants minified files skipped, it can set a $skipMinified member var to TRUE
    - See the included JS and CSS tokenizers for an example
- Config vars set in ruleset.xml files are now processed earlier, allowing them to be used during sniff registration
    - Among other things, this allows the installed_paths config var to be set in ruleset.xml files
    - Thanks to [Pieter Frenssen][@pfrenssen] for the patch
- Improved detection of regular expressions in the JS tokenizer
- Generic PHP Syntax sniff now uses PHP_BINARY (if available) to determine the path to PHP if no other path is available
    - You can still manually set `php_path` to use a specific binary for testing
    - Thanks to [Andrew Berry][@deviantintegral] for the patch
- The PHP-supplied T_POW_EQUAL token has been replicated for PHP versions before 5.6
- Added support for PHP7 use group declarations (request [#878][sq-878])
    - New tokens T_OPEN_USE_GROUP and T_CLOSE_USE_GROUP are assigned to the open and close curly braces
- Generic ScopeIndent sniff now reports errors for every line that needs the indent changed (request [#903][sq-903])
    - Previously, it ignored lines that were indented correctly in the context of their block
    - This change produces more technically accurate error messages, but is much more verbose
- The PSR2 and Squiz standards now allow multi-line default values in function declarations (request [#542][sq-542])
    - Previously, these would automatically make the function a multi-line declaration
- Squiz InlineCommentSniff now allows docblocks on require(_once) and include(_once) statements
    - Thanks to [Gary Jones][@GaryJones] for the patch
- Squiz and PEAR Class and File sniffs no longer assume the first comment in a file is always a file comment
    - phpDocumentor assigns the comment to the file only if it is not followed by a structural element
    - These sniffs now follow this same rule
- Squiz ClassCommentSniff no longer checks for blank lines before class comments
    - Removes the error Squiz.Commenting.ClassComment.SpaceBefore
- Renamed Squiz.CSS.Opacity.SpacingAfterPoint to Squiz.CSS.Opacity.DecimalPrecision
    - Please update your ruleset if you are referencing this error code directly
- Fixed PHP tokenizer problem that caused an infinite loop when checking a comment with specific content
- Generic Disallow Space and Tab indent sniffs now detect and fix indents inside embedded HTML chunks (request [#882][sq-882])
- Squiz CSS IndentationSniff no longer assumes the class opening brace is at the end of a line
- Squiz FunctionCommentThrowTagSniff now ignores non-docblock comments
- Squiz ComparisonOperatorUsageSniff now allows conditions like while(true)
- PEAR FunctionCallSignatureSniff (and the Squiz and PSR2 sniffs that use it) now correctly check the first argument
    - Further fix for bug [#698][sq-698]

### Fixed
- Fixed bug [#791][sq-791] : codingStandardsChangeSetting settings not working with namespaces
- Fixed bug [#872][sq-872] : Incorrect detection of blank lines between CSS class names
- Fixed bug [#879][sq-879] : Generic InlineControlStructureSniff can create parse error when case/if/elseif/else have mixed brace and braceless definitions
- Fixed bug [#883][sq-883] : PSR2 is not checking for blank lines at the start and end of control structures
- Fixed bug [#884][sq-884] : Incorrect indentation notice for anonymous classes
- Fixed bug [#887][sq-887] : Using curly braces for a shared CASE/DEFAULT statement can generate an error in PSR2 SwitchDeclaration
- Fixed bug [#889][sq-889] : Closure inside catch/else/elseif causes indentation error
- Fixed bug [#890][sq-890] : Function call inside returned short array value can cause indentation error inside CASE statements
- Fixed bug [#897][sq-897] : Generic.Functions.CallTimePassByReference.NotAllowed false positive when short array syntax
- Fixed bug [#900][sq-900] : Squiz.Functions.FunctionDeclarationArgumentSpacing bug when no space between type hint and argument
- Fixed bug [#902][sq-902] : T_OR_EQUAL and T_POW_EQUAL are not seen as assignment tokens
- Fixed bug [#910][sq-910] : Unrecognized "extends" and indentation on anonymous classes
- Fixed bug [#915][sq-915] : JS Tokenizer generates errors when processing some decimals
- Fixed bug [#928][sq-928] : Endless loop when sniffing a PHP file with a git merge conflict inside a function
- Fixed bug [#937][sq-937] : Shebang can cause PSR1 SideEffects warning
    - Thanks to [Clay Loveless][@claylo] for the patch
- Fixed bug [#938][sq-938] : CallTimePassByReferenceSniff ignores functions with return value

[sq-542]: https://github.com/squizlabs/PHP_CodeSniffer/issues/542
[sq-791]: https://github.com/squizlabs/PHP_CodeSniffer/issues/791
[sq-847]: https://github.com/squizlabs/PHP_CodeSniffer/issues/847
[sq-872]: https://github.com/squizlabs/PHP_CodeSniffer/issues/872
[sq-877]: https://github.com/squizlabs/PHP_CodeSniffer/issues/877
[sq-878]: https://github.com/squizlabs/PHP_CodeSniffer/issues/878
[sq-879]: https://github.com/squizlabs/PHP_CodeSniffer/issues/879
[sq-882]: https://github.com/squizlabs/PHP_CodeSniffer/issues/882
[sq-883]: https://github.com/squizlabs/PHP_CodeSniffer/issues/883
[sq-884]: https://github.com/squizlabs/PHP_CodeSniffer/issues/884
[sq-887]: https://github.com/squizlabs/PHP_CodeSniffer/issues/887
[sq-889]: https://github.com/squizlabs/PHP_CodeSniffer/issues/889
[sq-890]: https://github.com/squizlabs/PHP_CodeSniffer/issues/890
[sq-897]: https://github.com/squizlabs/PHP_CodeSniffer/issues/897
[sq-900]: https://github.com/squizlabs/PHP_CodeSniffer/issues/900
[sq-902]: https://github.com/squizlabs/PHP_CodeSniffer/issues/902
[sq-903]: https://github.com/squizlabs/PHP_CodeSniffer/issues/903
[sq-910]: https://github.com/squizlabs/PHP_CodeSniffer/issues/910
[sq-915]: https://github.com/squizlabs/PHP_CodeSniffer/issues/915
[sq-928]: https://github.com/squizlabs/PHP_CodeSniffer/issues/928
[sq-934]: https://github.com/squizlabs/PHP_CodeSniffer/issues/934
[sq-937]: https://github.com/squizlabs/PHP_CodeSniffer/pull/937
[sq-938]: https://github.com/squizlabs/PHP_CodeSniffer/issues/938

## [2.5.1] - 2016-01-20

### Changed
- The PHP-supplied T_SPACESHIP token has been replicated for PHP versions before 7.0
- T_SPACESHIP is now correctly identified as an operator
    - Thanks to [Alexander Obuhovich][@aik099] for the patch
- Generic LowerCaseKeyword now ensures array type hints are lowercase as well
    - Thanks to [Mathieu Rochette][@mathroc] for the patch
- Squiz ComparisonOperatorUsageSniff no longer hangs on JS FOR loops that don't use semicolons
- PHP_CodesSniffer now includes the composer `autoload.php` file, if there is one
    - Thanks to [Klaus Purer][@klausi] for the patch
- Added error Squiz.Commenting.FunctionComment.ScalarTypeHintMissing for PHP7 only (request [#858][sq-858])
    - These errors were previously reported as Squiz.Commenting.FunctionComment.TypeHintMissing on PHP7
    - Disable this error message in a ruleset.xml file if your code needs to run on both PHP5 and PHP7
- The PHP 5.6 __debugInfo magic method no longer produces naming convention errors
    - Thanks to [Michael Nowack][@syranez] for the patch
- PEAR and Squiz FunctionComment sniffs now support variadic functions (request [#841][sq-841])

### Fixed
- Fixed bug [#622][sq-622] : Wrong detection of Squiz.CSS.DuplicateStyleDefinition with media queries
- Fixed bug [#752][sq-752] : The missing exception error is reported in first found DocBlock
- Fixed bug [#794][sq-794] : PSR2 MultiLineFunctionDeclaration forbids comments after opening parenthesis of a multiline call
- Fixed bug [#820][sq-820] : PEAR/PSR2 FunctionCallSignature sniffs suggest wrong indent when there are multiple arguments on a line
- Fixed bug [#822][sq-822] : Ruleset hard-coded file paths are not used if not running from the same directory as the ruleset
- Fixed bug [#825][sq-825] : FunctionCallArgumentSpacing sniff complains about more than one space before comment in multi-line function call
- Fixed bug [#828][sq-828] : Null classname is tokenized as T_NULL instead of T_STRING
- Fixed bug [#829][sq-829] : Short array argument not fixed correctly when multiple function arguments are on the same line
- Fixed bug [#831][sq-831] : PHPCS freezes in an infinite loop under Windows if no standard is passed
- Fixed bug [#832][sq-832] : Tokenizer does not support context sensitive parsing
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- Fixed bug [#835][sq-835] : PEAR.Functions.FunctionCallSignature broken when closure uses return types
- Fixed bug [#838][sq-838] : CSS indentation fixer changes color codes
    - Thanks to [Klaus Purer][@klausi] for the patch
- Fixed bug [#839][sq-839] : "__()" method is marked as not camel caps
    - Thanks to [Tim Bezhashvyly][@tim-bezhashvyly] for the patch
- Fixed bug [#852][sq-852] : Generic.Commenting.DocComment not finding errors when long description is omitted
- Fixed bug [#854][sq-854] : Return typehints in interfaces are not reported as T_RETURN_TYPE
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- Fixed bug [#855][sq-855] : Capital letter detection for multibyte strings doesn't work correctly
- Fixed bug [#857][sq-857] : PSR2.ControlStructure.SwitchDeclaration shouldn't check indent of curly brace closers
- Fixed bug [#859][sq-859] : Switch statement indention issue when returning function call with closure
- Fixed bug [#861][sq-861] : Single-line arrays and function calls can generate incorrect indentation errors
- Fixed bug [#867][sq-867] : Squiz.Strings.DoubleQuoteUsage broken for some escape codes
    - Thanks to [Jack Blower][@ElvenSpellmaker] for the help with the fix
- Fixed bug [#21005][pear-21005] : Incorrect indent detection when multiple properties are initialized to arrays
- Fixed bug [#21010][pear-21010] : Incorrect missing colon detection in CSS when first style is not on new line
- Fixed bug [#21011][pear-21011] : Incorrect error message text when newline found after opening brace

[sq-622]: https://github.com/squizlabs/PHP_CodeSniffer/issues/622
[sq-752]: https://github.com/squizlabs/PHP_CodeSniffer/issues/752
[sq-794]: https://github.com/squizlabs/PHP_CodeSniffer/issues/794
[sq-820]: https://github.com/squizlabs/PHP_CodeSniffer/issues/820
[sq-822]: https://github.com/squizlabs/PHP_CodeSniffer/issues/822
[sq-825]: https://github.com/squizlabs/PHP_CodeSniffer/issues/825
[sq-828]: https://github.com/squizlabs/PHP_CodeSniffer/issues/828
[sq-829]: https://github.com/squizlabs/PHP_CodeSniffer/issues/829
[sq-831]: https://github.com/squizlabs/PHP_CodeSniffer/issues/831
[sq-832]: https://github.com/squizlabs/PHP_CodeSniffer/issues/832
[sq-835]: https://github.com/squizlabs/PHP_CodeSniffer/issues/835
[sq-838]: https://github.com/squizlabs/PHP_CodeSniffer/pull/838
[sq-839]: https://github.com/squizlabs/PHP_CodeSniffer/issues/839
[sq-841]: https://github.com/squizlabs/PHP_CodeSniffer/issues/841
[sq-852]: https://github.com/squizlabs/PHP_CodeSniffer/issues/852
[sq-854]: https://github.com/squizlabs/PHP_CodeSniffer/issues/854
[sq-855]: https://github.com/squizlabs/PHP_CodeSniffer/pull/855
[sq-857]: https://github.com/squizlabs/PHP_CodeSniffer/issues/857
[sq-858]: https://github.com/squizlabs/PHP_CodeSniffer/issues/858
[sq-859]: https://github.com/squizlabs/PHP_CodeSniffer/issues/859
[sq-861]: https://github.com/squizlabs/PHP_CodeSniffer/issues/861
[sq-867]: https://github.com/squizlabs/PHP_CodeSniffer/issues/867
[pear-21005]: https://pear.php.net/bugs/bug.php?id=21005
[pear-21010]: https://pear.php.net/bugs/bug.php?id=21010
[pear-21011]: https://pear.php.net/bugs/bug.php?id=21011

## [2.5.0] - 2015-12-11

### Changed
- PHPCS will now look for a phpcs.xml file in parent directories as well as the current directory (request [#626][sq-626])
- PHPCS will now use a phpcs.xml file even if files are specified on the command line
    - This file is still only used if no standard is specified on the command line
- Added support for a phpcs.xml.dist file (request [#583][sq-583])
    - If both a phpcs.xml and phpcs.xml.dist file are present, the phpcs.xml file will be used
- Added support for setting PHP ini values in ruleset.xml files (request [#560][sq-560])
    - Setting the value of the new ini tags to name="memory_limit" value="32M" is the same as -d memory_limit=32M
- Added support for one or more bootstrap files to be run before processing begins
    - Use the --bootstrap=file,file,file command line argument to include bootstrap files
    - Useful if you want to override some of the high-level settings of PHPCS or PHPCBF
    - Thanks to [John Maguire][@johnmaguire] for the patch
- Added additional verbose output for CSS tokenizing
- Squiz ComparisonOperatorUsageSniff now checks FOR, WHILE and DO-WHILE statements
    - Thanks to [Arnout Boks][@aboks] for the patch

### Fixed
- Fixed bug [#660][sq-660] : Syntax checks can fail on Windows with PHP5.6
- Fixed bug [#784][sq-784] : $this->trait is seen as a T_TRAIT token
- Fixed bug [#786][sq-786] : Switch indent issue with short array notation
- Fixed bug [#787][sq-787] : SpacingAfterDefaultBreak confused by multi-line statements
- Fixed bug [#797][sq-797] : Parsing CSS url() value breaks further parsing
- Fixed bug [#805][sq-805] : Squiz.Commenting.FunctionComment.InvalidTypeHint on Scalar types on PHP7
- Fixed bug [#807][sq-807] : Cannot fix line endings when open PHP tag is not on the first line
- Fixed bug [#808][sq-808] : JS tokenizer incorrectly setting some function and class names to control structure tokens
- Fixed bug [#809][sq-809] : PHPCBF can break a require_once statement with a space before the open parenthesis
- Fixed bug [#813][sq-813] : PEAR FunctionCallSignature checks wrong indent when first token on line is part of a multi-line string

[sq-560]: https://github.com/squizlabs/PHP_CodeSniffer/issues/560
[sq-583]: https://github.com/squizlabs/PHP_CodeSniffer/issues/583
[sq-626]: https://github.com/squizlabs/PHP_CodeSniffer/issues/626
[sq-660]: https://github.com/squizlabs/PHP_CodeSniffer/pull/660
[sq-784]: https://github.com/squizlabs/PHP_CodeSniffer/issues/784
[sq-786]: https://github.com/squizlabs/PHP_CodeSniffer/issues/786
[sq-787]: https://github.com/squizlabs/PHP_CodeSniffer/issues/787
[sq-797]: https://github.com/squizlabs/PHP_CodeSniffer/issues/797
[sq-805]: https://github.com/squizlabs/PHP_CodeSniffer/issues/805
[sq-807]: https://github.com/squizlabs/PHP_CodeSniffer/issues/807
[sq-808]: https://github.com/squizlabs/PHP_CodeSniffer/issues/808
[sq-809]: https://github.com/squizlabs/PHP_CodeSniffer/issues/809
[sq-813]: https://github.com/squizlabs/PHP_CodeSniffer/issues/813

## [2.4.0] - 2015-11-24

### Changed
- Added support for PHP 7 anonymous classes
    - Anonymous classes are now tokenized as T_ANON_CLASS and ignored by normal class sniffs
- Added support for PHP 7 function return type declarations
    - Return types are now tokenized as T_RETURN_TYPE
- Fixed tokenizing of the XOR operator, which was incorrectly identified as a power operator (bug [#765][sq-765])
    - The T_POWER token has been removed and replaced by the T_BITWISE_XOR token
    - The PHP-supplied T_POW token has been replicated for PHP versions before 5.6
- Traits are now tokenized in PHP versions before 5.4 to make testing easier
- Improved regular expression detection in JS files
- PEAR FunctionCallSignatureSniff now properly detects indents in more mixed HTML/PHP code blocks
- Full report now properly indents lines when newlines are found inside error messages
- Generating documentation without specifying a standard now uses the default standard instead
    - Thanks to [Ken Guest][@kenguest] for the patch
- Generic InlineControlStructureSniff now supports braceless do/while loops in JS
    - Thanks to [Pieter Frenssen][@pfrenssen] for the patch
- Added more guard code for function declarations with syntax errors
    - Thanks to Yun Young-jin for the patch
- Added more guard code for foreach declarations with syntax errors
    - Thanks to [Johan de Ruijter][@johanderuijter] for the patch
- Added more guard code for class declarations with syntax errors
- Squiz ArrayDeclarationSniff now has guard code for arrays with syntax errors
- Generic InlineControlStructureSniff now correctly fixes ELSEIF statements

### Fixed
- Fixed bug [#601][sq-601] : Expected type hint int[]; found array in Squiz FunctionCommentSniff
    - Thanks to [Scato Eggen][@scato] for the patch
- Fixed bug [#625][sq-625] : Consider working around T_HASHBANG in HHVM 3.5.x and 3.6.x
    - Thanks to [Kunal Mehta][@legoktm] for the patch
- Fixed bug [#692][sq-692] : Comment tokenizer can break when using mbstring function overloading
- Fixed bug [#694][sq-694] : Long sniff codes can cause PHP warnings in source report when showing error codes
- Fixed bug [#698][sq-698] : PSR2.Methods.FunctionCallSignature.Indent forces exact indent of ternary operator parameters
- Fixed bug [#704][sq-704] : ScopeIndent can fail when an opening parenthesis is on a line by itself
- Fixed bug [#707][sq-707] : Squiz MethodScopeSniff doesn't handle nested functions
- Fixed bug [#709][sq-709] : Squiz.Sniffs.Whitespace.ScopeClosingBraceSniff marking indented endif in mixed inline HTML blocks
- Fixed bug [#711][sq-711] : Sniffing from STDIN shows Generic.Files.LowercasedFilename.NotFound error
- Fixed bug [#714][sq-714] : Fixes suppression of errors using docblocks
    - Thanks to [Andrzej Karmazyn][@akarmazyn] for the patch
- Fixed bug [#716][sq-716] : JSON report is invalid when messages contain newlines or tabs
    - Thanks to [Pieter Frenssen][@pfrenssen] for the patch
- Fixed bug [#723][sq-723] : ScopeIndent can fail when multiple array closers are on the same line
- Fixed bug [#730][sq-730] : ScopeIndent can fail when a short array opening square bracket is on a line by itself
- Fixed bug [#732][sq-732] : PHP Notice if @package name is made up of all invalid characters
    - Adds new error code PEAR.Commenting.FileComment.InvalidPackageValue
- Fixed bug [#748][sq-748] : Auto fix for Squiz.Commenting.BlockComment.WrongEnd is incorrect
    - Thanks to [J.D. Grimes][@JDGrimes] for the patch
- Fixed bug [#753][sq-753] : PSR2 standard shouldn't require space after USE block when next code is a closing tag
- Fixed bug [#768][sq-768] : PEAR FunctionCallSignature sniff forbids comments after opening parenthesis of a multiline call
- Fixed bug [#769][sq-769] : Incorrect detection of variable reference operator when used with short array syntax
    - Thanks to [Klaus Purer][@klausi] for the patch
- Fixed bug [#772][sq-772] : Syntax error when using PHPCBF on alternative style foreach loops
- Fixed bug [#773][sq-773] : Syntax error when stripping trailing PHP close tag and previous statement has no semicolon
- Fixed bug [#778][sq-778] : PHPCBF creates invalid PHP for inline FOREACH containing multiple control structures
- Fixed bug [#781][sq-781] : Incorrect checking for PHP7 return types on multi-line function declarations
- Fixed bug [#782][sq-782] : Conditional function declarations cause fixing conflicts in Squiz standard
    - Squiz.ControlStructures.ControlSignature no longer enforces a single newline after open brace
    - Squiz.WhiteSpace.ControlStructureSpacing can be used to check spacing at the start/end of control structures

[sq-601]: https://github.com/squizlabs/PHP_CodeSniffer/issues/601
[sq-625]: https://github.com/squizlabs/PHP_CodeSniffer/issues/625
[sq-692]: https://github.com/squizlabs/PHP_CodeSniffer/pull/692
[sq-694]: https://github.com/squizlabs/PHP_CodeSniffer/issues/694
[sq-698]: https://github.com/squizlabs/PHP_CodeSniffer/issues/698
[sq-704]: https://github.com/squizlabs/PHP_CodeSniffer/issues/704
[sq-707]: https://github.com/squizlabs/PHP_CodeSniffer/pull/707
[sq-709]: https://github.com/squizlabs/PHP_CodeSniffer/issues/709
[sq-711]: https://github.com/squizlabs/PHP_CodeSniffer/issues/711
[sq-714]: https://github.com/squizlabs/PHP_CodeSniffer/pull/714
[sq-716]: https://github.com/squizlabs/PHP_CodeSniffer/pull/716
[sq-723]: https://github.com/squizlabs/PHP_CodeSniffer/issues/723
[sq-730]: https://github.com/squizlabs/PHP_CodeSniffer/pull/730
[sq-732]: https://github.com/squizlabs/PHP_CodeSniffer/pull/732
[sq-748]: https://github.com/squizlabs/PHP_CodeSniffer/pull/748
[sq-753]: https://github.com/squizlabs/PHP_CodeSniffer/issues/753
[sq-765]: https://github.com/squizlabs/PHP_CodeSniffer/issues/765
[sq-768]: https://github.com/squizlabs/PHP_CodeSniffer/issues/768
[sq-769]: https://github.com/squizlabs/PHP_CodeSniffer/pull/769
[sq-772]: https://github.com/squizlabs/PHP_CodeSniffer/issues/772
[sq-773]: https://github.com/squizlabs/PHP_CodeSniffer/issues/773
[sq-778]: https://github.com/squizlabs/PHP_CodeSniffer/issues/778
[sq-781]: https://github.com/squizlabs/PHP_CodeSniffer/issues/781
[sq-782]: https://github.com/squizlabs/PHP_CodeSniffer/issues/782

## [2.3.4] - 2015-09-09

### Changed
- JSON report format now includes the fixable status for each error message and the total number of fixable errors
- Added more guard code for function declarations with syntax errors
- Added tokenizer support for the PHP declare construct
    - Thanks to [Andy Blyler][@ablyler] for the patch
- Generic UnnecessaryStringConcatSniff can now allow strings concatenated over multiple lines
    - Set the allowMultiline property to TRUE (default is FALSE) in your ruleset.xml file to enable this
    - By default, concat used only for getting around line length limits still generates an error
    - Thanks to [Stefan Lenselink][@stefanlenselink] for the contribution
- Invalid byte sequences no longer throw iconv_strlen() errors (request [#639][sq-639])
    - Thanks to [Willem Stuursma][@willemstuursma] for the patch
- Generic TodoSniff and FixmeSniff are now better at processing strings with invalid characters
- PEAR FunctionCallSignatureSniff now ignores indentation of inline HTML content
- Squiz ControlSignatureSniff now supports control structures with only inline HTML content

### Fixed
- Fixed bug [#636][sq-636] : Some class names cause CSS tokenizer to hang
- Fixed bug [#638][sq-638] : VCS blame reports output error content from the blame commands for files not under VC
- Fixed bug [#642][sq-642] : Method params incorrectly detected when default value uses short array syntax
    - Thanks to [Josh Davis][@joshdavis11] for the patch
- Fixed bug [#644][sq-644] : PEAR ScopeClosingBrace sniff does not work with mixed HTML/PHP
- Fixed bug [#645][sq-645] : FunctionSignature and ScopeIndent sniffs don't detect indents correctly when PHP open tag is not on a line by itself
- Fixed bug [#648][sq-648] : Namespace not tokenized correctly when followed by multiple use statements
- Fixed bug [#654][sq-654] : Comments affect indent check for BSDAllman brace style
- Fixed bug [#658][sq-658] : Squiz.Functions.FunctionDeclarationSpacing error for multi-line declarations with required spaces greater than zero
    - Thanks to [J.D. Grimes][@JDGrimes] for the patch
- Fixed bug [#663][sq-663] : No space after class name generates: Class name "" is not in camel caps format
- Fixed bug [#667][sq-667] : Scope indent check can go into infinite loop due to some parse errors
- Fixed bug [#670][sq-670] : Endless loop in PSR1 SideEffects sniffer if no semicolon after last statement
    - Thanks to [Thomas Jarosch][@thomasjfox] for the patch
- Fixed bug [#672][sq-672] : Call-time pass-by-reference false positive
- Fixed bug [#683][sq-683] : Comments are incorrectly reported by PSR2.ControlStructures.SwitchDeclaration sniff
- Fixed bug [#687][sq-687] : ScopeIndent does not check indent correctly for method prefixes like public and abstract
- Fixed bug [#689][sq-689] : False error on some comments after class closing brace

[sq-636]: https://github.com/squizlabs/PHP_CodeSniffer/issues/636
[sq-638]: https://github.com/squizlabs/PHP_CodeSniffer/issues/638
[sq-639]: https://github.com/squizlabs/PHP_CodeSniffer/pull/639
[sq-642]: https://github.com/squizlabs/PHP_CodeSniffer/pull/642
[sq-644]: https://github.com/squizlabs/PHP_CodeSniffer/issues/644
[sq-645]: https://github.com/squizlabs/PHP_CodeSniffer/issues/645
[sq-648]: https://github.com/squizlabs/PHP_CodeSniffer/issues/648
[sq-654]: https://github.com/squizlabs/PHP_CodeSniffer/issues/654
[sq-658]: https://github.com/squizlabs/PHP_CodeSniffer/pull/658
[sq-663]: https://github.com/squizlabs/PHP_CodeSniffer/issues/663
[sq-667]: https://github.com/squizlabs/PHP_CodeSniffer/issues/667
[sq-670]: https://github.com/squizlabs/PHP_CodeSniffer/pull/670
[sq-672]: https://github.com/squizlabs/PHP_CodeSniffer/issues/672
[sq-683]: https://github.com/squizlabs/PHP_CodeSniffer/issues/683
[sq-687]: https://github.com/squizlabs/PHP_CodeSniffer/issues/687
[sq-689]: https://github.com/squizlabs/PHP_CodeSniffer/issues/689

## [2.3.3] - 2015-06-24

### Changed
- Improved the performance of the CSS tokenizer, especially on very large CSS files (thousands of lines)
    - Thanks to [Klaus Purer][@klausi] for the patch
- Defined tokens for lower PHP versions are now phpcs-specific strings instead of ints
    - Stops conflict with other projects, like PHP_CodeCoverage
- Added more guard code for syntax errors to various sniffs
- Improved support for older HHVM versions
    - Thanks to [Kunal Mehta][@legoktm] for the patch
- Squiz ValidLogicalOperatorsSniff now ignores XOR as type casting is different when using the ^ operator (request [#567][sq-567])
- Squiz CommentedOutCodeSniff is now better at ignoring URLs inside comments
- Squiz ControlSignatureSniff is now better at checking embedded PHP code
- Squiz ScopeClosingBraceSniff is now better at checking embedded PHP code

### Fixed
- Fixed bug [#584][sq-584] : Squiz.Arrays.ArrayDeclaration sniff gives incorrect NoComma error for multiline string values
- Fixed bug [#589][sq-589] : PEAR.Functions.FunctionCallSignature sniff not checking all function calls
- Fixed bug [#592][sq-592] : USE statement tokenizing can sometimes result in mismatched scopes
- Fixed bug [#594][sq-594] : Tokenizer issue on closure that returns by reference
- Fixed bug [#595][sq-595] : Colons in CSS selectors within media queries throw false positives
    - Thanks to [Klaus Purer][@klausi] for the patch
- Fixed bug [#598][sq-598] : PHPCBF can break function/use closure brace placement
- Fixed bug [#603][sq-603] : Squiz ControlSignatureSniff hard-codes opener type while fixing
- Fixed bug [#605][sq-605] : Auto report-width specified in ruleset.xml ignored
- Fixed bug [#611][sq-611] : Invalid numeric literal on CSS files under PHP7
- Fixed bug [#612][sq-612] : Multi-file diff generating incorrectly if files do not end with EOL char
- Fixed bug [#615][sq-615] : Squiz OperatorBracketSniff incorrectly reports and fixes operations using self::
- Fixed bug [#616][sq-616] : Squiz DisallowComparisonAssignmentSniff inconsistent errors with inline IF statements
- Fixed bug [#617][sq-617] : Space after switch keyword in PSR-2 is not being enforced
- Fixed bug [#621][sq-621] : PSR2 SwitchDeclaration sniff doesn't detect, or correctly fix, case body on same line as statement

[sq-567]: https://github.com/squizlabs/PHP_CodeSniffer/issues/567
[sq-584]: https://github.com/squizlabs/PHP_CodeSniffer/issues/584
[sq-589]: https://github.com/squizlabs/PHP_CodeSniffer/issues/589
[sq-592]: https://github.com/squizlabs/PHP_CodeSniffer/issues/592
[sq-594]: https://github.com/squizlabs/PHP_CodeSniffer/issues/594
[sq-595]: https://github.com/squizlabs/PHP_CodeSniffer/pull/595
[sq-598]: https://github.com/squizlabs/PHP_CodeSniffer/issues/598
[sq-603]: https://github.com/squizlabs/PHP_CodeSniffer/issues/603
[sq-605]: https://github.com/squizlabs/PHP_CodeSniffer/issues/605
[sq-611]: https://github.com/squizlabs/PHP_CodeSniffer/issues/611
[sq-612]: https://github.com/squizlabs/PHP_CodeSniffer/issues/612
[sq-615]: https://github.com/squizlabs/PHP_CodeSniffer/issues/615
[sq-616]: https://github.com/squizlabs/PHP_CodeSniffer/issues/616
[sq-617]: https://github.com/squizlabs/PHP_CodeSniffer/issues/617
[sq-621]: https://github.com/squizlabs/PHP_CodeSniffer/issues/621

## [2.3.2] - 2015-04-29

### Changed
- The error message for PSR2.ControlStructures.SwitchDeclaration.WrongOpenercase is now clearer (request [#579][sq-579])

### Fixed
- Fixed bug [#545][sq-545] : Long list of CASE statements can cause tokenizer to reach a depth limit
- Fixed bug [#565][sq-565] : Squiz.WhiteSpace.OperatorSpacing reports negative number in short array
    - Thanks to [Vašek Purchart][@VasekPurchart] for the patch
    - Same fix also applied to Squiz.Formatting.OperatorBracket
- Fixed bug [#569][sq-569] : Generic ScopeIndentSniff throws PHP notices in JS files
- Fixed bug [#570][sq-570] : Phar class fatals in PHP less than 5.3

[sq-545]: https://github.com/squizlabs/PHP_CodeSniffer/issues/545
[sq-565]: https://github.com/squizlabs/PHP_CodeSniffer/pull/565
[sq-569]: https://github.com/squizlabs/PHP_CodeSniffer/pull/569
[sq-570]: https://github.com/squizlabs/PHP_CodeSniffer/issues/570
[sq-579]: https://github.com/squizlabs/PHP_CodeSniffer/issues/579

## [2.3.1] - 2015-04-23

### Changed
- PHPCS can now exit with 0 even if errors are found
    - Set the ignore_errors_on_exit config variable to 1 to set this behaviour
    - Use with the ignore_warnings_on_exit config variable to never return a non-zero exit code
- Added Generic DisallowLongArraySyntaxSniff to enforce the use of the PHP short array syntax (request [#483][sq-483])
    - Thanks to [Xaver Loppenstedt][@xalopp] for helping with tests
- Added Generic DisallowShortArraySyntaxSniff to ban the use of the PHP short array syntax (request [#483][sq-483])
    - Thanks to [Xaver Loppenstedt][@xalopp] for helping with tests
- Generic ScopeIndentSniff no longer does exact checking for content inside parenthesis (request [#528][sq-528])
    - Only applies to custom coding standards that set the "exact" flag to TRUE
- Squiz ConcatenationSpacingSniff now has a setting to ignore newline characters around operators (request [#511][sq-511])
    - Default remains FALSE, so newlines are not allowed
    - Override the "ignoreNewlines" setting in a ruleset.xml file to change
- Squiz InlineCommentSniff no longer checks the last char of a comment if the first char is not a letter (request [#505][sq-505])
- The Squiz standard has increased the max padding for statement alignment from 12 to 20

### Fixed
- Fixed bug [#479][sq-479] : Yielded values are not recognised as returned values in Squiz FunctionComment sniff
- Fixed bug [#512][sq-512] : Endless loop whilst parsing mixture of control structure styles
- Fixed bug [#515][sq-515] : Spaces in JS block incorrectly flagged as indentation error
- Fixed bug [#523][sq-523] : Generic ScopeIndent errors for IF in FINALLY
- Fixed bug [#527][sq-527] : Closure inside IF statement is not tokenized correctly
- Fixed bug [#529][sq-529] : Squiz.Strings.EchoedStrings gives false positive when echoing using an inline condition
- Fixed bug [#537][sq-537] : Using --config-set is breaking phpcs.phar
- Fixed bug [#543][sq-543] : SWITCH with closure in condition generates inline control structure error
- Fixed bug [#551][sq-551] : Multiple catch blocks not checked in Squiz.ControlStructures.ControlSignature sniff
- Fixed bug [#554][sq-554] : ScopeIndentSniff causes errors when encountering an unmatched parenthesis
- Fixed bug [#558][sq-558] : PHPCBF adds brace for ELSE IF split over multiple lines
- Fixed bug [#564][sq-564] : Generic MultipleStatementAlignment sniff reports incorrect errors for multiple assignments on a single line

[sq-479]: https://github.com/squizlabs/PHP_CodeSniffer/issues/479
[sq-483]: https://github.com/squizlabs/PHP_CodeSniffer/issues/483
[sq-505]: https://github.com/squizlabs/PHP_CodeSniffer/issues/505
[sq-511]: https://github.com/squizlabs/PHP_CodeSniffer/issues/511
[sq-512]: https://github.com/squizlabs/PHP_CodeSniffer/issues/512
[sq-515]: https://github.com/squizlabs/PHP_CodeSniffer/issues/515
[sq-523]: https://github.com/squizlabs/PHP_CodeSniffer/issues/523
[sq-527]: https://github.com/squizlabs/PHP_CodeSniffer/issues/527
[sq-528]: https://github.com/squizlabs/PHP_CodeSniffer/issues/528
[sq-529]: https://github.com/squizlabs/PHP_CodeSniffer/issues/529
[sq-537]: https://github.com/squizlabs/PHP_CodeSniffer/issues/537
[sq-543]: https://github.com/squizlabs/PHP_CodeSniffer/issues/543
[sq-551]: https://github.com/squizlabs/PHP_CodeSniffer/issues/551
[sq-554]: https://github.com/squizlabs/PHP_CodeSniffer/issues/554
[sq-558]: https://github.com/squizlabs/PHP_CodeSniffer/issues/558
[sq-564]: https://github.com/squizlabs/PHP_CodeSniffer/issues/564

## [2.3.0] - 2015-03-04

### Changed
- The existence of the main config file is now cached to reduce is_file() calls when it doesn't exist (request [#486][sq-486])
- Abstract classes inside the Sniffs directory are now ignored even if they are named `[Name]Sniff.php` (request [#476][sq-476])
    - Thanks to [David Vernet][@Decave] for the patch
- PEAR and Squiz FileComment sniffs no longer have @ in their error codes
    - e.g., PEAR.Commenting.FileComment.Duplicate@categoryTag becomes PEAR.Commenting.FileComment.DuplicateCategoryTag
    - e.g., Squiz.Commenting.FileComment.Missing@categoryTag becomes Squiz.Commenting.FileComment.MissingCategoryTag
- PEAR MultiLineConditionSniff now allows comment lines inside multi-line IF statement conditions
    - Thanks to [Klaus Purer][@klausi] for the patch
- Generic ForbiddenFunctionsSniff now supports setting null replacements in ruleset files (request [#263][sq-263])
- Generic opening function brace sniffs now support checking of closures
    - Set the checkClosures property to TRUE (default is FALSE) in your ruleset.xml file to enable this
    - Can also set the checkFunctions property to FALSE (default is TRUE) in your ruleset.xml file to only check closures
    - Affects OpeningFunctionBraceBsdAllmanSniff and OpeningFunctionBraceKernighanRitchieSniff
- Generic OpeningFunctionBraceKernighanRitchieSniff can now fix all the errors it finds
- Generic OpeningFunctionBraceKernighanRitchieSniff now allows empty functions with braces next to each other
- Generic OpeningFunctionBraceBsdAllmanSniff now allows empty functions with braces next to each other
- Improved auto report width for the "full" report
- Improved conflict detection during auto fixing
- Generic ScopeIndentSniff is no longer confused by empty closures
- Squiz ControlSignatureSniff now always ignores comments (fixes bug [#490][sq-490])
    - Include the Squiz.Commenting.PostStatementComment sniff in your ruleset.xml to ban these comments again
- Squiz OperatorSpacingSniff no longer throws errors for code in the form ($foo || -1 === $bar)
- Fixed errors tokenizing T_ELSEIF tokens on HHVM 3.5
- Squiz ArrayDeclarationSniff is no longer tricked by comments after array values
- PEAR IncludingFileSniff no longer produces invalid code when removing parenthesis from require/include statements

### Fixed
- Fixed bug [#415][sq-415] : The @codingStandardsIgnoreStart has no effect during fixing
- Fixed bug [#432][sq-432] : Properties of custom sniffs cannot be configured
- Fixed bug [#453][sq-453] : PSR2 standard does not allow closing tag for mixed PHP/HTML files
- Fixed bug [#457][sq-457] : FunctionCallSignature sniffs do not support here/nowdoc syntax and can cause syntax error when fixing
- Fixed bug [#466][sq-466] : PropertyLabelSpacing JS fixer issue when there is no space after colon
- Fixed bug [#473][sq-473] : Writing a report for an empty folder to existing file includes the existing contents
- Fixed bug [#485][sq-485] : PHP notice in Squiz.Commenting.FunctionComment when checking malformed @throws comment
- Fixed bug [#491][sq-491] : Generic InlineControlStructureSniff can correct with missing semicolon
    - Thanks to [Jesse Donat][@donatj] for the patch
- Fixed bug [#492][sq-492] : Use statements don't increase the scope indent
- Fixed bug [#493][sq-493] : PSR1_Sniffs_Methods_CamelCapsMethodNameSniff false positives for some magic method detection
    - Thanks to [Andreas Möller][@localheinz] for the patch
- Fixed bug [#496][sq-496] : Closures in PSR2 are not checked for a space after the function keyword
- Fixed bug [#497][sq-497] : Generic InlineControlStructureSniff does not support alternative SWITCH syntax
- Fixed bug [#500][sq-500] : Functions not supported as values in Squiz ArrayDeclaration sniff
- Fixed bug [#501][sq-501] : ScopeClosingBrace and ScopeIndent conflict with closures used as array values
    - Generic ScopeIndentSniff may now report fewer errors for closures, but perform the same fixes
- Fixed bug [#502][sq-502] : PSR1 SideEffectsSniff sees declare() statements as side effects

[sq-415]: https://github.com/squizlabs/PHP_CodeSniffer/issues/415
[sq-432]: https://github.com/squizlabs/PHP_CodeSniffer/issues/432
[sq-453]: https://github.com/squizlabs/PHP_CodeSniffer/issues/453
[sq-457]: https://github.com/squizlabs/PHP_CodeSniffer/issues/457
[sq-466]: https://github.com/squizlabs/PHP_CodeSniffer/issues/466
[sq-473]: https://github.com/squizlabs/PHP_CodeSniffer/issues/473
[sq-476]: https://github.com/squizlabs/PHP_CodeSniffer/issues/476
[sq-485]: https://github.com/squizlabs/PHP_CodeSniffer/issues/485
[sq-486]: https://github.com/squizlabs/PHP_CodeSniffer/issues/486
[sq-490]: https://github.com/squizlabs/PHP_CodeSniffer/issues/490
[sq-491]: https://github.com/squizlabs/PHP_CodeSniffer/pull/491
[sq-492]: https://github.com/squizlabs/PHP_CodeSniffer/pull/492
[sq-493]: https://github.com/squizlabs/PHP_CodeSniffer/pull/493
[sq-496]: https://github.com/squizlabs/PHP_CodeSniffer/issues/496
[sq-497]: https://github.com/squizlabs/PHP_CodeSniffer/issues/497
[sq-500]: https://github.com/squizlabs/PHP_CodeSniffer/issues/500
[sq-501]: https://github.com/squizlabs/PHP_CodeSniffer/issues/501
[sq-502]: https://github.com/squizlabs/PHP_CodeSniffer/issues/502

## [2.2.0] - 2015-01-22

### Changed
- Added (hopefully) tastefully used colors to report and progress output for the phpcs command
    - Use the --colors command line argument to use colors in output
    - Use the command "phpcs --config-set colors true" to turn colors on by default
    - Use the --no-colors command line argument to turn colors off when the config value is set
- Added support for using the full terminal width for report output
    - Use the --report-width=auto command line argument to auto-size the reports
    - Use the command "phpcs --config-set report_width auto" to use auto-sizing by default
- Reports will now size to fit inside the report width setting instead of always using padding to fill the space
- If no files or standards are specified, PHPCS will now look for a phpcs.xml file in the current directory
    - This file has the same format as a standard ruleset.xml file
    - The phpcs.xml file should specify (at least) files to process and a standard/sniffs to use
    - Useful for running the phpcs and phpcbf commands without any arguments at the top of a repository
- Default file paths can now be specified in a ruleset.xml file using the "file" tag
    - File paths are only processed if no files were specified on the command line
- Extensions specified on the CLI are now merged with those set in ruleset.xml files
    - Previously, the ruleset.xml file setting replaced the CLI setting completely
- Squiz coding standard now requires lowercase PHP constants (true, false and null)
    - Removed Squiz.NamingConventions.ConstantCase sniff as the rule is now consistent across PHP and JS files
- Squiz FunctionOpeningBraceSpaceSniff no longer does additional checks for JS functions
    - PHP and JS functions and closures are now treated the same way
- Squiz MultiLineFunctionDeclarationSniff now supports JS files
- Interactive mode no longer breaks if you also specify a report type on the command line
- PEAR InlineCommentSniff now fixes the Perl-style comments that it finds (request [#375][sq-375])
- PSR2 standard no longer fixes the placement of docblock open tags as comments are excluded from this standard
- PSR2 standard now sets a default tab width of 4 spaces
- Generic DocCommentSniff now only disallows lowercase letters at the start of a long/short comment (request [#377][sq-377])
    - All non-letter characters are now allowed, including markdown special characters and numbers
- Generic DisallowMultipleStatementsSniff now allows multiple open/close tags on the same line (request [#423][sq-423])
- Generic CharacterBeforePHPOpeningTagSniff now only checks the first PHP tag it finds (request [#423][sq-423])
- Generic CharacterBeforePHPOpeningTagSniff now allows a shebang line at the start of the file (request [#20481][pear-20481])
- Generic InlineHTMLUnitTest now allows a shebang line at the start of the file (request [#20481][pear-20481])
- PEAR ObjectOperatorIndentSniff now only checks object operators at the start of a line
- PEAR FileComment and ClassComment sniffs no longer have @ in their error codes
    - E.g., PEAR.Commenting.FileComment.Missing@categoryTag becomes PEAR.Commenting.FileComment.MissingCategoryTag
    - Thanks to [Grzegorz Rygielski][@grzr] for the patch
- Squiz ControlStructureSpacingSniff no longer enforces a blank line before CATCH statements
- Squiz FunctionCommentSniff now fixes the return type in the @return tag (request [#392][sq-392])
- Squiz BlockCommentSniff now only disallows lowercase letters at the start of the comment
- Squiz InlineCommentSniff now only disallows lowercase letters at the start of the comment
- Squiz OperatorSpacingSniff now has a setting to ignore newline characters around operators (request [#348][sq-348])
    - Default remains FALSE, so newlines are not allowed
    - Override the "ignoreNewlines" setting in a ruleset.xml file to change
- PSR2 ControlStructureSpacingSniff now checks for, and fixes, newlines after the opening parenthesis
- Added a markdown document generator (--generator=markdown to use)
    - Thanks to [Stefano Kowalke][@Konafets] for the contribution

### Fixed
- Fixed bug [#379][sq-379] : Squiz.Arrays.ArrayDeclaration.NoCommaAfterLast incorrectly detects comments
- Fixed bug [#382][sq-382] : JS tokenizer incorrect for inline conditionally created immediately invoked anon function
- Fixed bug [#383][sq-383] : Squiz.Arrays.ArrayDeclaration.ValueNoNewline incorrectly detects nested arrays
- Fixed bug [#386][sq-386] : Undefined offset in Squiz.FunctionComment sniff when param has no comment
- Fixed bug [#390][sq-390] : Indentation of non-control structures isn't adjusted when containing structure is fixed
- Fixed bug [#400][sq-400] : InlineControlStructureSniff fails to fix when statement has no semicolon
- Fixed bug [#401][sq-401] : PHPCBF no-patch option shows an error when there are no fixable violations in a file
- Fixed bug [#405][sq-405] : The "Squiz.WhiteSpace.FunctionSpacing" sniff removes class "}" during fixing
- Fixed bug [#407][sq-407] : PEAR.ControlStructures.MultiLineCondition doesn't account for comments at the end of lines
- Fixed bug [#410][sq-410] : The "Squiz.WhiteSpace.MemberVarSpacing" not respecting "var"
- Fixed bug [#411][sq-411] : Generic.WhiteSpace.ScopeIndent.Incorrect - false positive with multiple arrays in argument list
- Fixed bug [#412][sq-412] : PSR2 multi-line detection doesn't work for inline IF and string concats
- Fixed bug [#414][sq-414] : Squiz.WhiteSpace.MemberVarSpacing - inconsistent checking of member vars with comment
- Fixed bug [#433][sq-433] : Wrong detection of Squiz.Arrays.ArrayDeclaration.KeyNotAligned when key contains space
- Fixed bug [#434][sq-434] : False positive for spacing around "=>" in inline array within foreach
- Fixed bug [#452][sq-452] : Ruleset exclude-pattern for specific sniff code ignored when using CLI --ignore option
- Fixed bug [#20482][pear-20482] : Scope indent sniff can get into infinite loop when processing a parse error

[sq-348]: https://github.com/squizlabs/PHP_CodeSniffer/issues/348
[sq-375]: https://github.com/squizlabs/PHP_CodeSniffer/issues/375
[sq-377]: https://github.com/squizlabs/PHP_CodeSniffer/issues/377
[sq-379]: https://github.com/squizlabs/PHP_CodeSniffer/issues/379
[sq-382]: https://github.com/squizlabs/PHP_CodeSniffer/issues/382
[sq-383]: https://github.com/squizlabs/PHP_CodeSniffer/issues/383
[sq-386]: https://github.com/squizlabs/PHP_CodeSniffer/issues/386
[sq-390]: https://github.com/squizlabs/PHP_CodeSniffer/issues/390
[sq-392]: https://github.com/squizlabs/PHP_CodeSniffer/issues/392
[sq-400]: https://github.com/squizlabs/PHP_CodeSniffer/issues/400
[sq-401]: https://github.com/squizlabs/PHP_CodeSniffer/issues/401
[sq-405]: https://github.com/squizlabs/PHP_CodeSniffer/issues/405
[sq-407]: https://github.com/squizlabs/PHP_CodeSniffer/issues/407
[sq-410]: https://github.com/squizlabs/PHP_CodeSniffer/issues/410
[sq-411]: https://github.com/squizlabs/PHP_CodeSniffer/issues/411
[sq-412]: https://github.com/squizlabs/PHP_CodeSniffer/issues/412
[sq-414]: https://github.com/squizlabs/PHP_CodeSniffer/issues/414
[sq-423]: https://github.com/squizlabs/PHP_CodeSniffer/issues/423
[sq-433]: https://github.com/squizlabs/PHP_CodeSniffer/issues/433
[sq-434]: https://github.com/squizlabs/PHP_CodeSniffer/issues/434
[sq-452]: https://github.com/squizlabs/PHP_CodeSniffer/issues/452
[pear-20481]: https://pear.php.net/bugs/bug.php?id=20481
[pear-20482]: https://pear.php.net/bugs/bug.php?id=20482

## [2.1.0] - 2014-12-18

### Changed
- Time and memory output is now shown if progress information is also shown (request [#335][sq-335])
- A tilde can now be used to reference a user's home directory in a path to a standard (request [#353][sq-353])
- Added PHP_CodeSniffer_File::findStartOfStatement() to find the first non-whitespace token in a statement
    - Possible alternative for code using PHP_CodeSniffer_File::findPrevious() with the local flag set
- Added PHP_CodeSniffer_File::findEndOfStatement() to find the last non-whitespace token in a statement
    - Possible alternative for code using PHP_CodeSniffer_File::findNext() with the local flag set
- Generic opening function brace sniffs now ensure the opening brace is the last content on the line
    - Affects OpeningFunctionBraceBsdAllmanSniff and OpeningFunctionBraceKernighanRitchieSniff
    - Also enforced in PEAR FunctionDeclarationSniff and Squiz MultiLineFunctionDeclarationSniff
- Generic DisallowTabIndentSniff now replaces tabs everywhere it finds them, except in strings and here/now docs
- Generic EmptyStatementSniff error codes now contain the type of empty statement detected (request [#314][sq-314])
    - All messages generated by this sniff are now errors (empty CATCH was previously a warning)
    - Message code `Generic.CodeAnalysis.EmptyStatement.NotAllowed` has been removed
    - Message code `Generic.CodeAnalysis.EmptyStatement.NotAllowedWarning` has been removed
    - New message codes have the format `Generic.CodeAnalysis.EmptyStatement.Detected[TYPE]`
    - Example code is `Generic.CodeAnalysis.EmptyStatement.DetectedCATCH`
    - You can now use a custom ruleset to change messages to warnings and to exclude them
- PEAR and Squiz FunctionCommentSniffs no longer ban `@return` tags for constructors and destructors
    - Removed message PEAR.Commenting.FunctionComment.ReturnNotRequired
    - Removed message Squiz.Commenting.FunctionComment.ReturnNotRequired
    - Change initiated by request [#324][sq-324] and request [#369][sq-369]
- Squiz EmptyStatementSniff has been removed
    - Squiz standard now includes Generic EmptyStatementSniff and turns off the empty CATCH error
- Squiz ControlSignatureSniff fixes now retain comments between the closing parenthesis and open brace
- Squiz SuperfluousWhitespaceSniff now checks for extra blank lines inside closures
    - Thanks to [Sertan Danis][@sertand] for the patch
- Squiz ArrayDeclarationSniff now skips function calls while checking multi-line arrays

### Fixed
- Fixed bug [#337][sq-337] : False positive with anonymous functions in Generic_Sniffs_WhiteSpace_ScopeIndentSniff
- Fixed bug [#339][sq-339] : reformatting brace location can result in broken code
- Fixed bug [#342][sq-342] : Nested ternary operators not tokenized correctly
- Fixed bug [#345][sq-345] : Javascript regex not tokenized when inside array
- Fixed bug [#346][sq-346] : PHP path can't be determined in some cases in "phpcs.bat" (on Windows XP)
- Fixed bug [#358][sq-358] : False positives for Generic_Sniffs_WhiteSpace_ScopeIndentSniff
- Fixed bug [#361][sq-361] : Sniff-specific exclude patterns don't work for Windows
- Fixed bug [#364][sq-364] : Don't interpret "use function" as declaration
- Fixed bug [#366][sq-366] : phpcbf with PSR2 errors on control structure alternative syntax
- Fixed bug [#367][sq-367] : Nested Anonymous Functions Causing False Negative
- Fixed bug [#371][sq-371] : Shorthand binary cast causes tokenizer errors
    - New token T_BINARY_CAST added for the b"string" cast format (the 'b' is the T_BINARY_CAST token)
- Fixed bug [#372][sq-372] : phpcbf parse problem, wrong brace placement for inline IF
- Fixed bug [#373][sq-373] : Double quote usage fix removing too many double quotes
- Fixed bug [#20196][pear-20196] : 1.5.2 breaks scope_closer position

[sq-314]: https://github.com/squizlabs/PHP_CodeSniffer/issues/314
[sq-324]: https://github.com/squizlabs/PHP_CodeSniffer/issues/324
[sq-335]: https://github.com/squizlabs/PHP_CodeSniffer/issues/335
[sq-337]: https://github.com/squizlabs/PHP_CodeSniffer/issues/337
[sq-339]: https://github.com/squizlabs/PHP_CodeSniffer/issues/339
[sq-342]: https://github.com/squizlabs/PHP_CodeSniffer/issues/342
[sq-345]: https://github.com/squizlabs/PHP_CodeSniffer/issues/345
[sq-346]: https://github.com/squizlabs/PHP_CodeSniffer/issues/346
[sq-353]: https://github.com/squizlabs/PHP_CodeSniffer/issues/353
[sq-358]: https://github.com/squizlabs/PHP_CodeSniffer/issues/358
[sq-361]: https://github.com/squizlabs/PHP_CodeSniffer/issues/361
[sq-364]: https://github.com/squizlabs/PHP_CodeSniffer/pull/364
[sq-366]: https://github.com/squizlabs/PHP_CodeSniffer/issues/366
[sq-367]: https://github.com/squizlabs/PHP_CodeSniffer/issues/367
[sq-369]: https://github.com/squizlabs/PHP_CodeSniffer/issues/369
[sq-371]: https://github.com/squizlabs/PHP_CodeSniffer/issues/371
[sq-372]: https://github.com/squizlabs/PHP_CodeSniffer/issues/372
[sq-373]: https://github.com/squizlabs/PHP_CodeSniffer/issues/373
[pear-20196]: https://pear.php.net/bugs/bug.php?id=20196

## [2.0.0] - 2014-12-05

### Changed
- JS tokenizer now sets functions as T_CLOSUREs if the function is anonymous
- JS tokenizer now sets all objects to T_OBJECT
    - Object end braces are set to a new token T_CLOSE_OBJECT
    - T_OBJECT tokens no longer act like scopes; i.e., they have no condition/opener/closer
    - T_PROPERTY tokens no longer act like scopes; i.e., they have no condition/opener/closer
    - T_OBJECT tokens have a bracket_closer instead, which can be used to find the ending
    - T_CLOSE_OBJECT tokens have a bracket_opener
- Improved regular expression detection in the JS tokenizer
- You can now get PHP_CodeSniffer to ignore a single line by putting @codingStandardsIgnoreLine in a comment
    - When the comment is found, the comment line and the following line will be ignored
    - Thanks to [Andy Bulford][@abulford] for the contribution
- PHPCBF now prints output when it is changing into directories
- Improved conflict detection during auto fixing
- The -vvv command line argument will now output the current file content for each loop during fixing
- Generic ScopeIndentSniff now checks that open/close PHP tags are aligned to the correct column
- PEAR FunctionCallSignatureSniff now checks indent of closing parenthesis even if it is not on a line by itself
- PEAR FunctionCallSignatureSniff now supports JS files
- PEAR MultiLineConditionSniff now supports JS files
- Squiz DocCommentAlignmentSniff now supports JS files
- Fixed a problem correcting the closing brace line in Squiz ArrayDeclarationSniff
- Fixed a problem auto-fixing the Squiz.WhiteSpace.FunctionClosingBraceSpace.SpacingBeforeNestedClose error
- Squiz EmbeddedPhpSniff no longer reports incorrect alignment of tags when they are not on new lines
- Squiz EmbeddedPhpSniff now aligns open tags correctly when moving them onto a new line
- Improved fixing of arrays with multiple values in Squiz ArrayDeclarationSniff
- Improved detection of function comments in Squiz FunctionCommentSpacingSniff
- Improved fixing of lines after cases statements in Squiz SwitchDeclarationSniff

### Fixed
- Fixed bug [#311][sq-311] : Suppression of function prototype breaks checking of lines within function
- Fixed bug [#320][sq-320] : Code sniffer indentation issue
- Fixed bug [#333][sq-333] : Nested ternary operators causing problems

[sq-320]: https://github.com/squizlabs/PHP_CodeSniffer/issues/320
[sq-333]: https://github.com/squizlabs/PHP_CodeSniffer/issues/333

## [1.5.6] - 2014-12-05

### Changed
- JS tokenizer now detects xor statements correctly
- The --config-show command now pretty-prints the config values
    - Thanks to [Ken Guest][@kenguest] for the patch
- Setting and removing config values now catches exceptions if the config file is not writable
    - Thanks to [Ken Guest][@kenguest] for the patch
- Setting and removing config values now prints a message to confirm the action and show old values
- You can now get PHP_CodeSniffer to ignore a single line by putting @codingStandardsIgnoreLine in a comment
    - When the comment is found, the comment line and the following line will be ignored
    - Thanks to [Andy Bulford][@abulford] for the contribution
- Generic ConstructorNameSniff no longer errors for PHP4 style constructors when __construct() is present
    - Thanks to [Thibaud Fabre][@fabre-thibaud] for the patch

### Fixed
- Fixed bug [#280][sq-280] : The --config-show option generates error when there is no config file
- Fixed bug [#306][sq-306] : File containing only a namespace declaration raises undefined index notice
- Fixed bug [#308][sq-308] : Squiz InlineIfDeclarationSniff fails on ternary operators inside closure
- Fixed bug [#310][sq-310] : Variadics not recognized by tokenizer
- Fixed bug [#311][sq-311] : Suppression of function prototype breaks checking of lines within function

[sq-311]: https://github.com/squizlabs/PHP_CodeSniffer/issues/311

## [2.0.0RC4] - 2014-11-07

### Changed
- JS tokenizer now detects xor statements correctly
- Improved detection of properties and objects in the JS tokenizer
- Generic ScopeIndentSniff can now fix indents using tabs instead of spaces
    - Set the tabIndent property to TRUE in your ruleset.xml file to enable this
    - It is important to also set a tab-width setting, either in the ruleset or on the command line, for accuracy
- Generic ScopeIndentSniff now checks and auto-fixes JS files
- Generic DisallowSpaceIndentSniff is now able to replace space indents with tab indents during fixing
- Support for phpcs-only and phpcbf-only attributes has been added to all ruleset.xml elements
    - Allows parts of the ruleset to only apply when using a specific tool
    - Useful for doing things like excluding indent fixes but still reporting indent errors
- Unit tests can now set command line arguments during a test run
    - Override getCliValues() and pass an array of CLI arguments for each file being tested
- File-wide sniff properties can now be set using T_INLINE_HTML content during unit test runs
    - Sniffs that start checking at the open tag can only, normally, have properties set using a ruleset
- Generic ConstructorNameSniff no longer errors for PHP4 style constructors when __construct() is present
    - Thanks to [Thibaud Fabre][@fabre-thibaud] for the patch
- Generic DocCommentSniff now checks that the end comment tag is on a new line
- Generic MultipleStatementAlignmentSniff no longer skips assignments for closures
- Squiz DocCommentAlignment sniff now has better checking for single line doc block
- Running unit tests with the -v CLI argument no longer generates PHP errors

### Fixed
- Fixed bug [#295][sq-295] : ScopeIndentSniff hangs when processing nested closures
- Fixed bug [#298][sq-298] : False positive in ScopeIndentSniff when anonymous functions are used with method chaining
- Fixed bug [#302][sq-302] : Fixing code in Squiz InlineComment sniff can remove some comment text
- Fixed bug [#303][sq-303] : Open and close tag on same line can cause a PHP notice checking scope indent
- Fixed bug [#306][sq-306] : File containing only a namespace declaration raises undefined index notice
- Fixed bug [#307][sq-307] : Conditional breaks in case statements get incorrect indentations
- Fixed bug [#308][sq-308] : Squiz InlineIfDeclarationSniff fails on ternary operators inside closure
- Fixed bug [#310][sq-310] : Variadics not recognized by tokenizer

[sq-295]: https://github.com/squizlabs/PHP_CodeSniffer/issues/295
[sq-298]: https://github.com/squizlabs/PHP_CodeSniffer/issues/298
[sq-302]: https://github.com/squizlabs/PHP_CodeSniffer/issues/302
[sq-303]: https://github.com/squizlabs/PHP_CodeSniffer/issues/303
[sq-306]: https://github.com/squizlabs/PHP_CodeSniffer/issues/306
[sq-307]: https://github.com/squizlabs/PHP_CodeSniffer/issues/307
[sq-308]: https://github.com/squizlabs/PHP_CodeSniffer/issues/308
[sq-310]: https://github.com/squizlabs/PHP_CodeSniffer/issues/310

## [2.0.0RC3] - 2014-10-16

### Changed
- Improved default output for PHPCBF and removed the options to print verbose and progress output
- If a .fixed file is supplied for a unit test file, the auto fixes will be checked against it during testing
    - See Generic ScopeIndentUnitTest.inc and ScopeIndentUnitTest.inc.fixed for an example
- Fixer token replacement methods now return TRUE if the change was accepted and FALSE if rejected
- The --config-show command now pretty-prints the config values
    - Thanks to [Ken Guest][@kenguest] for the patch
- Setting and removing config values now catches exceptions if the config file is not writable
    - Thanks to [Ken Guest][@kenguest] for the patch
- Setting and removing config values now prints a message to confirm the action and show old values
- Generic ScopeIndentSniff has been completely rewritten to improve fixing and embedded PHP detection
- Generic DisallowTabIndent and DisallowSpaceIndent sniffs now detect indents at the start of block comments
- Generic DisallowTabIndent and DisallowSpaceIndent sniffs now detect indents inside multi-line strings
- Generic DisallowTabIndentSniff now replaces tabs inside doc block comments
- Squiz ControlStructureSpacingSniff error codes have been corrected; they were reversed
- Squiz EmbeddedPhpSniff now checks open and close tag indents and fixes some errors
- Squiz FileCommentSniff no longer throws incorrect blank line before comment errors in JS files
- Squiz ClassDeclarationSniff now has better checking for blank lines after a closing brace
- Removed error Squiz.Classes.ClassDeclaration.NoNewlineAfterCloseBrace (request [#285][sq-285])
    - Already handled by Squiz.Classes.ClassDeclaration.CloseBraceSameLine

### Fixed
- Fixed bug [#280][sq-280] : The --config-show option generates error when there is no config file

[sq-280]: https://github.com/squizlabs/PHP_CodeSniffer/issues/280
[sq-285]: https://github.com/squizlabs/PHP_CodeSniffer/issues/285

## [2.0.0RC2] - 2014-09-26

### Changed
- Minified JS and CSS files are now detected and skipped (fixes bug [#252][sq-252] and bug [#19899][pear-19899])
    - A warning will be added to the file so it can be found in the report and ignored in the future
- Fixed incorrect length of JS object operator tokens
- PHP tokenizer no longer converts class/function names to special tokens types
    - Class/function names such as parent and true would become special tokens such as T_PARENT and T_TRUE
- PHPCS can now exit with 0 if only warnings were found (request [#262][sq-262])
    - Set the ignore_warnings_on_exit config variable to 1 to set this behaviour
    - Default remains at exiting with 0 only if no errors and no warnings were found
    - Also changes return value of PHP_CodeSniffer_Reporting::printReport()
- Rulesets can now set associative array properties
    - property `name="[property]" type="array" value="foo=>bar,baz=>qux"`
- Generic ForbiddenFunctionsSniff now has a public property called forbiddenFunctions (request [#263][sq-263])
    - Override the property in a ruleset.xml file to define forbidden functions and their replacements
    - A replacement of NULL indicates that no replacement is available
    - e.g., value="delete=>unset,print=>echo,create_function=>null"
    - Custom sniffs overriding this one will need to change the visibility of their member var
- Improved closure support in Generic ScopeIndentSniff
- Improved indented PHP tag support in Generic ScopeIndentSniff
- Improved fixing of mixed line indents in Generic ScopeIndentSniff
- Added conflict detection to the file fixer
    - If 2 sniffs look to be conflicting, one change will be ignored to allow a fix to occur
- Generic CamelCapsFunctionNameSniff now ignores a single leading underscore
    - Thanks to [Alex Slobodiskiy][@xt99] for the patch
- Standards can now be located within hidden directories (further fix for bug [#20323][pear-20323])
    - Thanks to [Klaus Purer][@klausi] for the patch
- Sniff ignore patterns now replace Win dir separators like file ignore patterns already did
- Exclude patterns now use backtick delimiters, allowing all special characters to work correctly again
    - Thanks to [Jeremy Edgell][@jedgell] for the patch
- Errors converted to warnings in a ruleset (and vice versa) now retain their fixable status
    - Thanks to [Alexander Obuhovich][@aik099] for the patch
- Squiz ConcatenationSpacingSniff now has a setting to specify how many spaces there should be around concat operators
    - Default remains at 0
    - Override the "spacing" setting in a ruleset.xml file to change
- Added auto-fixes for Squiz InlineCommentSniff
- Generic DocCommentSniff now correctly fixes additional blank lines at the end of a comment
- Squiz OperatorBracketSniff now correctly fixes operations that include arrays
- Zend ClosingTagSniff fix now correctly leaves closing tags when followed by HTML
- Added Generic SyntaxSniff to check for syntax errors in PHP files
    - Thanks to [Blaine Schmeisser][@bayleedev] for the contribution
- Added Generic OneTraitPerFileSniff to check that only one trait is defined in each file
    - Thanks to [Alexander Obuhovich][@aik099] for the contribution
- Squiz DiscouragedFunctionsSniff now warns about var_dump()
- PEAR ValidFunctionNameSniff no longer throws an error for _()
- Squiz and PEAR FunctionCommentSniffs now support _()
- Generic DisallowTabIndentSniff now checks for, and fixes, mixed indents again
- Generic UpperCaseConstantSniff and LowerCaseConstantSniff now ignore function names

### Fixed
- Fixed bug [#243][sq-243] : Missing DocBlock not detected
- Fixed bug [#248][sq-248] : FunctionCommentSniff expects ampersand on param name
- Fixed bug [#265][sq-265] : False positives with type hints in ForbiddenFunctionsSniff
- Fixed bug [#20373][pear-20373] : Inline comment sniff tab handling way
- Fixed bug [#20377][pear-20377] : Error when trying to execute phpcs with report=json
- Fixed bug [#20378][pear-20378] : Report appended to existing file if no errors found in run
- Fixed bug [#20381][pear-20381] : Invalid "Comment closer must be on a new line"
    - Thanks to [Brad Kent][@bkdotcom] for the patch
- Fixed bug [#20402][pear-20402] : SVN pre-commit hook fails due to unknown argument error

[sq-243]: https://github.com/squizlabs/PHP_CodeSniffer/issues/243
[sq-252]: https://github.com/squizlabs/PHP_CodeSniffer/issues/252
[sq-262]: https://github.com/squizlabs/PHP_CodeSniffer/issues/262
[sq-263]: https://github.com/squizlabs/PHP_CodeSniffer/issues/263
[pear-19899]: https://pear.php.net/bugs/bug.php?id=19899
[pear-20377]: https://pear.php.net/bugs/bug.php?id=20377
[pear-20402]: https://pear.php.net/bugs/bug.php?id=20402

## [1.5.5] - 2014-09-25

### Changed
- PHP tokenizer no longer converts class/function names to special tokens types
    - Class/function names such as parent and true would become special tokens such as T_PARENT and T_TRUE
- Improved closure support in Generic ScopeIndentSniff
- Improved indented PHP tag support in Generic ScopeIndentSniff
- Generic CamelCapsFunctionNameSniff now ignores a single leading underscore
    - Thanks to [Alex Slobodiskiy][@xt99] for the patch
- Standards can now be located within hidden directories (further fix for bug [#20323][pear-20323])
    - Thanks to [Klaus Purer][@klausi] for the patch
- Added Generic SyntaxSniff to check for syntax errors in PHP files
    - Thanks to [Blaine Schmeisser][@bayleedev] for the contribution
- Squiz DiscouragedFunctionsSniff now warns about var_dump()
- PEAR ValidFunctionNameSniff no longer throws an error for _()
- Squiz and PEAR FunctionCommentSnif now support _()
- Generic UpperCaseConstantSniff and LowerCaseConstantSniff now ignore function names

### Fixed
- Fixed bug [#248][sq-248] : FunctionCommentSniff expects ampersand on param name
- Fixed bug [#265][sq-265] : False positives with type hints in ForbiddenFunctionsSniff
- Fixed bug [#20373][pear-20373] : Inline comment sniff tab handling way
- Fixed bug [#20378][pear-20378] : Report appended to existing file if no errors found in run
- Fixed bug [#20381][pear-20381] : Invalid "Comment closer must be on a new line"
    - Thanks to [Brad Kent][@bkdotcom] for the patch
- Fixed bug [#20386][pear-20386] : Squiz.Commenting.ClassComment.SpacingBefore thrown if first block comment

[sq-248]: https://github.com/squizlabs/PHP_CodeSniffer/issues/248
[sq-265]: https://github.com/squizlabs/PHP_CodeSniffer/pull/265
[pear-20373]: https://pear.php.net/bugs/bug.php?id=20373
[pear-20378]: https://pear.php.net/bugs/bug.php?id=20378
[pear-20381]: https://pear.php.net/bugs/bug.php?id=20381
[pear-20386]: https://pear.php.net/bugs/bug.php?id=20386

## [2.0.0RC1] - 2014-08-06

### Changed
- PHPCBF will now fix incorrect newline characters in a file
- PHPCBF now exits cleanly when there are no errors to fix
- Added phpcbf.bat file for Windows
- Verbose option no longer errors when using a phar file with a space in the path
- Fixed a reporting error when using HHVM
    - Thanks to [Martins Sipenko][@martinssipenko] for the patch
- addFixableError() and addFixableWarning() now only return true if the fixer is enabled
    - Saves checking ($phpcsFile->fixer->enabled === true) before every fix
- Added addErrorOnLine() and addWarningOnLine() to add a non-fixable violation to a line at column 1
    - Useful if you are generating errors using an external tool or parser and only know line numbers
    - Thanks to [Ondřej Mirtes][@ondrejmirtes] for the patch
- CSS tokenizer now identifies embedded PHP code using the new T_EMBEDDED_PHP token type
    - The entire string of PHP is contained in a single token
- PHP tokenizer contains better detection of short array syntax
- Unit test runner now also test any standards installed under the installed_paths config var
- Exclude patterns now use {} delimiters, allowing the | special character to work correctly again
- The filtering component of the --extensions argument is now ignored again when passing filenames
    - Can still be used to specify a custom tokenizer for each extension when passing filenames
    - If no tokenizer is specified, default values will be used for common file extensions
- Diff report now produces relative paths on Windows, where possible (further fix for bug [#20234][pear-20234])
- If a token's content has been modified by the tab-width setting, it will now have an orig_content in the tokens array
- Generic DisallowSpaceIndent and DisallowTabIndent sniffs now check original indent content even when tab-width is set
    - Previously, setting --tab-width would force both to check the indent as spaces
- Fixed a problem where PHPCBF could replace tabs with too many spaces when changing indents
- Fixed a problem that could occur with line numbers when using HHVM to check files with Windows newline characters
- Removed use of sys_get_temp_dir() as this is not supported by the min PHP version
- Squiz ArrayDeclarationSniff now supports short array syntax
- Squiz ControlSignatureSniff no longer uses the Abstract Pattern sniff
    - If you are extending this sniff, you'll need to rewrite your code
    - The rewrite allows this sniff to fix all control structure formatting issues it finds
- The installed_paths config var now accepts relative paths
    - The paths are relative to the PHP_CodeSniffer install directory
    - Thanks to [Weston Ruter][@westonruter] for the patch
- Generic ScopeIndentSniff now accounts for different open tag indents
- PEAR FunctionDeclarationSniff now ignores short arrays when checking indent
    - Thanks to [Daniel Tschinder][@danez] for the patch
- PSR2 FunctionCallSignatureSniff now treats multi-line strings as a single-line argument, like arrays and closures
    - Thanks to [Dawid Nowak][@MacDada] for the patch
- PSR2 UseDeclarationSniff now checks for a single space after the USE keyword
- Generic ForbiddenFunctionsSniff now detects calls to functions in the global namespace
    - Thanks to [Ole Martin Handeland][@olemartinorg] for the patch
- Generic LowerCaseConstantSniff and UpperCaseConstantSniff now ignore namespaces beginning with TRUE/FALSE/NULL
    - Thanks to [Renan Gonçalves][@renan] for the patch
- Squiz InlineCommentSniff no longer requires a blank line after post-statement comments (request [#20299][pear-20299])
- Squiz SelfMemberReferenceSniff now works correctly with namespaces
- Squiz FunctionCommentSniff is now more relaxed when checking namespaced type hints
- Tab characters are now encoded in abstract pattern error messages
    - Thanks to [Blaine Schmeisser][@bayleedev] for the patch
- Invalid sniff codes passed to --sniffs now show a friendly error message (request [#20313][pear-20313])
- Generic LineLengthSniff now shows a warning if the iconv module is disabled (request [#20314][pear-20314])
- Source report no longer shows errors if category or sniff names ends in an uppercase error
    - Thanks to [Jonathan Marcil][@jmarcil] for the patch

### Fixed
- Fixed bug [#20261][pear-20261] : phpcbf has an endless fixing loop
- Fixed bug [#20268][pear-20268] : Incorrect documentation titles in PEAR documentation
- Fixed bug [#20296][pear-20296] : new array notion in function comma check fails
- Fixed bug [#20297][pear-20297] : phar does not work when renamed it to phpcs
- Fixed bug [#20307][pear-20307] : PHP_CodeSniffer_Standards_AbstractVariableSniff analyze traits
- Fixed bug [#20308][pear-20308] : Squiz.ValidVariableNameSniff - wrong variable usage
- Fixed bug [#20309][pear-20309] : Use "member variable" term in sniff "processMemberVar" method
- Fixed bug [#20310][pear-20310] : PSR2 does not check for space after function name
- Fixed bug [#20322][pear-20322] : Display rules set to type=error even when suppressing warnings
- Fixed bug [#20323][pear-20323] : PHPCS tries to load sniffs from hidden directories
- Fixed bug [#20346][pear-20346] : Fixer endless loop with Squiz.CSS sniffs
- Fixed bug [#20355][pear-20355] : No sniffs are registered with PHAR on Windows

[pear-20261]: https://pear.php.net/bugs/bug.php?id=20261
[pear-20297]: https://pear.php.net/bugs/bug.php?id=20297
[pear-20346]: https://pear.php.net/bugs/bug.php?id=20346
[pear-20355]: https://pear.php.net/bugs/bug.php?id=20355

## [1.5.4] - 2014-08-06

### Changed
- Removed use of sys_get_temp_dir() as this is not supported by the min PHP version
- The installed_paths config var now accepts relative paths
    - The paths are relative to the PHP_CodeSniffer install directory
    - Thanks to [Weston Ruter][@westonruter] for the patch
- Generic ScopeIndentSniff now accounts for different open tag indents
- PEAR FunctionDeclarationSniff now ignores short arrays when checking indent
    - Thanks to [Daniel Tschinder][@danez] for the patch
- PSR2 FunctionCallSignatureSniff now treats multi-line strings as a single-line argument, like arrays and closures
    - Thanks to [Dawid Nowak][@MacDada] for the patch
- Generic ForbiddenFunctionsSniff now detects calls to functions in the global namespace
    - Thanks to [Ole Martin Handeland][@olemartinorg] for the patch
- Generic LowerCaseConstantSniff and UpperCaseConstantSniff now ignore namespaces beginning with TRUE/FALSE/NULL
    - Thanks to [Renan Gonçalves][@renan] for the patch
- Squiz InlineCommentSniff no longer requires a blank line after post-statement comments (request [#20299][pear-20299])
- Squiz SelfMemberReferenceSniff now works correctly with namespaces
- Tab characters are now encoded in abstract pattern error messages
    - Thanks to [Blaine Schmeisser][@bayleedev] for the patch
- Invalid sniff codes passed to --sniffs now show a friendly error message (request [#20313][pear-20313])
- Generic LineLengthSniff now shows a warning if the iconv module is disabled (request [#20314][pear-20314])
- Source report no longer shows errors if category or sniff names ends in an uppercase error
    - Thanks to [Jonathan Marcil][@jmarcil] for the patch

### Fixed
- Fixed bug [#20268][pear-20268] : Incorrect documentation titles in PEAR documentation
- Fixed bug [#20296][pear-20296] : new array notion in function comma check fails
- Fixed bug [#20307][pear-20307] : PHP_CodeSniffer_Standards_AbstractVariableSniff analyze traits
- Fixed bug [#20308][pear-20308] : Squiz.ValidVariableNameSniff - wrong variable usage
- Fixed bug [#20309][pear-20309] : Use "member variable" term in sniff "processMemberVar" method
- Fixed bug [#20310][pear-20310] : PSR2 does not check for space after function name
- Fixed bug [#20322][pear-20322] : Display rules set to type=error even when suppressing warnings
- Fixed bug [#20323][pear-20323] : PHPCS tries to load sniffs from hidden directories

[pear-20268]: https://pear.php.net/bugs/bug.php?id=20268
[pear-20296]: https://pear.php.net/bugs/bug.php?id=20296
[pear-20299]: https://pear.php.net/bugs/bug.php?id=20299
[pear-20307]: https://pear.php.net/bugs/bug.php?id=20307
[pear-20308]: https://pear.php.net/bugs/bug.php?id=20308
[pear-20309]: https://pear.php.net/bugs/bug.php?id=20309
[pear-20310]: https://pear.php.net/bugs/bug.php?id=20310
[pear-20313]: https://pear.php.net/bugs/bug.php?id=20313
[pear-20314]: https://pear.php.net/bugs/bug.php?id=20314
[pear-20322]: https://pear.php.net/bugs/bug.php?id=20322
[pear-20323]: https://pear.php.net/bugs/bug.php?id=20323

## [2.0.0a2] - 2014-05-01

### Changed
- Added report type --report=info to show information about the checked code to make building a standard easier
    - Checks a number of things, such as what line length you use, and spacing are brackets, but not everything
    - Still highly experimental
- Generic LineLengthSniff now shows warnings for long lines referring to licence and VCS information
    - It previously ignored these lines, but at the expense of performance
- Generic DisallowTabIndent and DisallowSpaceIndent sniffs no longer error when detecting mixed indent types
    - Only the first type of indent found on a line (space or indent) is considered
- Lots of little performance improvements that can add up to a substantial saving over large code bases
    - Added a "length" array index to tokens so you don't need to call strlen() of them, or deal with encoding
    - Can now use isset() to find tokens inside the PHP_CodeSniffer_Tokens static vars instead of in_array()
- Custom reports can now specify a $recordErrors member var; this previously only worked for built-in reports
    - When set to FALSE, error messages will not be recorded and only totals will be returned
    - This can save significant memory while processing a large code base
- Removed dependence on PHP_Timer
- PHP tokenizer now supports DEFAULT statements opened with a T_SEMICOLON
- The Squiz and PHPCS standards have increased the max padding for statement alignment from 8 to 12
- Squiz EchoedStringsSniff now supports statements without a semicolon, such as PHP embedded in HTML
- Squiz DoubleQuoteUsageSniff now properly replaces escaped double quotes when fixing a doubled quoted string
- Improved detection of nested IF statements that use the alternate IF/ENDIF syntax
- PSR1 CamelCapsMethodNameSniff now ignores magic methods
    - Thanks to [Eser Ozvataf][@eser] for the patch
- PSR1 SideEffectsSniff now ignores methods named define()
- PSR1 and PEAR ClassDeclarationSniffs now support traits (request [#20208][pear-20208])
- PSR2 ControlStructureSpacingSniff now allows newlines before/after parentheses
    - Thanks to [Maurus Cuelenaere][@mcuelenaere] for the patch
- PSR2 ControlStructureSpacingSniff now checks TRY and CATCH statements
- Squiz SuperfluousWhitespaceSniff now detects whitespace at the end of block comment lines
    - Thanks to [Klaus Purer][@klausi] for the patch
- Squiz LowercasePHPFunctionsSniff no longer reports errors for namespaced functions
    - Thanks to [Max Galbusera][@maxgalbu] for the patch
- Squiz SwitchDeclarationSniff now allows exit() as a breaking statement for case/default
- Squiz ValidVariableNameSniff and Zend ValidVariableNameSniff now ignore additional PHP reserved vars
    - Thanks to Mikuláš Dítě and Adrian Crepaz for the patch
- Sniff code Squiz.WhiteSpace.MemberVarSpacing.After changed to Squiz.WhiteSpace.MemberVarSpacing.Incorrect (request [#20241][pear-20241])

### Fixed
- Fixed bug [#20200][pear-20200] : Invalid JSON produced with specific error message
- Fixed bug [#20204][pear-20204] : Ruleset exclude checks are case sensitive
- Fixed bug [#20213][pear-20213] : Invalid error, Inline IF must be declared on single line
- Fixed bug [#20225][pear-20225] : array_merge() that takes more than one line generates error
- Fixed bug [#20230][pear-20230] : Squiz ControlStructureSpacing sniff assumes specific condition formatting
- Fixed bug [#20234][pear-20234] : phpcbf patch command absolute paths
- Fixed bug [#20240][pear-20240] : Squiz block comment sniff fails when newline present
- Fixed bug [#20247][pear-20247] : The Squiz.WhiteSpace.ControlStructureSpacing sniff and do-while
    - Thanks to [Alexander Obuhovich][@aik099] for the patch
- Fixed bug [#20248][pear-20248] : The Squiz_Sniffs_WhiteSpace_ControlStructureSpacingSniff sniff and empty scope
- Fixed bug [#20252][pear-20252] : Unitialized string offset when package name starts with underscore

[pear-20234]: https://pear.php.net/bugs/bug.php?id=20234

## [1.5.3] - 2014-05-01

### Changed
- Improved detection of nested IF statements that use the alternate IF/ENDIF syntax
- PHP tokenizer now supports DEFAULT statements opened with a T_SEMICOLON
- PSR1 CamelCapsMethodNameSniff now ignores magic methods
    - Thanks to [Eser Ozvataf][@eser] for the patch
- PSR1 SideEffectsSniff now ignores methods named define()
- PSR1 and PEAR ClassDeclarationSniffs now support traits (request [#20208][pear-20208])
- PSR2 ControlStructureSpacingSniff now allows newlines before/after parentheses
    - Thanks to [Maurus Cuelenaere][@mcuelenaere] for the patch
- Squiz LowercasePHPFunctionsSniff no longer reports errors for namespaced functions
    - Thanks to [Max Galbusera][@maxgalbu] for the patch
- Squiz SwitchDeclarationSniff now allows exit() as a breaking statement for case/default
- Squiz ValidVariableNameSniff and Zend ValidVariableNameSniff now ignore additional PHP reserved vars
    - Thanks to Mikuláš Dítě and Adrian Crepaz for the patch
- Sniff code Squiz.WhiteSpace.MemberVarSpacing.After changed to Squiz.WhiteSpace.MemberVarSpacing.Incorrect (request [#20241][pear-20241])

### Fixed
- Fixed bug [#20200][pear-20200] : Invalid JSON produced with specific error message
- Fixed bug [#20204][pear-20204] : Ruleset exclude checks are case sensitive
- Fixed bug [#20213][pear-20213] : Invalid error, Inline IF must be declared on single line
- Fixed bug [#20225][pear-20225] : array_merge() that takes more than one line generates error
- Fixed bug [#20230][pear-20230] : Squiz ControlStructureSpacing sniff assumes specific condition formatting
- Fixed bug [#20240][pear-20240] : Squiz block comment sniff fails when newline present
- Fixed bug [#20247][pear-20247] : The Squiz.WhiteSpace.ControlStructureSpacing sniff and do-while
    - Thanks to [Alexander Obuhovich][@aik099] for the patch
- Fixed bug [#20248][pear-20248] : The Squiz_Sniffs_WhiteSpace_ControlStructureSpacingSniff sniff and empty scope
- Fixed bug [#20252][pear-20252] : Uninitialized string offset when package name starts with underscore

[pear-20200]: https://pear.php.net/bugs/bug.php?id=20200
[pear-20204]: https://pear.php.net/bugs/bug.php?id=20204
[pear-20208]: https://pear.php.net/bugs/bug.php?id=20208
[pear-20213]: https://pear.php.net/bugs/bug.php?id=20213
[pear-20225]: https://pear.php.net/bugs/bug.php?id=20225
[pear-20230]: https://pear.php.net/bugs/bug.php?id=20230
[pear-20240]: https://pear.php.net/bugs/bug.php?id=20240
[pear-20241]: https://pear.php.net/bugs/bug.php?id=20241
[pear-20247]: https://pear.php.net/bugs/bug.php?id=20247
[pear-20248]: https://pear.php.net/bugs/bug.php?id=20248
[pear-20252]: https://pear.php.net/bugs/bug.php?id=20252

## [2.0.0a1] - 2014-02-05

### Changed
- Added the phpcbf script to automatically fix many errors found by the phpcs script
- Added report type --report=diff to show suggested changes to fix coding standard violations
- The --report argument now allows for custom reports to be used
    - Use the full path to your custom report class as the report name
- The --extensions argument is now respected when passing filenames; not just with directories
- The --extensions argument now allows you to specify the tokenizer for each extension
    - e.g., `--extensions=module/php,es/js`
- Command line arguments can now be set in ruleset files
    - e.g., `arg name="report" value="summary"` (print summary report; same as `--report=summary`)
    - e.g., `arg value="sp"` (print source and progress information; same as `-sp`)
    - The `-vvv`, `--sniffs`, `--standard` and `-l` command line arguments cannot be set in this way
- Sniff process() methods can now optionally return a token to ignore up to
    - If returned, the sniff will not be executed again until the passed token is reached in the file
    - Useful if you are looking for tokens like T_OPEN_TAG but only want to process the first one
- Removed the comment parser classes and replaced it with a simple comment tokenizer
    - T_DOC_COMMENT tokens are now tokenized into T_DOC_COMMENT_* tokens so they can be used more easily
    - This change requires a significant rewrite of sniffs that use the comment parser
    - This change requires minor changes to sniffs that listen for T_DOC_COMMENT tokens directly
- Added Generic DocCommentSniff to check generic doc block formatting
    - Removed doc block formatting checks from PEAR ClassCommentSniff
    - Removed doc block formatting checks from PEAR FileCommentSniff
    - Removed doc block formatting checks from PEAR FunctionCommentSniff
    - Removed doc block formatting checks from Squiz ClassCommentSniff
    - Removed doc block formatting checks from Squiz FileCommentSniff
    - Removed doc block formatting checks from Squiz FunctionCommentSniff
    - Removed doc block formatting checks from Squiz VariableCommentSniff
- Squiz DocCommentAlignmentSniff has had its error codes changed
    - NoSpaceBeforeTag becomes NoSpaceAfterStar
    - SpaceBeforeTag becomes SpaceAfterStar
    - SpaceBeforeAsterisk becomes SpaceBeforeStar
- Generic MultipleStatementAlignment now aligns assignments within a block so they fit within their max padding setting
    - The sniff previously requested the padding as 1 space if max padding was exceeded
    - It now aligns the assignment with surrounding assignments if it can
    - Removed property ignoreMultiline as multi-line assignments are now handled correctly and should not be ignored
- Squiz FunctionClosingBraceSpaceSniff now requires a blank line before the brace in all cases except function args
- Added error Squiz.Commenting.ClassComment.SpacingAfter to ensure there are no blank lines after a class comment
- Added error Squiz.WhiteSpace.MemberVarSpacing.AfterComment to ensure there are no blank lines after a member var comment
    - Fixes have also been corrected to not strip the member var comment or indent under some circumstances
    - Thanks to [Mark Scherer][@dereuromark] for help with this fix
- Added error Squiz.Commenting.FunctionCommentThrowTag.Missing to ensure a throw is documented
- Removed error Squiz.Commenting.FunctionCommentThrowTag.WrongType
- Content passed via STDIN can now specify the filename to use so that sniffs can run the correct filename checks
    - Ensure the first line of the content is: phpcs_input_file: /path/to/file
- Squiz coding standard now enforces no closing PHP tag at the end of a pure PHP file
- Squiz coding standard now enforces a single newline character at the end of the file
- Squiz ClassDeclarationSniff no longer checks for a PHP ending tag after a class definition
- Squiz ControlStructureSpacingSniff now checks TRY and CATCH statements as well
- Removed MySource ChannelExceptionSniff

## [1.5.2] - 2014-02-05

### Changed
- Improved support for the PHP 5.5. classname::class syntax
    - PSR2 SwitchDeclarationSniff no longer throws errors when this syntax is used in CASE conditions
- Improved support for negative checks of instanceOf in Squiz ComparisonOperatorUsageSniff
    - Thanks to [Martin Winkel][@storeman] for the patch
- Generic FunctionCallArgumentSpacingSniff now longer complains about space before comma when using here/nowdocs
    - Thanks to [Richard van Velzen][@rvanvelzen] for the patch
- Generic LowerCaseConstantSniff and UpperCaseConstantSniff now ignore class constants
    - Thanks to [Kristopher Wilson][@mrkrstphr] for the patch
- PEAR FunctionCallSignatureSniff now has settings to specify how many spaces should appear before/after parentheses
    - Override the 'requiredSpacesAfterOpen' and 'requiredSpacesBeforeClose' settings in a ruleset.xml file to change
    - Default remains at 0 for both
    - Thanks to [Astinus Eberhard][@Astinus-Eberhard] for the patch
- PSR2 ControlStructureSpacingSniff now has settings to specify how many spaces should appear before/after parentheses
    - Override the 'requiredSpacesAfterOpen' and 'requiredSpacesBeforeClose' settings in a ruleset.xml file to change
    - Default remains at 0 for both
    - Thanks to [Astinus Eberhard][@Astinus-Eberhard] for the patch
- Squiz ForEachLoopDeclarationSniff now has settings to specify how many spaces should appear before/after parentheses
    - Override the 'requiredSpacesAfterOpen' and 'requiredSpacesBeforeClose' settings in a ruleset.xml file to change
    - Default remains at 0 for both
    - Thanks to [Astinus Eberhard][@Astinus-Eberhard] for the patch
- Squiz ForLoopDeclarationSniff now has settings to specify how many spaces should appear before/after parentheses
    - Override the 'requiredSpacesAfterOpen' and 'requiredSpacesBeforeClose' settings in a ruleset.xml file to change
    - Default remains at 0 for both
    - Thanks to [Astinus Eberhard][@Astinus-Eberhard] for the patch
- Squiz FunctionDeclarationArgumentSpacingSniff now has settings to specify how many spaces should appear before/after parentheses
    - Override the 'requiredSpacesAfterOpen' and 'requiredSpacesBeforeClose' settings in a ruleset.xml file to change
    - Default remains at 0 for both
    - Thanks to [Astinus Eberhard][@Astinus-Eberhard] for the patch
- Removed UnusedFunctionParameter, CyclomaticComplexity and NestingLevel from the Squiz standard
- Generic FixmeSniff and TodoSniff now work correctly with accented characters

### Fixed
- Fixed bug [#20145][pear-20145] : Custom ruleset preferences directory over installed standard
- Fixed bug [#20147][pear-20147] : phpcs-svn-pre-commit - no more default error report
- Fixed bug [#20151][pear-20151] : Problem handling "if(): ... else: ... endif;" syntax
- Fixed bug [#20190][pear-20190] : Invalid regex in Squiz_Sniffs_WhiteSpace_SuperfluousWhitespaceSniff

[pear-20145]: https://pear.php.net/bugs/bug.php?id=20145
[pear-20147]: https://pear.php.net/bugs/bug.php?id=20147
[pear-20151]: https://pear.php.net/bugs/bug.php?id=20151
[pear-20190]: https://pear.php.net/bugs/bug.php?id=20190

## [1.5.1] - 2013-12-12

### Changed
- Config values can now be set at runtime using the command line argument `--runtime-set key value`
    - Runtime values are the same as config values, but are not written to the main config file
    - Thanks to [Wim Godden][@wimg] for the patch
- Config values can now be set in ruleset files
    - e.g., config name="zend_ca_path" value="/path/to/ZendCodeAnalyzer"
    - Can not be used to set config values that override command line values, such as show_warnings
    - Thanks to [Jonathan Marcil][@jmarcil] for helping with the patch
- Added a new installed_paths config value to allow for the setting of directories that contain standards
    - By default, standards have to be installed into the CodeSniffer/Standards directory to be considered installed
    - New config value allows a list of paths to be set in addition to this internal path
    - Installed standards appear when using the -i arg, and can be referenced in rulesets using only their name
    - Set paths by running: phpcs --config-set installed_paths /path/one,/path/two,...
- PSR2 ClassDeclarationSniff now allows a list of extended interfaces to be split across multiple lines
- Squiz DoubleQuoteUsageSniff now allows \b in double quoted strings
- Generic ForbiddenFunctionsSniff now ignores object creation
    - This is a further fix for bug [#20100][pear-20100] : incorrect Function mysql() has been deprecated report

### Fixed
- Fixed bug [#20136][pear-20136] : Squiz_Sniffs_WhiteSpace_ScopeKeywordSpacingSniff and Traits
- Fixed bug [#20138][pear-20138] : Protected property underscore and camel caps issue (in trait with Zend)
    - Thanks to [Gaetan Rousseau][@Naelyth] for the patch
- Fixed bug [#20139][pear-20139] : No report file generated on success

[pear-20136]: https://pear.php.net/bugs/bug.php?id=20136
[pear-20138]: https://pear.php.net/bugs/bug.php?id=20138
[pear-20139]: https://pear.php.net/bugs/bug.php?id=20139

## [1.5.0] - 2013-11-28

### Changed
- Doc generation is now working again for installed standards
    - Includes a fix for limiting the docs to specific sniffs
- Generic ScopeIndentSniff now allows for ignored tokens to be set via ruleset.xml files
    - E.g., to ignore comments, override a property using:
    - name="ignoreIndentationTokens" type="array" value="T_COMMENT,T_DOC_COMMENT"
- PSR2 standard now ignores comments when checking indentation rules
- Generic UpperCaseConstantNameSniff no longer reports errors where constants are used (request [#20090][pear-20090])
    - It still reports errors where constants are defined
- Individual messages can now be excluded in ruleset.xml files using the exclude tag (request [#20091][pear-20091])
    - Setting message severity to 0 continues to be supported
- Squiz OperatorSpacingSniff no longer throws errors for the ?: short ternary operator
    - Thanks to [Antoine Musso][@hashar] for the patch
- Comment parser now supports non-English characters when splitting comment lines into words
    - Thanks to [Nik Sun][@CandySunPlus] for the patch
- Exit statements are now recognised as valid closers for CASE and DEFAULT blocks
    - Thanks to [Maksim Kochkin][@ksimka] for the patch
- PHP_CodeSniffer_CLI::process() can now be passed an incomplete array of CLI values
    - Missing values will be set to the CLI defaults
    - Thanks to [Maksim Kochkin][@ksimka] for the patch

### Fixed
- Fixed bug [#20093][pear-20093] : Bug with ternary operator token
- Fixed bug [#20097][pear-20097] : `CLI.php` throws error in PHP 5.2
- Fixed bug [#20100][pear-20100] : incorrect Function mysql() has been deprecated report
- Fixed bug [#20119][pear-20119] : PHP warning: invalid argument to str_repeat() in SVN blame report with -s
- Fixed bug [#20123][pear-20123] : PSR2 complains about an empty second statement in for-loop
- Fixed bug [#20131][pear-20131] : PHP errors in svnblame report, if there are files not under version control
- Fixed bug [#20133][pear-20133] : Allow "HG: hg_id" as value for @version tag

[pear-20090]: https://pear.php.net/bugs/bug.php?id=20090
[pear-20091]: https://pear.php.net/bugs/bug.php?id=20091
[pear-20093]: https://pear.php.net/bugs/bug.php?id=20093

## [1.4.8] - 2013-11-26

### Changed
- Generic ScopeIndentSniff now allows for ignored tokens to be set via ruleset.xml files
    - E.g., to ignore comments, override a property using:
    - name="ignoreIndentationTokens" type="array" value="T_COMMENT,T_DOC_COMMENT"
- PSR2 standard now ignores comments when checking indentation rules
- Squiz OperatorSpacingSniff no longer throws errors for the ?: short ternary operator
    - Thanks to [Antoine Musso][@hashar] for the patch
- Comment parser now supports non-English characters when splitting comment lines into words
    - Thanks to [Nik Sun][@CandySunPlus] for the patch
- Exit statements are now recognised as valid closers for CASE and DEFAULT blocks
    - Thanks to [Maksim Kochkin][@ksimka] for the patch
- PHP_CodeSniffer_CLI::process() can now be passed an incomplete array of CLI values
    - Missing values will be set to the CLI defaults
    - Thanks to [Maksim Kochkin][@ksimka] for the patch

### Fixed
- Fixed bug [#20097][pear-20097] : `CLI.php` throws error in PHP 5.2
- Fixed bug [#20100][pear-20100] : incorrect Function mysql() has been deprecated report
- Fixed bug [#20119][pear-20119] : PHP warning: invalid argument to str_repeat() in SVN blame report with -s
- Fixed bug [#20123][pear-20123] : PSR2 complains about an empty second statement in for-loop
- Fixed bug [#20131][pear-20131] : PHP errors in svnblame report, if there are files not under version control
- Fixed bug [#20133][pear-20133] : Allow "HG: hg_id" as value for @version tag

[pear-20097]: https://pear.php.net/bugs/bug.php?id=20097
[pear-20100]: https://pear.php.net/bugs/bug.php?id=20100
[pear-20119]: https://pear.php.net/bugs/bug.php?id=20119
[pear-20123]: https://pear.php.net/bugs/bug.php?id=20123
[pear-20131]: https://pear.php.net/bugs/bug.php?id=20131
[pear-20133]: https://pear.php.net/bugs/bug.php?id=20133

## [1.5.0RC4] - 2013-09-26

### Changed
- You can now restrict violations to individual sniff codes using the --sniffs command line argument
    - Previously, this only restricted violations to an entire sniff and not individual messages
    - If you have scripts calling PHP_CodeSniffer::process() or creating PHP_CodeSniffer_File objects, you must update your code
    - The array of restrictions passed to PHP_CodeSniffer::process() must now be an array of sniff codes instead of class names
    - The PHP_CodeSniffer_File::__construct() method now requires an array of restrictions to be passed
- Doc generation is now working again
- Progress information now shows the percentage complete at the end of each line
- Added report type --report=junit to show the error list in a JUnit compatible format
    - Thanks to [Oleg Lobach][@bladeofsteel] for the contribution
- Added support for the PHP 5.4 callable type hint
- Fixed problem where some file content could be ignored when checking STDIN
- Version information is now printed when installed via composer or run from a Git clone (request [#20050][pear-20050])
- Added Squiz DisallowBooleanStatementSniff to ban boolean operators outside of control structure conditions
- The CSS tokenizer is now more reliable when encountering 'list' and 'break' strings
- Coding standard ignore comments can now appear instead doc blocks as well as inline comments
    - Thanks to [Stuart Langley][@sjlangley] for the patch
- Generic LineLengthSniff now ignores SVN URL and Head URL comments
    - Thanks to [Karl DeBisschop][@kdebisschop] for the patch
- PEAR MultiLineConditionSniff now has a setting to specify how many spaces code should be indented
    - Default remains at 4; override the 'indent' setting in a ruleset.xml file to change
    - Thanks to [Szabolcs Sulik][@blerou] for the patch
- PEAR MultiLineAssignmentSniff now has a setting to specify how many spaces code should be indented
    - Default remains at 4; override the 'indent' setting in a ruleset.xml file to change
    - Thanks to [Szabolcs Sulik][@blerou] for the patch
- PEAR FunctionDeclarationSniff now has a setting to specify how many spaces code should be indented
    - Default remains at 4; override the 'indent' setting in a ruleset.xml file to change
    - Thanks to [Szabolcs Sulik][@blerou] for the patch
- Squiz SwitchDeclarationSniff now has a setting to specify how many spaces code should be indented
    - Default remains at 4; override the 'indent' setting in a ruleset.xml file to change
    - Thanks to [Szabolcs Sulik][@blerou] for the patch
- Squiz CSS IndentationSniff now has a setting to specify how many spaces code should be indented
    - Default remains at 4; override the 'indent' setting in a ruleset.xml file to change
    - Thanks to [Hugo Fonseca][@fonsecas72] for the patch
- Squiz and MySource File and Function comment sniffs now allow all tags and don't require a particular licence
- Squiz standard now allows lines to be 120 characters long before warning; up from 85
- Squiz LowercaseStyleDefinitionSniff no longer throws errors for class names in nested style definitions
- Squiz ClassFileNameSniff no longer throws errors when checking STDIN
- Squiz CSS sniffs no longer generate errors for IE filters
- Squiz CSS IndentationSniff no longer sees comments as blank lines
- Squiz LogicalOperatorSpacingSniff now ignores whitespace at the end of a line
- Squiz.Scope.MethodScope.Missing error message now mentions 'visibility' instead of 'scope modifier'
    - Thanks to [Renat Akhmedyanov][@r3nat] for the patch
- Added support for the PSR2 multi-line arguments errata
- The PSR2 standard no longer throws errors for additional spacing after a type hint
- PSR UseDeclarationSniff no longer throws errors for USE statements inside TRAITs

### Fixed
- Fixed cases where code was incorrectly assigned the T_GOTO_LABEL token when used in a complex CASE condition
- Fixed bug [#20026][pear-20026] : Check for multi-line arrays that should be single-line is slightly wrong
    - Adds new error message for single-line arrays that end with a comma
- Fixed bug [#20029][pear-20029] : ForbiddenFunction sniff incorrectly recognizes methods in USE clauses
- Fixed bug [#20043][pear-20043] : Mis-interpretation of Foo::class
- Fixed bug [#20044][pear-20044] : PSR1 camelCase check does not ignore leading underscores
- Fixed bug [#20045][pear-20045] : Errors about indentation for closures with multi-line 'use' in functions
- Fixed bug [#20051][pear-20051] : Undefined index: scope_opener / scope_closer
    - Thanks to [Anthon Pang][@robocoder] for the patch

[pear-20051]: https://pear.php.net/bugs/bug.php?id=20051

## [1.4.7] - 2013-09-26

### Changed
- Added report type --report=junit to show the error list in a JUnit compatible format
    - Thanks to [Oleg Lobach][@bladeofsteel] for the contribution
- Added support for the PHP 5.4 callable type hint
- Fixed problem where some file content could be ignored when checking STDIN
- Version information is now printed when installed via composer or run from a Git clone (request [#20050][pear-20050])
- The CSS tokenizer is now more reliable when encountering 'list' and 'break' strings
- Coding standard ignore comments can now appear instead doc blocks as well as inline comments
    - Thanks to [Stuart Langley][@sjlangley] for the patch
- Generic LineLengthSniff now ignores SVN URL and Head URL comments
    - Thanks to [Karl DeBisschop][@kdebisschop] for the patch
- PEAR MultiLineConditionSniff now has a setting to specify how many spaces code should be indented
    - Default remains at 4; override the 'indent' setting in a ruleset.xml file to change
    - Thanks to [Szabolcs Sulik][@blerou] for the patch
- PEAR MultiLineAssignmentSniff now has a setting to specify how many spaces code should be indented
    - Default remains at 4; override the 'indent' setting in a ruleset.xml file to change
    - Thanks to [Szabolcs Sulik][@blerou] for the patch
- PEAR FunctionDeclarationSniff now has a setting to specify how many spaces code should be indented
    - Default remains at 4; override the 'indent' setting in a ruleset.xml file to change
    - Thanks to [Szabolcs Sulik][@blerou] for the patch
- Squiz SwitchDeclarationSniff now has a setting to specify how many spaces code should be indented
    - Default remains at 4; override the 'indent' setting in a ruleset.xml file to change
    - Thanks to [Szabolcs Sulik][@blerou] for the patch
- Squiz CSS IndentationSniff now has a setting to specify how many spaces code should be indented
    - Default remains at 4; override the 'indent' setting in a ruleset.xml file to change
    - Thanks to [Hugo Fonseca][@fonsecas72] for the patch
- Squiz and MySource File and Function comment sniffs now allow all tags and don't require a particular licence
- Squiz LowercaseStyleDefinitionSniff no longer throws errors for class names in nested style definitions
- Squiz ClassFileNameSniff no longer throws errors when checking STDIN
- Squiz CSS sniffs no longer generate errors for IE filters
- Squiz CSS IndentationSniff no longer sees comments as blank lines
- Squiz LogicalOperatorSpacingSniff now ignores whitespace at the end of a line
- Squiz.Scope.MethodScope.Missing error message now mentions 'visibility' instead of 'scope modifier'
    - Thanks to [Renat Akhmedyanov][@r3nat] for the patch
- Added support for the PSR2 multi-line arguments errata
- The PSR2 standard no longer throws errors for additional spacing after a type hint
- PSR UseDeclarationSniff no longer throws errors for USE statements inside TRAITs

### Fixed
- Fixed bug [#20026][pear-20026] : Check for multi-line arrays that should be single-line is slightly wrong
    - Adds new error message for single-line arrays that end with a comma
- Fixed bug [#20029][pear-20029] : ForbiddenFunction sniff incorrectly recognizes methods in USE clauses
- Fixed bug [#20043][pear-20043] : Mis-interpretation of Foo::class
- Fixed bug [#20044][pear-20044] : PSR1 camelCase check does not ignore leading underscores
- Fixed bug [#20045][pear-20045] : Errors about indentation for closures with multi-line 'use' in functions

[pear-20026]: https://pear.php.net/bugs/bug.php?id=20026
[pear-20029]: https://pear.php.net/bugs/bug.php?id=20029
[pear-20043]: https://pear.php.net/bugs/bug.php?id=20043
[pear-20044]: https://pear.php.net/bugs/bug.php?id=20044
[pear-20045]: https://pear.php.net/bugs/bug.php?id=20045
[pear-20050]: https://pear.php.net/bugs/bug.php?id=20050

## [1.5.0RC3] - 2013-07-25

### Changed
- Added report type --report=json to show the error list and total counts for all checked files
    - Thanks to [Jeffrey Fisher][@jeffslofish] for the contribution
- PHP_CodeSniffer::isCamelCaps now allows for acronyms at the start of a string if the strict flag is FALSE
    - acronyms are defined as at least 2 uppercase characters in a row
    - e.g., the following is now valid camel caps with strict set to FALSE: XMLParser
- The PHP tokenizer now tokenizes goto labels as T_GOTO_LABEL instead of T_STRING followed by T_COLON
- The JS tokenizer now has support for the T_THROW token
- Symlinked directories inside CodeSniffer/Standards and in ruleset.xml files are now supported
    - Only available since PHP 5.2.11 and 5.3.1
    - Thanks to [Maik Penz][@goatherd] for the patch
- The JS tokenizer now correctly identifies T_INLINE_ELSE tokens instead of leaving them as T_COLON
    - Thanks to [Arnout Boks][@aboks] for the patch
- Explaining a standard (phpcs -e) that uses namespaces now works correctly
- Restricting a check to specific sniffs (phpcs --sniffs=...) now works correctly with namespaced sniffs
    - Thanks to [Maik Penz][@goatherd] for the patch
- Docs added for the entire Generic standard, and many sniffs from other standards are now documented as well
    - Thanks to [Spencer Rinehart][@nubs] for the contribution
- Clearer error message for when the sniff class name does not match the directory structure
- Generated HTML docs now correctly show the open PHP tag in code comparison blocks
- Added Generic InlineHTMLSniff to ensure a file only contains PHP code
- Added Squiz ShorthandSizeSniff to check that CSS sizes are using shorthand notation only when 1 or 2 values are used
- Added Squiz ForbiddenStylesSniff to ban the use of some deprecated browser-specific styles
- Added Squiz NamedColoursSniff to ban the use of colour names
- PSR2 standard no longer enforces no whitespace between the closing parenthesis of a function call and the semicolon
- PSR2 ClassDeclarationSniff now ignores empty classes when checking the end brace position
- PSR2 SwitchDeclarationSniff no longer reports errors for empty lines between CASE statements
- PEAR ObjectOperatorIndentSniff now has a setting to specify how many spaces code should be indented
    - Default remains at 4; override the indent setting in a ruleset.xml file to change
    - Thanks to [Andrey Mindubaev][@covex-nn] for the patch
- Squiz FileExtensionSniff now supports traits
    - Thanks to [Lucas Green][@mythril] for the patch
- Squiz ArrayDeclarationSniff no longer reports errors for no comma at the end of a line that contains a function call
- Squiz SwitchDeclarationSniff now supports T_CONTINUE and T_THROW as valid case/default breaking statements
- Squiz CommentedOutCodeSniff is now better at ignoring commented out HTML, XML and regular expressions
- Squiz DisallowComparisonAssignmentSniff no longer throws errors for the third expression in a FOR statement
- Squiz ColourDefinitionSniff no longer throws errors for some CSS class names
- Squiz ControlStructureSpacingSniff now supports all types of CASE/DEFAULT breaking statements
- Generic CallTimePassByReferenceSniff now reports errors for functions called using a variable
    - Thanks to [Maik Penz][@goatherd] for the patch
- Generic ConstructorNameSniff no longer throws a notice for abstract constructors inside abstract classes
    - Thanks to [Spencer Rinehart][@nubs] for the patch
- Squiz ComparisonOperatorUsageSniff now checks inside elseif statements
    - Thanks to [Arnout Boks][@aboks] for the patch
- Squiz OperatorSpacingSniff now reports errors for no spacing around inline then and else tokens
    - Thanks to [Arnout Boks][@aboks] for the patch

### Fixed
- Fixed bug [#19811][pear-19811] : Comments not ignored in all cases in AbstractPatternSniff
    - Thanks to [Erik Wiffin][@erikwiffin] for the patch
- Fixed bug [#19892][pear-19892] : ELSE with no braces causes incorrect SWITCH break statement indentation error
- Fixed bug [#19897][pear-19897] : Indenting warnings in templates not consistent
- Fixed bug [#19908][pear-19908] : PEAR MultiLineCondition Does Not Apply elseif
- Fixed bug [#19930][pear-19930] : option --report-file generate an empty file
- Fixed bug [#19935][pear-19935] : notify-send reports do not vanish in gnome-shell
    - Thanks to [Christian Weiske][@cweiske] for the patch
- Fixed bug [#19944][pear-19944] : docblock squiz sniff "return void" trips over return in lambda function
- Fixed bug [#19953][pear-19953] : PSR2 - Spaces before interface name for abstract class
- Fixed bug [#19956][pear-19956] : phpcs warns for Type Hint missing Resource
- Fixed bug [#19957][pear-19957] : Does not understand trait method aliasing
- Fixed bug [#19968][pear-19968] : Permission denied on excluded directory
- Fixed bug [#19969][pear-19969] : Sniffs with namespace not recognized in reports
- Fixed bug [#19997][pear-19997] : Class names incorrectly detected as constants

[pear-19930]: https://pear.php.net/bugs/bug.php?id=19930

## [1.4.6] - 2013-07-25

### Changed
- Added report type --report=json to show the error list and total counts for all checked files
    - Thanks to [Jeffrey Fisher][@jeffslofish] for the contribution
- The JS tokenizer now has support for the T_THROW token
- Symlinked directories inside CodeSniffer/Standards and in ruleset.xml files are now supported
    - Only available since PHP 5.2.11 and 5.3.1
    - Thanks to [Maik Penz][@goatherd] for the patch
- The JS tokenizer now correctly identifies T_INLINE_ELSE tokens instead of leaving them as T_COLON
    - Thanks to [Arnout Boks][@aboks] for the patch
- Explaining a standard (phpcs -e) that uses namespaces now works correctly
- Restricting a check to specific sniffs (phpcs --sniffs=...) now works correctly with namespaced sniffs
    - Thanks to [Maik Penz][@goatherd] for the patch
- Docs added for the entire Generic standard, and many sniffs from other standards are now documented as well
    - Thanks to [Spencer Rinehart][@nubs] for the contribution
- Clearer error message for when the sniff class name does not match the directory structure
- Generated HTML docs now correctly show the open PHP tag in code comparison blocks
- Added Generic InlineHTMLSniff to ensure a file only contains PHP code
- Added Squiz ShorthandSizeSniff to check that CSS sizes are using shorthand notation only when 1 or 2 values are used
- Added Squiz ForbiddenStylesSniff to ban the use of some deprecated browser-specific styles
- Added Squiz NamedColoursSniff to ban the use of colour names
- PSR2 standard no longer enforces no whitespace between the closing parenthesis of a function call and the semicolon
- PSR2 ClassDeclarationSniff now ignores empty classes when checking the end brace position
- PSR2 SwitchDeclarationSniff no longer reports errors for empty lines between CASE statements
- PEAR ObjectOperatorIndentSniff now has a setting to specify how many spaces code should be indented
    - Default remains at 4; override the indent setting in a ruleset.xml file to change
    - Thanks to [Andrey Mindubaev][@covex-nn] for the patch
- Squiz FileExtensionSniff now supports traits
    - Thanks to [Lucas Green][@mythril] for the patch
- Squiz ArrayDeclarationSniff no longer reports errors for no comma at the end of a line that contains a function call
- Squiz SwitchDeclarationSniff now supports T_CONTINUE and T_THROW as valid case/default breaking statements
- Squiz CommentedOutCodeSniff is now better at ignoring commented out HTML, XML and regular expressions
- Squiz DisallowComparisonAssignmentSniff no longer throws errors for the third expression in a FOR statement
- Squiz ColourDefinitionSniff no longer throws errors for some CSS class names
- Squiz ControlStructureSpacingSniff now supports all types of CASE/DEFAULT breaking statements
- Generic CallTimePassByReferenceSniff now reports errors for functions called using a variable
    - Thanks to [Maik Penz][@goatherd] for the patch
- Generic ConstructorNameSniff no longer throws a notice for abstract constructors inside abstract classes
    - Thanks to [Spencer Rinehart][@nubs] for the patch
- Squiz ComparisonOperatorUsageSniff now checks inside elseif statements
    - Thanks to [Arnout Boks][@aboks] for the patch
- Squiz OperatorSpacingSniff now reports errors for no spacing around inline then and else tokens
    - Thanks to [Arnout Boks][@aboks] for the patch

### Fixed
- Fixed bug [#19811][pear-19811] : Comments not ignored in all cases in AbstractPatternSniff
    - Thanks to [Erik Wiffin][@erikwiffin] for the patch
- Fixed bug [#19892][pear-19892] : ELSE with no braces causes incorrect SWITCH break statement indentation error
- Fixed bug [#19897][pear-19897] : Indenting warnings in templates not consistent
- Fixed bug [#19908][pear-19908] : PEAR MultiLineCondition Does Not Apply elseif
- Fixed bug [#19913][pear-19913] : Running phpcs in interactive mode causes warnings
    - Thanks to [Harald Franndorfer][pear-gemineye] for the patch
- Fixed bug [#19935][pear-19935] : notify-send reports do not vanish in gnome-shell
    - Thanks to [Christian Weiske][@cweiske] for the patch
- Fixed bug [#19944][pear-19944] : docblock squiz sniff "return void" trips over return in lambda function
- Fixed bug [#19953][pear-19953] : PSR2 - Spaces before interface name for abstract class
- Fixed bug [#19956][pear-19956] : phpcs warns for Type Hint missing Resource
- Fixed bug [#19957][pear-19957] : Does not understand trait method aliasing
- Fixed bug [#19968][pear-19968] : Permission denied on excluded directory
- Fixed bug [#19969][pear-19969] : Sniffs with namespace not recognized in reports
- Fixed bug [#19997][pear-19997] : Class names incorrectly detected as constants

[pear-19811]: https://pear.php.net/bugs/bug.php?id=19811
[pear-19892]: https://pear.php.net/bugs/bug.php?id=19892
[pear-19897]: https://pear.php.net/bugs/bug.php?id=19897
[pear-19908]: https://pear.php.net/bugs/bug.php?id=19908
[pear-19913]: https://pear.php.net/bugs/bug.php?id=19913
[pear-19935]: https://pear.php.net/bugs/bug.php?id=19935
[pear-19944]: https://pear.php.net/bugs/bug.php?id=19944
[pear-19953]: https://pear.php.net/bugs/bug.php?id=19953
[pear-19956]: https://pear.php.net/bugs/bug.php?id=19956
[pear-19957]: https://pear.php.net/bugs/bug.php?id=19957
[pear-19968]: https://pear.php.net/bugs/bug.php?id=19968
[pear-19969]: https://pear.php.net/bugs/bug.php?id=19969
[pear-19997]: https://pear.php.net/bugs/bug.php?id=19997

## [1.5.0RC2] - 2013-04-04

### Changed
- Ruleset processing has been rewritten to be more predictable
    - Provides much better support for relative paths inside ruleset files
    - May mean that sniffs that were previously ignored are now being included when importing external rulesets
    - Ruleset processing output can be seen by using the -vv command line argument
    - Internal sniff registering functions have all changed, so please review custom scripts
- You can now pass multiple coding standards on the command line, comma separated (request [#19144][pear-19144])
    - Works with built-in or custom standards and rulesets, or a mix of both
- You can now exclude directories or whole standards in a ruleset XML file (request [#19731][pear-19731])
    - e.g., exclude "Generic.Commenting" or just "Generic"
    - You can also pass in a path to a directory instead, if you know it
- Added Generic LowerCaseKeywordSniff to ensure all PHP keywords are defined in lowercase
    - The PSR2 and Squiz standards now use this sniff
- Added Generic SAPIUsageSniff to ensure the `PHP_SAPI` constant is used instead of `php_sapi_name()` (request [#19863][pear-19863])
- Squiz FunctionSpacingSniff now has a setting to specify how many lines there should between functions (request [#19843][pear-19843])
    - Default remains at 2
    - Override the "spacing" setting in a ruleset.xml file to change
- Squiz LowercasePHPFunctionSniff no longer throws errors for the limited set of PHP keywords it was checking
    - Add a rule for Generic.PHP.LowerCaseKeyword to your ruleset to replicate this functionality
- Added support for the PHP 5.4 T_CALLABLE token so it can be used in lower PHP versions
- Generic EndFileNoNewlineSniff now supports checking of CSS and JS files
- PSR2 SwitchDeclarationSniff now has a setting to specify how many spaces code should be indented
    - Default remains at 4; override the indent setting in a ruleset.xml file to change
    - Thanks to [Asher Snyder][@asnyder] for the patch
- Generic ScopeIndentSniff now has a setting to specify a list of tokens that should be ignored
    - The first token on the line is checked and the whole line is ignored if the token is in the array
    - Thanks to [Eloy Lafuente][@stronk7] for the patch
- Squiz LowercaseClassKeywordsSniff now checks for the TRAIT keyword
    - Thanks to [Anthon Pang][@robocoder] for the patch
- If you create your own PHP_CodeSniffer object, PHPCS will no longer exit when an unknown argument is found
    - This allows you to create wrapper scripts for PHPCS more easily
- PSR2 MethodDeclarationSniff no longer generates a notice for methods named "_"
    - Thanks to [Bart S][@zBart] for the patch
- Squiz BlockCommentSniff no longer reports that a blank line between a scope closer and block comment is invalid
- Generic DuplicateClassNameSniff no longer reports an invalid error if multiple PHP open tags exist in a file
- Generic DuplicateClassNameSniff no longer reports duplicate errors if multiple PHP open tags exist in a file

### Fixed
- Fixed bug [#19819][pear-19819] : Freeze with syntax error in use statement
- Fixed bug [#19820][pear-19820] : Wrong message level in Generic_Sniffs_CodeAnalysis_EmptyStatementSniff
- Fixed bug [#19859][pear-19859] : CodeSniffer::setIgnorePatterns API changed
- Fixed bug [#19871][pear-19871] : findExtendedClassName doesn't return FQCN on namespaced classes
- Fixed bug [#19879][pear-19879] : bitwise and operator interpreted as reference by value

[pear-19144]: https://pear.php.net/bugs/bug.php?id=19144
[pear-19731]: https://pear.php.net/bugs/bug.php?id=19731

## [1.4.5] - 2013-04-04

### Changed
- Added Generic LowerCaseKeywordSniff to ensure all PHP keywords are defined in lowercase
    - The PSR2 and Squiz standards now use this sniff
- Added Generic SAPIUsageSniff to ensure the `PHP_SAPI` constant is used instead of `php_sapi_name()` (request [#19863][pear-19863])
- Squiz FunctionSpacingSniff now has a setting to specify how many lines there should between functions (request [#19843][pear-19843])
    - Default remains at 2
    - Override the "spacing" setting in a ruleset.xml file to change
- Squiz LowercasePHPFunctionSniff no longer throws errors for the limited set of PHP keywords it was checking
    - Add a rule for Generic.PHP.LowerCaseKeyword to your ruleset to replicate this functionality
- Added support for the PHP 5.4 T_CALLABLE token so it can be used in lower PHP versions
- Generic EndFileNoNewlineSniff now supports checking of CSS and JS files
- PSR2 SwitchDeclarationSniff now has a setting to specify how many spaces code should be indented
    - Default remains at 4; override the indent setting in a ruleset.xml file to change
    - Thanks to [Asher Snyder][@asnyder] for the patch
- Generic ScopeIndentSniff now has a setting to specify a list of tokens that should be ignored
    - The first token on the line is checked and the whole line is ignored if the token is in the array
    - Thanks to [Eloy Lafuente][@stronk7] for the patch
- Squiz LowercaseClassKeywordsSniff now checks for the TRAIT keyword
    - Thanks to [Anthon Pang][@robocoder] for the patch
- If you create your own PHP_CodeSniffer object, PHPCS will no longer exit when an unknown argument is found
    - This allows you to create wrapper scripts for PHPCS more easily
- PSR2 MethodDeclarationSniff no longer generates a notice for methods named "_"
    - Thanks to [Bart S][@zBart] for the patch
- Squiz BlockCommentSniff no longer reports that a blank line between a scope closer and block comment is invalid
- Generic DuplicateClassNameSniff no longer reports an invalid error if multiple PHP open tags exist in a file
- Generic DuplicateClassNameSniff no longer reports duplicate errors if multiple PHP open tags exist in a file

### Fixed
- Fixed bug [#19819][pear-19819] : Freeze with syntax error in use statement
- Fixed bug [#19820][pear-19820] : Wrong message level in Generic_Sniffs_CodeAnalysis_EmptyStatementSniff
- Fixed bug [#19859][pear-19859] : CodeSniffer::setIgnorePatterns API changed
- Fixed bug [#19871][pear-19871] : findExtendedClassName doesn't return FQCN on namespaced classes
- Fixed bug [#19879][pear-19879] : bitwise and operator interpreted as reference by value

[pear-19819]: https://pear.php.net/bugs/bug.php?id=19819
[pear-19820]: https://pear.php.net/bugs/bug.php?id=19820
[pear-19843]: https://pear.php.net/bugs/bug.php?id=19843
[pear-19859]: https://pear.php.net/bugs/bug.php?id=19859
[pear-19863]: https://pear.php.net/bugs/bug.php?id=19863
[pear-19871]: https://pear.php.net/bugs/bug.php?id=19871
[pear-19879]: https://pear.php.net/bugs/bug.php?id=19879

## [1.5.0RC1] - 2013-02-08

### Changed
- Reports have been completely rewritten to consume far less memory
    - Each report is incrementally written to the file system during a run and then printed out when the run ends
    - There is no longer a need to keep the list of errors and warnings in memory during a run
- Multi-file sniff support has been removed because they are too memory intensive
    - If you have a custom multi-file sniff, you can convert it into a standard sniff quite easily
    - See `CodeSniffer/Standards/Generic/Sniffs/Classes/DuplicateClassNameSniff.php` for an example

## [1.4.4] - 2013-02-07

### Changed
- Ignored lines no longer cause the summary report to show incorrect error and warning counts
    - Thanks to [Bert Van Hauwaert][@becoded] for the patch
- Added Generic CSSLintSniff to run CSSLint over a CSS file and report warnings
    - Set full command to run CSSLint using phpcs --config-set csslint_path /path/to/csslint
    - Thanks to [Roman Levishchenko][@index0h] for the contribution
- Added PSR2 ControlStructureSpacingSniff to ensure there are no spaces before and after parenthesis in control structures
    - Fixes bug [#19732][pear-19732] : PSR2: some control structures errors not reported
- Squiz commenting sniffs now support non-English characters when checking for capital letters
    - Thanks to [Roman Levishchenko][@index0h] for the patch
- Generic EndFileNewlineSniff now supports JS and CSS files
    - Thanks to [Denis Ryabkov][@dryabkov] for the patch
- PSR1 SideEffectsSniff no longer reports constant declarations as side effects
- Notifysend report now supports notify-send versions before 0.7.3
    - Thanks to [Ken Guest][@kenguest] for the patch
- PEAR and Squiz FunctionCommentSniffs no longer report errors for misaligned argument comments when they are blank
    - Thanks to [Thomas Peterson][@boonkerz] for the patch
- Squiz FunctionDeclarationArgumentSpacingSniff now works correctly for equalsSpacing values greater than 0
    - Thanks to [Klaus Purer][@klausi] for the patch
- Squiz SuperfluousWhitespaceSniff no longer throws errors for CSS files with no newline at the end
- Squiz SuperfluousWhitespaceSniff now allows a single newline at the end of JS and CSS files

### Fixed
- Fixed bug [#19755][pear-19755] : Token of T_CLASS type has no scope_opener and scope_closer keys
- Fixed bug [#19759][pear-19759] : Squiz.PHP.NonExecutableCode fails for return function()...
- Fixed bug [#19763][pear-19763] : Use statements for traits not recognised correctly for PSR2 code style
- Fixed bug [#19764][pear-19764] : Instead of for traits throws uppercase constant name errors
- Fixed bug [#19772][pear-19772] : PSR2_Sniffs_Namespaces_UseDeclarationSniff does not properly recognize last use
- Fixed bug [#19775][pear-19775] : False positive in NonExecutableCode sniff when not using curly braces
- Fixed bug [#19782][pear-19782] : Invalid found size functions in loop when using object operator
- Fixed bug [#19799][pear-19799] : config folder is not created automatically
- Fixed bug [#19804][pear-19804] : JS Tokenizer wrong /**/ parsing

[pear-19732]: https://pear.php.net/bugs/bug.php?id=19732
[pear-19755]: https://pear.php.net/bugs/bug.php?id=19755
[pear-19759]: https://pear.php.net/bugs/bug.php?id=19759
[pear-19763]: https://pear.php.net/bugs/bug.php?id=19763
[pear-19764]: https://pear.php.net/bugs/bug.php?id=19764
[pear-19772]: https://pear.php.net/bugs/bug.php?id=19772
[pear-19775]: https://pear.php.net/bugs/bug.php?id=19775
[pear-19782]: https://pear.php.net/bugs/bug.php?id=19782
[pear-19799]: https://pear.php.net/bugs/bug.php?id=19799
[pear-19804]: https://pear.php.net/bugs/bug.php?id=19804

## [1.4.3] - 2012-12-04

### Changed
- Added support for the PHP 5.5 T_FINALLY token to detect try/catch/finally statements
- Added empty CodeSniffer.conf to enable config settings for Composer installs
- Added Generic EndFileNoNewlineSniff to ensure there is no newline at the end of a file
- Autoloader can now load PSR-0 compliant classes
    - Thanks to [Maik Penz][@goatherd] for the patch
- Squiz NonExecutableCodeSniff no longer throws error for multi-line RETURNs inside CASE statements
    - Thanks to [Marc Ypes][@ceeram] for the patch
- Squiz OperatorSpacingSniff no longer reports errors for negative numbers inside inline THEN statements
    - Thanks to [Klaus Purer][@klausi] for the patch
- Squiz OperatorSpacingSniff no longer reports errors for the assignment of operations involving negative numbers
- Squiz SelfMemberReferenceSniff can no longer get into an infinite loop when checking a static call with a namespace
    - Thanks to [Andy Grunwald][@andygrunwald] for the patch

### Fixed
- Fixed bug [#19699][pear-19699] : Generic.Files.LineLength giving false positives when tab-width is used
- Fixed bug [#19726][pear-19726] : Wrong number of spaces expected after instanceof static
- Fixed bug [#19727][pear-19727] : PSR2: no error reported when using } elseif {

[pear-19699]: https://pear.php.net/bugs/bug.php?id=19699
[pear-19726]: https://pear.php.net/bugs/bug.php?id=19726
[pear-19727]: https://pear.php.net/bugs/bug.php?id=19727

## [1.4.2] - 2012-11-09

### Changed
- PHP_CodeSniffer can now be installed using Composer
    - Require `squizlabs/php_codesniffer` in your `composer.json` file
    - Thanks to [Rob Bast][@alcohol], [Stephen Rees-Carter][@valorin], [Stefano Kowalke][@Konafets] and [Ivan Habunek][@ihabunek] for help with this
- Squiz BlockCommentSniff and InlineCommentSniff no longer report errors for trait block comments
- Squiz SelfMemberReferenceSniff now supports namespaces
    - Thanks to [Andy Grunwald][@andygrunwald] for the patch
- Squiz FileCommentSniff now uses tag names inside the error codes for many messages
    - This allows you to exclude specific missing, out of order etc., tags
- Squiz SuperfluousWhitespaceSniff now has an option to ignore blank lines
    - This will stop errors being reported for lines that contain only whitespace
    - Set the ignoreBlankLines property to TRUE in your ruleset.xml file to enable this
- PSR2 no longer reports errors for whitespace at the end of blank lines

### Fixed
- Fixed gitblame report not working on Windows
    - Thanks to [Rogerio Prado de Jesus][@rogeriopradoj]
- Fixed an incorrect error in Squiz OperatorSpacingSniff for default values inside a closure definition
- Fixed bug [#19691][pear-19691] : SubversionPropertiesSniff fails to find missing properties
    - Thanks to [Kevin Winahradsky][pear-kwinahradsky] for the patch
- Fixed bug [#19692][pear-19692] : DisallowMultipleAssignments is triggered by a closure
- Fixed bug [#19693][pear-19693] : exclude-patterns no longer work on specific messages
- Fixed bug [#19694][pear-19694] : Squiz.PHP.LowercasePHPFunctions incorrectly matches return by ref functions

[pear-19691]: https://pear.php.net/bugs/bug.php?id=19691
[pear-19692]: https://pear.php.net/bugs/bug.php?id=19692
[pear-19693]: https://pear.php.net/bugs/bug.php?id=19693
[pear-19694]: https://pear.php.net/bugs/bug.php?id=19694

## [1.4.1] - 2012-11-02

### Changed
- All ignore patterns have been reverted to being checked against the absolute path of a file
    - Patterns can be specified to be relative in a ruleset.xml file, but nowhere else
    - e.g., `<exclude-pattern type="relative">^tests/*</exclude-pattern>`
- Added support for PHP tokenizing of T_INLINE_ELSE colons, so this token type is now available
    - Custom sniffs that rely on looking for T_COLON tokens inside inline if statements must be changed to use the new token
    - Fixes bug [#19666][pear-19666] : PSR1.Files.SideEffects throws a notice Undefined index: scope_closer
- Messages can now be changed from errors to warnings (and vice versa) inside ruleset.xml files
    - As you would with "message" and "severity", specify a "type" tag under a "rule" tag and set the value to "error" or "warning"
- PHP_CodeSniffer will now generate a warning on files that it detects have mixed line endings
    - This warning has the code Internal.LineEndings.Mixed and can be overridden in a ruleset.xml file
    - Thanks to [Vit Brunner][@tasuki] for help with this
- Sniffs inside PHP 5.3 namespaces are now supported, along with the existing underscore-style emulated namespaces
    - For example: namespace MyStandard\Sniffs\Arrays; class ArrayDeclarationSniff implements \PHP_CodeSniffer_Sniff { ...
    - Thanks to [Till Klampaeckel][@till] for the patch
- Generic DuplicateClassNameSniff is no longer a multi-file sniff, so it won't max out your memory
    - Multi-file sniff support should be considered deprecated as standard sniffs can now do the same thing
- Added Generic DisallowSpaceIndent to check that files are indented using tabs
- Added Generic OneClassPerFileSniff to check that only one class is defined in each file
    - Thanks to [Andy Grunwald][@andygrunwald] for the contribution
- Added Generic OneInterfacePerFileSniff to check that only one interface is defined in each file
    - Thanks to [Andy Grunwald][@andygrunwald] for the contribution
- Added Generic LowercasedFilenameSniff to check that filenames are lowercase
    - Thanks to [Andy Grunwald][@andygrunwald] for the contribution
- Added Generic ClosingPHPTagSniff to check that each open PHP tag has a corresponding close tag
    - Thanks to [Andy Grunwald][@andygrunwald] for the contribution
- Added Generic CharacterBeforePHPOpeningTagSniff to check that the open PHP tag is the first content in a file
    - Thanks to [Andy Grunwald][@andygrunwald] for the contribution
- Fixed incorrect errors in Squiz OperatorBracketSniff and OperatorSpacingSniff for negative numbers in CASE statements
    - Thanks to [Arnout Boks][@aboks] for the patch
- Generic CamelCapsFunctionNameSniff no longer enforces exact case matching for PHP magic methods
- Generic CamelCapsFunctionNameSniff no longer throws errors for overridden SOAPClient methods prefixed with double underscores
    - Thanks to [Dorian Villet][@gnutix] for the patch
- PEAR ValidFunctionNameSniff now supports traits
- PSR1 ClassDeclarationSniff no longer throws an error for non-namespaced code if PHP version is less than 5.3.0

### Fixed
- Fixed bug [#19616][pear-19616] : Nested switches cause false error in PSR2
- Fixed bug [#19629][pear-19629] : PSR2 error for inline comments on multi-line argument lists
- Fixed bug [#19644][pear-19644] : Alternative syntax, e.g. if/endif triggers Inline Control Structure error
- Fixed bug [#19655][pear-19655] : Closures reporting as multi-line when they are not
- Fixed bug [#19675][pear-19675] : Improper indent of nested anonymous function bodies in a call
- Fixed bug [#19685][pear-19685] : PSR2 catch-22 with empty third statement in for loop
- Fixed bug [#19687][pear-19687] : Anonymous functions inside arrays marked as indented incorrectly in PSR2

[pear-19616]: https://pear.php.net/bugs/bug.php?id=19616
[pear-19629]: https://pear.php.net/bugs/bug.php?id=19629
[pear-19644]: https://pear.php.net/bugs/bug.php?id=19644
[pear-19655]: https://pear.php.net/bugs/bug.php?id=19655
[pear-19666]: https://pear.php.net/bugs/bug.php?id=19666
[pear-19675]: https://pear.php.net/bugs/bug.php?id=19675
[pear-19685]: https://pear.php.net/bugs/bug.php?id=19685
[pear-19687]: https://pear.php.net/bugs/bug.php?id=19687

## [1.4.0] - 2012-09-26

### Changed
- Added PSR1 and PSR2 coding standards that can be used to check your code against these guidelines
- PHP 5.4 short array syntax is now detected and tokens are assigned to the open and close characters
    - New tokens are T_OPEN_SHORT_ARRAY and T_CLOSE_SHORT_ARRAY as PHP does not define its own
- Added the ability to explain a coding standard by listing the sniffs that it includes
    - The sniff list includes all imported and native sniffs
    - Explain a standard by using the `-e` and `--standard=[standard]` command line arguments
    - E.g., `phpcs -e --standard=Squiz`
    - Thanks to [Ben Selby][@benmatselby] for the idea
- Added report to show results using notify-send
    - Use --report=notifysend to generate the report
    - Thanks to [Christian Weiske][@cweiske] for the contribution
- The JS tokenizer now recognises RETURN as a valid closer for CASE and DEFAULT inside switch statements
- AbstractPatternSniff now sets the ignoreComments option using a public var rather than through the constructor
    - This allows the setting to be overwritten in ruleset.xml files
    - Old method remains for backwards compatibility
- Generic LowerCaseConstantSniff and UpperCaseConstantSniff no longer report errors on classes named True, False or Null
- PEAR ValidFunctionNameSniff no longer enforces exact case matching for PHP magic methods
- Squiz SwitchDeclarationSniff now allows RETURN statements to close a CASE or DEFAULT statement
- Squiz BlockCommentSniff now correctly reports an error for blank lines before blocks at the start of a control structure

### Fixed
- Fixed a PHP notice generated when loading custom array settings from a ruleset.xml file
- Fixed bug [#17908][pear-17908] : CodeSniffer does not recognise optional @params
    - Thanks to [Pete Walker][pear-pete] for the patch
- Fixed bug [#19538][pear-19538] : Function indentation code sniffer checks inside short arrays
- Fixed bug [#19565][pear-19565] : Non-Executable Code Sniff Broken for Case Statements with both return and break
- Fixed bug [#19612][pear-19612] : Invalid @package suggestion

[pear-17908]: https://pear.php.net/bugs/bug.php?id=17908
[pear-19538]: https://pear.php.net/bugs/bug.php?id=19538
[pear-19565]: https://pear.php.net/bugs/bug.php?id=19565
[pear-19612]: https://pear.php.net/bugs/bug.php?id=19612

## [1.3.6] - 2012-08-08

### Changed
- Memory usage has been dramatically reduced when using the summary report
    - Reduced memory is only available when displaying a single summary report to the screen
    - PHP_CodeSniffer will not generate any messages in this case, storing only error counts instead
    - Impact is most notable with very high error and warning counts
- Significantly improved the performance of Squiz NonExecutableCodeSniff
- Ignore patterns now check the relative path of a file based on the dir being checked
    - Allows ignore patterns to become more generic as the path to the code is no longer included when checking
    - Thanks to [Kristof Coomans][@kristofser] for the patch
- Sniff settings can now be changed by specifying a special comment format inside a file
    - e.g., // @codingStandardsChangeSetting PEAR.Functions.FunctionCallSignature allowMultipleArguments false
    - If you change a setting, don't forget to change it back
- Added Generic EndFileNewlineSniff to ensure PHP files end with a newline character
- PEAR FunctionCallSignatureSniff now includes a setting to force one argument per line in multi-line calls
    - Set allowMultipleArguments to false
- Squiz standard now enforces one argument per line in multi-line function calls
- Squiz FunctionDeclarationArgumentSpacingSniff now supports closures
- Squiz OperatorSpacingSniff no longer throws an error for negative values inside an inline THEN statement
    - Thanks to [Klaus Purer][@klausi] for the patch
- Squiz FunctionCommentSniff now throws an error for not closing a comment with */
    - Thanks to [Klaus Purer][@klausi] for the patch
- Summary report no longer shows two lines of PHP_Timer output when showing sources

### Fixed
- Fixed undefined variable error in PEAR FunctionCallSignatureSniff for lines with no indent
- Fixed bug [#19502][pear-19502] : Generic.Files.LineEndingsSniff fails if no new-lines in file
- Fixed bug [#19508][pear-19508] : switch+return: Closing brace indented incorrectly
- Fixed bug [#19532][pear-19532] : The PSR-2 standard don't recognize Null in class names
- Fixed bug [#19546][pear-19546] : Error thrown for __call() method in traits

[pear-19502]: https://pear.php.net/bugs/bug.php?id=19502
[pear-19508]: https://pear.php.net/bugs/bug.php?id=19508
[pear-19532]: https://pear.php.net/bugs/bug.php?id=19532
[pear-19546]: https://pear.php.net/bugs/bug.php?id=19546

## [1.3.5] - 2012-07-12

### Changed
- Added Generic CamelCapsFunctionNameSniff to just check if function and method names use camel caps
    - Does not allow underscore prefixes for private/protected methods
    - Defaults to strict checking, where two uppercase characters can not be next to each other
    - Strict checking can be disabled in a ruleset.xml file
- Squiz FunctionDeclarationArgumentSpacing now has a setting to specify how many spaces should surround equals signs
    - Default remains at 0
    - Override the equalsSpacing setting in a ruleset.xml file to change
- Squiz ClassDeclarationSniff now throws errors for > 1 space before extends/implements class name with ns separator
- Squiz standard now warns about deprecated functions using Generic DeprecatedFunctionsSniff
- PEAR FunctionDeclarationSniff now reports an error for multiple spaces after the FUNCTION keyword and around USE
- PEAR FunctionDeclarationSniff now supports closures
- Squiz MultiLineFunctionDeclarationSniff now supports closures
- Exclude rules written for Unix systems will now work correctly on Windows
    - Thanks to [Walter Tamboer][@waltertamboer] for the patch
- The PHP tokenizer now recognises T_RETURN as a valid closer for T_CASE and T_DEFAULT inside switch statements

### Fixed
- Fixed duplicate message codes in Generic OpeningFunctionBraceKernighanRitchieSniff
- Fixed bug [#18651][pear-18651] : PHPUnit Test cases for custom standards are not working on Windows
- Fixed bug [#19416][pear-19416] : Shorthand arrays cause bracket spacing errors
- Fixed bug [#19421][pear-19421] : phpcs doesn't recognize ${x} as equivalent to $x
- Fixed bug [#19428][pear-19428] : PHPCS Report "hgblame" doesn't support windows paths
    - Thanks to [Justin Rovang][@rovangju] for the patch
- Fixed bug [#19448][pear-19448] : Problem with detecting remote standards
- Fixed bug [#19463][pear-19463] : Anonymous functions incorrectly being flagged by NonExecutableCodeSniff
- Fixed bug [#19469][pear-19469] : PHP_CodeSniffer_File::getMemberProperties() sets wrong scope
- Fixed bug [#19471][pear-19471] : phpcs on Windows, when using Zend standard, doesn't catch problems
    - Thanks to [Ivan Habunek][@ihabunek] for the patch
- Fixed bug [#19478][pear-19478] : Incorrect indent detection in PEAR standard
    - Thanks to [Shane Auckland][@shanethehat] for the patch
- Fixed bug [#19483][pear-19483] : Blame Reports fail with space in directory name

[pear-18651]: https://pear.php.net/bugs/bug.php?id=18651
[pear-19416]: https://pear.php.net/bugs/bug.php?id=19416
[pear-19421]: https://pear.php.net/bugs/bug.php?id=19421
[pear-19428]: https://pear.php.net/bugs/bug.php?id=19428
[pear-19448]: https://pear.php.net/bugs/bug.php?id=19448
[pear-19463]: https://pear.php.net/bugs/bug.php?id=19463
[pear-19469]: https://pear.php.net/bugs/bug.php?id=19469
[pear-19471]: https://pear.php.net/bugs/bug.php?id=19471
[pear-19478]: https://pear.php.net/bugs/bug.php?id=19478
[pear-19483]: https://pear.php.net/bugs/bug.php?id=19483

## [1.3.4] - 2012-05-17

### Changed
- Added missing package.xml entries for new Generic FixmeSniff
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- Expected indents for PEAR ScopeClosingBraceSniff and FunctionCallSignatureSniff can now be set in ruleset files
    - Both sniffs use a variable called "indent"
    - Thanks to [Thomas Despoix][pear-tomdesp] for the patch
- Standards designed to be installed in the PHPCS Standards dir will now work outside this dir as well
    - In particular, allows the Drupal CS to work without needing to symlink it into the PHPCS install
    - Thanks to [Peter Philipp][@das-peter] for the patch
- Rule references for standards, directories and specific sniffs can now be relative in ruleset.xml files
    - For example: `ref="../MyStandard/Sniffs/Commenting/DisallowHashCommentsSniff.php"`
- Symlinked standards now work correctly, allowing aliasing of installed standards (request [#19417][pear-19417])
    - Thanks to [Tom Klingenberg][@ktomk] for the patch
- Squiz ObjectInstantiationSniff now allows objects to be returned without assigning them to a variable
- Added Squiz.Commenting.FileComment.MissingShort error message for file comments that only contains tags
    - Also stops undefined index errors being generated for these comments
- Debug option -vv now shows tokenizer status for CSS files
- Added support for new gjslint error formats
    - Thanks to [Meck][@yesmeck] for the patch
- Generic ScopeIndentSniff now allows comment indents to not be exact even if the exact flag is set
    - The start of the comment is still checked for exact indentation as normal
- Fixed an issue in AbstractPatternSniff where comments were not being ignored in some cases
- Fixed an issue in Zend ClosingTagSniff where the closing tag was not always being detected correctly
    - Thanks to [Jonathan Robson][@jnrbsn] for the patch
- Fixed an issue in Generic FunctionCallArgumentSpacingSniff where closures could cause incorrect errors
- Fixed an issue in Generic UpperCaseConstantNameSniff where errors were incorrectly reported on goto statements
    - Thanks to [Tom Klingenberg][@ktomk] for the patch
- PEAR FileCommentSniff and ClassCommentSniff now support author emails with a single character in the local part
    - E.g., `a@me.com`
    - Thanks to Denis Shapkin for the patch

### Fixed
- Fixed bug [#19290][pear-19290] : Generic indent sniffer fails for anonymous functions
- Fixed bug [#19324][pear-19324] : Setting show_warnings configuration option does not work
- Fixed bug [#19354][pear-19354] : Not recognizing references passed to method
- Fixed bug [#19361][pear-19361] : CSS tokenizer generates errors when PHP embedded in CSS file
- Fixed bug [#19374][pear-19374] : HEREDOC/NOWDOC Indentation problems
- Fixed bug [#19381][pear-19381] : traits and indentations in traits are not handled properly
- Fixed bug [#19394][pear-19394] : Notice in NonExecutableCodeSniff
- Fixed bug [#19402][pear-19402] : Syntax error when executing phpcs on Windows with parens in PHP path
    - Thanks to [Tom Klingenberg][@ktomk] for the patch
- Fixed bug [#19411][pear-19411] : magic method error on __construct()
    - The fix required a rewrite of AbstractScopeSniff, so please test any sniffs that extend this class
- Fixed bug [#19412][pear-19412] : Incorrect error about assigning objects to variables when inside inline IF
- Fixed bug [#19413][pear-19413] : PHP_CodeSniffer thinks I haven't used a parameter when I have
- Fixed bug [#19414][pear-19414] : PHP_CodeSniffer seems to not track variables correctly in heredocs

[pear-19290]: https://pear.php.net/bugs/bug.php?id=19290
[pear-19324]: https://pear.php.net/bugs/bug.php?id=19324
[pear-19354]: https://pear.php.net/bugs/bug.php?id=19354
[pear-19361]: https://pear.php.net/bugs/bug.php?id=19361
[pear-19374]: https://pear.php.net/bugs/bug.php?id=19374
[pear-19381]: https://pear.php.net/bugs/bug.php?id=19381
[pear-19394]: https://pear.php.net/bugs/bug.php?id=19394
[pear-19402]: https://pear.php.net/bugs/bug.php?id=19402
[pear-19411]: https://pear.php.net/bugs/bug.php?id=19411
[pear-19412]: https://pear.php.net/bugs/bug.php?id=19412
[pear-19413]: https://pear.php.net/bugs/bug.php?id=19413
[pear-19414]: https://pear.php.net/bugs/bug.php?id=19414
[pear-19417]: https://pear.php.net/bugs/bug.php?id=19417

## [1.3.3] - 2012-02-07

### Changed
- Added new Generic FixmeSniff that shows error messages for all FIXME comments left in your code
    - Thanks to [Sam Graham][@illusori] for the contribution
- The maxPercentage setting in the Squiz CommentedOutCodeSniff can now be overridden in a ruleset.xml file
    - Thanks to [Volker Dusch][@edorian] for the patch
- The Checkstyle and XML reports now use XMLWriter
    - Only change in output is that empty file tags are no longer produced for files with no violations
    - Thanks to [Sebastian Bergmann][@sebastianbergmann] for the patch
- Added PHP_CodeSniffer_Tokens::$bracketTokens to give sniff writers fast access to open and close bracket tokens
- Fixed an issue in AbstractPatternSniff where EOL tokens were not being correctly checked in some cases
- PHP_CodeSniffer_File::getTokensAsString() now detects incorrect length value (request [#19313][pear-19313])

### Fixed
- Fixed bug [#19114][pear-19114] : CodeSniffer checks extension even for single file
- Fixed bug [#19171][pear-19171] : Show sniff codes option is ignored by some report types
    - Thanks to [Dominic Scheirlinck][@dominics] for the patch
- Fixed bug [#19188][pear-19188] : Lots of PHP Notices when analyzing the Symfony framework
    - First issue was list-style.. lines in CSS files not properly adjusting open/close bracket positions
    - Second issue was notices caused by bug [#19137][pear-19137]
- Fixed bug [#19208][pear-19208] : UpperCaseConstantName reports class members
    - Was also a problem with LowerCaseConstantName as well
- Fixed bug [#19256][pear-19256] : T_DOC_COMMENT in CSS files breaks ClassDefinitionNameSpacingSniff
    - Thanks to [Klaus Purer][@klausi] for the patch
- Fixed bug [#19264][pear-19264] : Squiz.PHP.NonExecutableCode does not handle RETURN in CASE without BREAK
- Fixed bug [#19270][pear-19270] : DuplicateClassName does not handle namespaces correctly
- Fixed bug [#19283][pear-19283] : CSS @media rules cause false positives
    - Thanks to [Klaus Purer][@klausi] for the patch

[pear-19114]: https://pear.php.net/bugs/bug.php?id=19114
[pear-19137]: https://pear.php.net/bugs/bug.php?id=19137
[pear-19171]: https://pear.php.net/bugs/bug.php?id=19171
[pear-19188]: https://pear.php.net/bugs/bug.php?id=19188
[pear-19208]: https://pear.php.net/bugs/bug.php?id=19208
[pear-19256]: https://pear.php.net/bugs/bug.php?id=19256
[pear-19264]: https://pear.php.net/bugs/bug.php?id=19264
[pear-19270]: https://pear.php.net/bugs/bug.php?id=19270
[pear-19283]: https://pear.php.net/bugs/bug.php?id=19283
[pear-19313]: https://pear.php.net/bugs/bug.php?id=19313

## [1.3.2] - 2011-12-01

### Changed
- Added Generic JSHintSniff to run jshint.js over a JS file and report warnings
    - Set jshint path using phpcs --config-set jshint_path /path/to/jshint-rhino.js
    - Set rhino path using phpcs --config-set rhino_path /path/to/rhino
    - Thanks to Alexander Weiß for the contribution
- Nowdocs are now tokenized using PHP_CodeSniffer specific T_NOWDOC tokens for easier identification
- Generic UpperCaseConstantNameSniff no longer throws errors for namespaces
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- Squiz NonExecutableCodeSniff now detects code after thrown exceptions
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- Squiz OperatorSpacingSniff now ignores references
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- Squiz FunctionCommentSniff now reports a missing function comment if it finds a standard code comment instead
- Squiz FunctionCommentThrownTagSniff no longer reports errors if it can't find a function comment

### Fixed
- Fixed unit tests not running under Windows
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- Fixed bug [#18964][pear-18964] : "$stackPtr must be of type T_VARIABLE" on heredocs and nowdocs
- Fixed bug [#18973][pear-18973] : phpcs is looking for variables in a nowdoc
- Fixed bug [#18974][pear-18974] : Blank line causes "Multi-line function call not indented correctly"
    - Adds new error message to ban empty lines in multi-line function calls
- Fixed bug [#18975][pear-18975] : "Closing parenthesis must be on a line by itself" also causes indentation error

[pear-18964]: https://pear.php.net/bugs/bug.php?id=18964
[pear-18973]: https://pear.php.net/bugs/bug.php?id=18973
[pear-18974]: https://pear.php.net/bugs/bug.php?id=18974
[pear-18975]: https://pear.php.net/bugs/bug.php?id=18975

## 1.3.1 - 2011-11-03

### Changed
- All report file command line arguments now work with relative paths (request [#17240][pear-17240])
- The extensions command line argument now supports multi-part file extensions (request [#17227][pear-17227])
- Added report type --report=hgblame to show number of errors/warnings committed by authors in a Mercurial repository
    - Has the same functionality as the svnblame report
    - Thanks to [Ben Selby][@benmatselby] for the patch
- Added T_BACKTICK token type to make detection of backticks easier (request [#18799][pear-18799])
- Added pattern matching support to Generic ForbiddenFunctionsSniff
    - If you are extending it and overriding register() or addError() you will need to review your sniff
- Namespaces are now recognised as scope openers, although they do not require braces (request [#18043][pear-18043])
- Added new ByteOrderMarkSniff to Generic standard (request [#18194][pear-18194])
    - Throws an error if a byte order mark is found in any PHP file
    - Thanks to [Piotr Karas][pear-ryba] for the contribution
- PHP_Timer output is no longer included in reports when being written to a file (request [#18252][pear-18252])
    - Also now shown for all report types if nothing is being printed to the screen
- Generic DeprecatedFunctionSniff now reports functions as deprecated and not simply forbidden (request [#18288][pear-18288])
- PHPCS now accepts file contents from STDIN (request [#18447][pear-18447])
    - Example usage: `cat temp.php | phpcs [options]`  -OR-  `phpcs [options] &lt; temp.php`
    - Not every sniff will work correctly due to the lack of a valid file path
- PHP_CodeSniffer_Exception no longer extends PEAR_Exception (request [#18483][pear-18483])
    - PEAR_Exception added a requirement that PEAR had to be installed
    - PHP_CodeSniffer is not used as a library, so unlikely to have any impact
- PEAR FileCommentSniff now allows GIT IDs in the version tag (request [#14874][pear-14874])
- AbstractVariableSniff now supports heredocs
    - Also includes some variable detection fixes
    - Thanks to [Sam Graham][@illusori] for the patch
- Squiz FileCommentSniff now enforces rule that package names cannot start with the word Squiz
- MySource AssignThisSniff now allows "this" to be assigned to the private var _self
- PEAR ClassDeclaration sniff now supports indentation checks when using the alternate namespace syntax
    - PEAR.Classes.ClassDeclaration.SpaceBeforeBrace message now contains 2 variables instead of 1
    - Sniff allows overriding of the default indent level, which is set to 4
    - Fixes bug [#18933][pear-18933] : Alternative namespace declaration syntax confuses scope sniffs

### Fixed
- Fixed issue in Squiz FileCommentSniff where suggested package name was the same as the incorrect package name
- Fixed some issues with Squiz ArrayDeclarationSniff when using function calls in array values
- Fixed doc generation so it actually works again
    - Also now works when being run from an SVN checkout as well as when installed as a PEAR package
    - Should fix bug [#18949][pear-18949] : Call to private method from static
- Fixed bug [#18465][pear-18465] : "self::" does not work in lambda functions
    - Also corrects conversion of T_FUNCTION tokens to T_CLOSURE, which was not fixing token condition arrays
- Fixed bug [#18543][pear-18543] : CSS Tokenizer deletes too many #
- Fixed bug [#18624][pear-18624] : @throws namespace problem
    - Thanks to [Gavin Davies][pear-boxgav] for the patch
- Fixed bug [#18628][pear-18628] : Generic.Files.LineLength gives incorrect results with Windows line-endings
- Fixed bug [#18633][pear-18633] : CSS Tokenizer doesn't replace T_LIST tokens inside some styles
- Fixed bug [#18657][pear-18657] : anonymous functions wrongly indented
- Fixed bug [#18670][pear-18670] : UpperCaseConstantNameSniff fails on dynamic retrieval of class constant
- Fixed bug [#18709][pear-18709] : Code sniffer sniffs file even if it's in --ignore
    - Thanks to [Artem Lopata][@biozshock] for the patch
- Fixed bug [#18762][pear-18762] : Incorrect handling of define and constant in UpperCaseConstantNameSniff
    - Thanks to [Thomas Baker][pear-bakert] for the patch
- Fixed bug [#18769][pear-18769] : CSS Tokenizer doesn't replace T_BREAK tokens inside some styles
- Fixed bug [#18835][pear-18835] : Unreachable errors of inline returns of closure functions
    - Thanks to [Patrick Schmidt][pear-woellchen] for the patch
- Fixed bug [#18839][pear-18839] : Fix miscount of warnings in `AbstractSniffUnitTest.php`
    - Thanks to [Sam Graham][@illusori] for the patch
- Fixed bug [#18844][pear-18844] : Generic_Sniffs_CodeAnalysis_UnusedFunctionParameterSniff with empty body
    - Thanks to [Dmitri Medvedev][pear-dvino] for the patch
- Fixed bug [#18847][pear-18847] : Running Squiz_Sniffs_Classes_ClassDeclarationSniff results in PHP notice
- Fixed bug [#18868][pear-18868] : jslint+rhino: errors/warnings not detected
    - Thanks to [Christian Weiske][@cweiske] for the patch
- Fixed bug [#18879][pear-18879] : phpcs-svn-pre-commit requires escapeshellarg
    - Thanks to [Bjorn Katuin][pear-bjorn] for the patch
- Fixed bug [#18951][pear-18951] : weird behaviour with closures and multi-line use () params

[pear-14874]: https://pear.php.net/bugs/bug.php?id=14874
[pear-17227]: https://pear.php.net/bugs/bug.php?id=17227
[pear-17240]: https://pear.php.net/bugs/bug.php?id=17240
[pear-18043]: https://pear.php.net/bugs/bug.php?id=18043
[pear-18194]: https://pear.php.net/bugs/bug.php?id=18194
[pear-18252]: https://pear.php.net/bugs/bug.php?id=18252
[pear-18288]: https://pear.php.net/bugs/bug.php?id=18288
[pear-18447]: https://pear.php.net/bugs/bug.php?id=18447
[pear-18465]: https://pear.php.net/bugs/bug.php?id=18465
[pear-18483]: https://pear.php.net/bugs/bug.php?id=18483
[pear-18543]: https://pear.php.net/bugs/bug.php?id=18543
[pear-18624]: https://pear.php.net/bugs/bug.php?id=18624
[pear-18628]: https://pear.php.net/bugs/bug.php?id=18628
[pear-18633]: https://pear.php.net/bugs/bug.php?id=18633
[pear-18657]: https://pear.php.net/bugs/bug.php?id=18657
[pear-18670]: https://pear.php.net/bugs/bug.php?id=18670
[pear-18709]: https://pear.php.net/bugs/bug.php?id=18709
[pear-18762]: https://pear.php.net/bugs/bug.php?id=18762
[pear-18769]: https://pear.php.net/bugs/bug.php?id=18769
[pear-18799]: https://pear.php.net/bugs/bug.php?id=18799
[pear-18835]: https://pear.php.net/bugs/bug.php?id=18835
[pear-18839]: https://pear.php.net/bugs/bug.php?id=18839
[pear-18844]: https://pear.php.net/bugs/bug.php?id=18844
[pear-18847]: https://pear.php.net/bugs/bug.php?id=18847
[pear-18868]: https://pear.php.net/bugs/bug.php?id=18868
[pear-18879]: https://pear.php.net/bugs/bug.php?id=18879
[pear-18933]: https://pear.php.net/bugs/bug.php?id=18933
[pear-18949]: https://pear.php.net/bugs/bug.php?id=18949
[pear-18951]: https://pear.php.net/bugs/bug.php?id=18951

## 1.3.0 - 2011-03-17

### Changed
- Add a new token T_CLOSURE that replaces T_FUNCTION if the function keyword is anonymous
- Many Squiz sniffs no longer report errors when checking closures; they are now ignored
- Fixed some error messages in PEAR MultiLineConditionSniff that were not using placeholders for message data
- AbstractVariableSniff now correctly finds variable names wrapped with curly braces inside double quoted strings
- PEAR FunctionDeclarationSniff now ignores arrays in argument default values when checking multi-line declarations

### Fixed
- Fixed bug [#18200][pear-18200] : Using custom named ruleset file as standard no longer works
- Fixed bug [#18196][pear-18196] : PEAR MultiLineCondition.SpaceBeforeOpenBrace not consistent with newline chars
- Fixed bug [#18204][pear-18204] : FunctionCommentThrowTag picks wrong exception type when throwing function call
- Fixed bug [#18222][pear-18222] : Add __invoke method to PEAR standard
- Fixed bug [#18235][pear-18235] : Invalid error generation in Squiz.Commenting.FunctionCommentThrowTag
- Fixed bug [#18250][pear-18250] : --standard with relative path skips Standards' "implicit" sniffs
- Fixed bug [#18274][pear-18274] : Multi-line IF and function call indent rules conflict
- Fixed bug [#18282][pear-18282] : Squiz doesn't handle final keyword before function comments
    - Thanks to [Dave Perrett][pear-recurser] for the patch
- Fixed bug [#18336][pear-18336] : Function isUnderscoreName gives PHP notices

[pear-18196]: https://pear.php.net/bugs/bug.php?id=18196
[pear-18200]: https://pear.php.net/bugs/bug.php?id=18200
[pear-18204]: https://pear.php.net/bugs/bug.php?id=18204
[pear-18222]: https://pear.php.net/bugs/bug.php?id=18222
[pear-18235]: https://pear.php.net/bugs/bug.php?id=18235
[pear-18250]: https://pear.php.net/bugs/bug.php?id=18250
[pear-18274]: https://pear.php.net/bugs/bug.php?id=18274
[pear-18282]: https://pear.php.net/bugs/bug.php?id=18282
[pear-18336]: https://pear.php.net/bugs/bug.php?id=18336

## 1.3.0RC2 - 2011-01-14

### Changed
- You can now print multiple reports for each run and print each to the screen or a file (request [#12434][pear-12434])
    - Format is `--report-[report][=file]` (e.g., `--report-xml=out.xml`)
    - Printing to screen is done by leaving `[file]` empty (e.g., `--report-xml`)
    - Multiple reports can be specified in this way (e.g., `--report-summary --report-xml=out.xml`)
    - The standard `--report` and `--report-file` command line arguments are unchanged
- Added `-d` command line argument to set `php.ini` settings while running (request [#17244][pear-17244])
    - Usage is: `phpcs -d memory_limit=32M -d ...`
    - Thanks to [Ben Selby][@benmatselby] for the patch
- Added -p command line argument to show progress during a run
    - Dot means pass, E means errors found, W means only warnings found and S means skipped file
    - Particularly good for runs where you are checking more than 100 files
    - Enable by default with --config-set show_progress 1
    - Will not print anything if you are already printing verbose output
    - This has caused a big change in the way PHP_CodeSniffer processes files (API changes around processing)
- You can now add exclude rules for individual sniffs or error messages (request [#17903][pear-17903])
    - Only available when using a ruleset.xml file to specify rules
    - Uses the same exclude-pattern tags as normal but allows them inside rule tags
- Using the -vvv option will now print a list of sniffs executed for each file and how long they took to process
- Added Generic ClosureLinterSniff to run Google's gjslint over your JS files
- The XML and CSV reports now include the severity of the error (request [#18165][pear-18165])
    - The Severity column in the CSV report has been renamed to Type, and a new Severity column added for this
- Fixed issue with Squiz FunctionCommentSniff reporting incorrect type hint when default value uses namespace
    - Thanks to Anti Veeranna for the patch
- Generic FileLengthSniff now uses iconv_strlen to check line length if an encoding is specified (request [#14237][pear-14237])
- Generic UnnecessaryStringConcatSniff now allows strings to be combined to form a PHP open or close tag
- Squiz SwitchDeclarationSniff no longer reports indentation errors for BREAK statements inside IF conditions
- Interactive mode now always prints the full error report (ignores command line)
- Improved regular expression detection in JavaScript files
    - Added new T_TYPEOF token that can be used to target the typeof JS operator
    - Fixes bug [#17611][pear-17611] : Regular expression tokens not recognised
- Squiz ScopeIndentSniff removed
    - Squiz standard no longer requires additional indents between ob_* methods
    - Also removed Squiz OutputBufferingIndentSniff that was checking the same thing
- PHP_CodeSniffer_File::getMemberProperties() performance improved significantly
    - Improves performance of Squiz ValidVariableNameSniff significantly
- Squiz OperatorSpacingSniff performance improved significantly
- Squiz NonExecutableCodeSniff performance improved significantly
    - Will throw duplicate errors in some cases now, but these should be rare
- MySource IncludeSystemSniff performance improved significantly
- MySource JoinStringsSniff no longer reports an error when using join() on a named JS array
- Warnings are now reported for each file when they cannot be opened instead of stopping the script
    - Hide warnings with the -n command line argument
    - Can override the warnings using the code Internal.DetectLineEndings

### Fixed
- Fixed bug [#17693][pear-17693] : issue with pre-commit hook script with filenames that start with v
- Fixed bug [#17860][pear-17860] : isReference function fails with references in array
    - Thanks to [Lincoln Maskey][pear-ljmaskey] for the patch
- Fixed bug [#17902][pear-17902] : Cannot run tests when tests are symlinked into tests dir
    - Thanks to [Matt Button][@BRMatt] for the patch
- Fixed bug [#17928][pear-17928] : Improve error message for Generic_Sniffs_PHP_UpperCaseConstantSniff
    - Thanks to [Stefano Kowalke][@Konafets] for the patch
- Fixed bug [#18039][pear-18039] : JS Tokenizer crash when ] is last character in file
- Fixed bug [#18047][pear-18047] : Incorrect handling of namespace aliases as constants
    - Thanks to [Dmitri Medvedev][pear-dvino] for the patch
- Fixed bug [#18072][pear-18072] : Impossible to exclude path from processing when symlinked
- Fixed bug [#18073][pear-18073] : Squiz.PHP.NonExecutableCode fault
- Fixed bug [#18117][pear-18117] : PEAR coding standard: Method constructor not sniffed as a function
- Fixed bug [#18135][pear-18135] : Generic FunctionCallArgumentSpacingSniff reports function declaration errors
- Fixed bug [#18140][pear-18140] : Generic scope indent in exact mode: strange expected/found values for switch
- Fixed bug [#18145][pear-18145] : Sniffs are not loaded for custom ruleset file
    - Thanks to [Scott McCammon][pear-mccammos] for the patch
- Fixed bug [#18152][pear-18152] : While and do-while with AbstractPatternSniff
- Fixed bug [#18191][pear-18191] : Squiz.PHP.LowercasePHPFunctions does not work with new Date()
- Fixed bug [#18193][pear-18193] : CodeSniffer doesn't reconize CR (\r) line endings

[pear-12434]: https://pear.php.net/bugs/bug.php?id=12434
[pear-14237]: https://pear.php.net/bugs/bug.php?id=14237
[pear-17244]: https://pear.php.net/bugs/bug.php?id=17244
[pear-17611]: https://pear.php.net/bugs/bug.php?id=17611
[pear-17693]: https://pear.php.net/bugs/bug.php?id=17693
[pear-17860]: https://pear.php.net/bugs/bug.php?id=17860
[pear-17902]: https://pear.php.net/bugs/bug.php?id=17902
[pear-17903]: https://pear.php.net/bugs/bug.php?id=17903
[pear-17928]: https://pear.php.net/bugs/bug.php?id=17928
[pear-18039]: https://pear.php.net/bugs/bug.php?id=18039
[pear-18047]: https://pear.php.net/bugs/bug.php?id=18047
[pear-18072]: https://pear.php.net/bugs/bug.php?id=18072
[pear-18073]: https://pear.php.net/bugs/bug.php?id=18073
[pear-18117]: https://pear.php.net/bugs/bug.php?id=18117
[pear-18135]: https://pear.php.net/bugs/bug.php?id=18135
[pear-18140]: https://pear.php.net/bugs/bug.php?id=18140
[pear-18145]: https://pear.php.net/bugs/bug.php?id=18145
[pear-18152]: https://pear.php.net/bugs/bug.php?id=18152
[pear-18165]: https://pear.php.net/bugs/bug.php?id=18165
[pear-18191]: https://pear.php.net/bugs/bug.php?id=18191
[pear-18193]: https://pear.php.net/bugs/bug.php?id=18193

## 1.3.0RC1 - 2010-09-03

### Changed
- Added exclude pattern support to ruleset.xml file so you can specify ignore patterns in a standard (request [#17683][pear-17683])
    - Use new exclude-pattern tags to include the ignore rules into your ruleset.xml file
    - See CodeSniffer/Standards/PHPCS/ruleset.xml for an example
- Added new --encoding command line argument to specify the encoding of the files being checked
    - When set to utf-8, stops the XML-based reports from double-encoding
    - When set to something else, helps the XML-based reports encode to utf-8
    - Default value is iso-8859-1 but can be changed with `--config-set encoding [value]`
- The report is no longer printed to screen when using the --report-file command line option (request [#17467][pear-17467])
    - If you want to print it to screen as well, use the -v command line argument
- The SVN and GIT blame reports now also show percentage of reported errors per author (request [#17606][pear-17606])
    - Thanks to [Ben Selby][@benmatselby] for the patch
- Updated the SVN pre-commit hook to work with the new severity levels feature
- Generic SubversionPropertiesSniff now allows properties to have NULL values (request [#17682][pear-17682])
    - A null value indicates that the property should exist but the value should not be checked
- Generic UpperCaseConstantName Sniff now longer complains about the PHPUnit_MAIN_METHOD constant (request [#17798][pear-17798])
- Squiz FileComment sniff now checks JS files as well as PHP files
- Squiz FunctionCommentSniff now supports namespaces in type hints

### Fixed
- Fixed a problem in Squiz OutputBufferingIndentSniff where block comments were reported as not indented
- Fixed bug [#17092][pear-17092] : Problems with utf8_encode and htmlspecialchars with non-ascii chars
    - Use the new --encoding=utf-8 command line argument if your files are utf-8 encoded
- Fixed bug [#17629][pear-17629] : PHP_CodeSniffer_Tokens::$booleanOperators missing T_LOGICAL_XOR
    - Thanks to [Matthew Turland][@elazar] for the patch
- Fixed bug [#17699][pear-17699] : Fatal error generating code coverage with PHPUnit 5.3.0RC1
- Fixed bug [#17718][pear-17718] : Namespace 'use' statement: used global class name is recognized as constant
- Fixed bug [#17734][pear-17734] : Generic SubversionPropertiesSniff complains on non SVN files
- Fixed bug [#17742][pear-17742] : EmbeddedPhpSniff reacts negatively to file without closing PHP tag
- Fixed bug [#17823][pear-17823] : Notice: Please no longer include `PHPUnit/Framework.php`

[pear-17092]: https://pear.php.net/bugs/bug.php?id=17092
[pear-17467]: https://pear.php.net/bugs/bug.php?id=17467
[pear-17606]: https://pear.php.net/bugs/bug.php?id=17606
[pear-17629]: https://pear.php.net/bugs/bug.php?id=17629
[pear-17682]: https://pear.php.net/bugs/bug.php?id=17682
[pear-17683]: https://pear.php.net/bugs/bug.php?id=17683
[pear-17699]: https://pear.php.net/bugs/bug.php?id=17699
[pear-17718]: https://pear.php.net/bugs/bug.php?id=17718
[pear-17734]: https://pear.php.net/bugs/bug.php?id=17734
[pear-17742]: https://pear.php.net/bugs/bug.php?id=17742
[pear-17798]: https://pear.php.net/bugs/bug.php?id=17798
[pear-17823]: https://pear.php.net/bugs/bug.php?id=17823

## 1.3.0a1 - 2010-07-15

### Changed
- All `CodingStandard.php` files have been replaced by `ruleset.xml` files
    - Custom standards will need to be converted over to this new format to continue working
- You can specify a path to your own custom ruleset.xml file by using the --standard command line arg
    - e.g., phpcs --standard=/path/to/my/ruleset.xml
- Added a new report type --report=gitblame to show how many errors and warnings were committed by each author
    - Has the same functionality as the svnblame report
    - Thanks to [Ben Selby][@benmatselby] for the patch
- A new token type T_DOLLAR has been added to allow you to sniff for variable variables (feature request [#17095][pear-17095])
    - Thanks to [Ian Young][pear-youngian] for the patch
- JS tokenizer now supports T_POWER (^) and T_MOD_EQUAL (%=) tokens (feature request [#17441][pear-17441])
- If you have PHP_Timer installed, you'll now get a time/memory summary at the end of a script run
    - Only happens when printing reports that are designed to be read on the command line
- Added Generic DeprecatedFunctionsSniff to warn about the use of deprecated functions (feature request [#16694][pear-16694])
    - Thanks to [Sebastian Bergmann][@sebastianbergmann] for the patch
- Added Squiz LogicalOperatorSniff to ensure that logical operators are surrounded by single spaces
- Added MySource ChannelExceptionSniff to ensure action files only throw ChannelException
- Added new method getClassProperties() for sniffs to use to determine if a class is abstract and/or final
    - Thanks to [Christian Kaps][@akkie] for the patch
- Generic UpperCaseConstantSniff no longer throws errors about namespaces
    - Thanks to [Christian Kaps][@akkie] for the patch
- Squiz OperatorBracketSniff now correctly checks value assignments in arrays
- Squiz LongConditionClosingCommentSniff now requires a comment for long CASE statements that use curly braces
- Squiz LongConditionClosingCommentSniff now requires an exact comment match on the brace
- MySource IncludeSystemSniff now ignores DOMDocument usage
- MySource IncludeSystemSniff no longer requires inclusion of systems that are being implemented
- Removed found and expected messages from Squiz ConcatenationSpacingSniff because they were messy and not helpful

### Fixed
- Fixed a problem where Generic CodeAnalysisSniff could show warnings if checking multi-line strings
- Fixed error messages in Squiz ArrayDeclarationSniff reporting incorrect number of found and expected spaces
- Fixed bug [#17048][pear-17048] : False positive in Squiz_WhiteSpace_ScopeKeywordSpacingSniff
- Fixed bug [#17054][pear-17054] : phpcs more strict than PEAR CS regarding function parameter spacing
- Fixed bug [#17096][pear-17096] : Notice: Undefined index: `scope_condition` in `ScopeClosingBraceSniff.php`
    - Moved PEAR.Functions.FunctionCallArgumentSpacing to Generic.Functions.FunctionCallArgumentSpacing
- Fixed bug [#17144][pear-17144] : Deprecated: Function eregi() is deprecated
- Fixed bug [#17236][pear-17236] : PHP Warning due to token_get_all() in DoubleQuoteUsageSniff
- Fixed bug [#17243][pear-17243] : Alternate Switch Syntax causes endless loop of Notices in SwitchDeclaration
- Fixed bug [#17313][pear-17313] : Bug with switch case structure
- Fixed bug [#17331][pear-17331] : Possible parse error: interfaces may not include member vars
- Fixed bug [#17337][pear-17337] : CSS tokenizer fails on quotes urls
- Fixed bug [#17420][pear-17420] : Uncaught exception when comment before function brace
- Fixed bug [#17503][pear-17503] : closures formatting is not supported

[pear-16694]: https://pear.php.net/bugs/bug.php?id=16694
[pear-17048]: https://pear.php.net/bugs/bug.php?id=17048
[pear-17054]: https://pear.php.net/bugs/bug.php?id=17054
[pear-17095]: https://pear.php.net/bugs/bug.php?id=17095
[pear-17096]: https://pear.php.net/bugs/bug.php?id=17096
[pear-17144]: https://pear.php.net/bugs/bug.php?id=17144
[pear-17236]: https://pear.php.net/bugs/bug.php?id=17236
[pear-17243]: https://pear.php.net/bugs/bug.php?id=17243
[pear-17313]: https://pear.php.net/bugs/bug.php?id=17313
[pear-17331]: https://pear.php.net/bugs/bug.php?id=17331
[pear-17337]: https://pear.php.net/bugs/bug.php?id=17337
[pear-17420]: https://pear.php.net/bugs/bug.php?id=17420
[pear-17441]: https://pear.php.net/bugs/bug.php?id=17441
[pear-17503]: https://pear.php.net/bugs/bug.php?id=17503

## 1.2.2 - 2010-01-27

### Changed
- The core PHP_CodeSniffer_File methods now understand the concept of closures (feature request [#16866][pear-16866])
    - Thanks to [Christian Kaps][@akkie] for the sample code
- Sniffs can now specify violation codes for each error and warning they add
    - Future versions will allow you to override messages and severities using these codes
    - Specifying a code is optional, but will be required if you wish to support overriding
- All reports have been broken into separate classes
    - Command line usage and report output remains the same
    - Thanks to Gabriele Santini for the patch
- Added an interactive mode that can be enabled using the -a command line argument
    - Scans files and stops when it finds a file with errors
    - Waits for user input to recheck the file (hopefully you fixed the errors) or skip the file
    - Useful for very large code bases where full rechecks take a while
- The reports now show the correct number of errors and warnings found
- The isCamelCaps method now allows numbers in class names
- The JS tokenizer now correctly identifies boolean and bitwise AND and OR tokens
- The JS tokenizer now correctly identifies regular expressions used in conditions
- PEAR ValidFunctionNameSniff now ignores closures
- Squiz standard now uses the PEAR setting of 85 chars for LineLengthSniff
- Squiz ControlStructureSpacingSniff now ensure there are no spaces around parentheses
- Squiz LongConditionClosingCommentSniff now checks for comments at the end of try/catch statements
- Squiz LongConditionClosingCommentSniff now checks validity of comments for short structures if they exist
- Squiz IncrementDecrementUsageSniff now has better checking to ensure it only looks at simple variable assignments
- Squiz PostStatementCommentSniff no longer throws errors for end function comments
- Squiz InlineCommentSniff no longer throws errors for end function comments
- Squiz OperatorBracketSniff now allows simple arithmetic operations in SWITCH conditions
- Squiz ValidFunctionNameSniff now ignores closures
- Squiz MethodScopeSniff now ignores closures
- Squiz ClosingDeclarationCommentSniff now ignores closures
- Squiz GlobalFunctionSniff now ignores closures
- Squiz DisallowComparisonAssignmentSniff now ignores the assigning of arrays
- Squiz DisallowObjectStringIndexSniff now allows indexes that contain dots and reserved words
- Squiz standard now throws nesting level and cyclomatic complexity errors at much higher levels
- Squiz CommentedOutCodeSniff now ignores common comment framing characters
- Squiz ClassCommentSniff now ensures the open comment tag is the only content on the first line
- Squiz FileCommentSniff now ensures the open comment tag is the only content on the first line
- Squiz FunctionCommentSniff now ensures the open comment tag is the only content on the first line
- Squiz VariableCommentSniff now ensures the open comment tag is the only content on the first line
- Squiz NonExecutableCodeSniff now warns about empty return statements that are not required
- Removed ForbiddenStylesSniff from Squiz standard
    - It is now in the MySource standard as BrowserSpecificStylesSniff
    - New BrowserSpecificStylesSniff ignores files with browser-specific suffixes
- MySource IncludeSystemSniff no longer throws errors when extending the Exception class
- MySource IncludeSystemSniff no longer throws errors for the abstract widget class
- MySource IncludeSystemSniff and UnusedSystemSniff now allow includes inside IF statements
- MySource IncludeSystemSniff no longer throws errors for included widgets inside methods
- MySource GetRequestDataSniff now throws errors for using $_FILES
- MySource CreateWidgetTypeCallbackSniff now allows return statements in nested functions
- MySource DisallowSelfActionsSniff now ignores abstract classes

### Fixed
- Fixed a problem with the SVN pre-commit hook for PHP versions without vertical whitespace regex support
- Fixed bug [#16740][pear-16740] : False positives for heredoc strings and unused parameter sniff
- Fixed bug [#16794][pear-16794] : ValidLogicalOperatorsSniff doesn't report operators not in lowercase
- Fixed bug [#16804][pear-16804] : Report filename is shortened too much
- Fixed bug [#16821][pear-16821] : Bug in Squiz_Sniffs_WhiteSpace_OperatorSpacingSniff
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- Fixed bug [#16836][pear-16836] : Notice raised when using semicolon to open case
- Fixed bug [#16855][pear-16855] : Generic standard sniffs incorrectly for define() method
- Fixed bug [#16865][pear-16865] : Two bugs in Squiz_Sniffs_WhiteSpace_OperatorSpacingSniff
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- Fixed bug [#16902][pear-16902] : Inline If Declaration bug
- Fixed bug [#16960][pear-16960] : False positive for late static binding in Squiz/ScopeKeywordSpacingSniff
    - Thanks to [Jakub Tománek][pear-thezero] for the patch
- Fixed bug [#16976][pear-16976] : The phpcs attempts to process symbolic links that don't resolve to files
- Fixed bug [#17017][pear-17017] : Including one file in the files sniffed alters errors reported for another file

[pear-16740]: https://pear.php.net/bugs/bug.php?id=16740
[pear-16794]: https://pear.php.net/bugs/bug.php?id=16794
[pear-16804]: https://pear.php.net/bugs/bug.php?id=16804
[pear-16821]: https://pear.php.net/bugs/bug.php?id=16821
[pear-16836]: https://pear.php.net/bugs/bug.php?id=16836
[pear-16855]: https://pear.php.net/bugs/bug.php?id=16855
[pear-16865]: https://pear.php.net/bugs/bug.php?id=16865
[pear-16866]: https://pear.php.net/bugs/bug.php?id=16866
[pear-16902]: https://pear.php.net/bugs/bug.php?id=16902
[pear-16960]: https://pear.php.net/bugs/bug.php?id=16960
[pear-16976]: https://pear.php.net/bugs/bug.php?id=16976
[pear-17017]: https://pear.php.net/bugs/bug.php?id=17017

## 1.2.1 - 2009-11-17

### Changed
- Added a new report type --report=svnblame to show how many errors and warnings were committed by each author
    - Also shows the percentage of their code that are errors and warnings
    - Requires you to have the SVN command in your path
    - Make sure SVN is storing usernames and passwords (if required) or you will need to enter them for each file
    - You can also use the -s command line argument to see the different types of errors authors are committing
    - You can use the -v command line argument to see all authors, even if they have no errors or warnings
- Added a new command line argument --report-width to allow you to set the column width of screen reports
    - Reports won't accept values less than 70 or else they get too small
    - Can also be set via a config var: phpcs --config-set report_width 100
- You can now get PHP_CodeSniffer to ignore a whole file by adding @codingStandardsIgnoreFile in the content
    - If you put it in the first two lines the file won't even be tokenized, so it will be much quicker
- Reports now print their file lists in alphabetical order
- PEAR FunctionDeclarationSniff now reports error for incorrect closing bracket placement in multi-line definitions
- Added Generic CallTimePassByReferenceSniff to prohibit the passing of variables into functions by reference
    - Thanks to Florian Grandel for the contribution
- Added Squiz DisallowComparisonAssignmentSniff to ban the assignment of comparison values to a variable
- Added Squiz DuplicateStyleDefinitionSniff to check for duplicate CSS styles in a single class block
- Squiz ArrayDeclarationSniff no longer checks the case of array indexes because that is not its job
- Squiz PostStatementCommentSniff now allows end comments for class member functions
- Squiz InlineCommentSniff now supports the checking of JS files
- MySource CreateWidgetTypeCallbackSniff now allows the callback to be passed to another function
- MySource CreateWidgetTypeCallbackSniff now correctly ignores callbacks used inside conditions
- Generic MultipleStatementAlignmentSniff now enforces a single space before equals sign if max padding is reached
- Fixed a problem in the JS tokenizer where regular expressions containing \// were not converted correctly
- Fixed a problem tokenizing CSS files where multiple ID targets on a line would look like comments
- Fixed a problem tokenizing CSS files where class names containing a colon looked like style definitions
- Fixed a problem tokenizing CSS files when style statements had empty url() calls
- Fixed a problem tokenizing CSS colours with the letter E in first half of the code
- Squiz ColonSpacingSniff now ensures it is only checking style definitions in CSS files and not class names
- Squiz DisallowComparisonAssignmentSniff no longer reports errors when assigning the return value of a function
- CSS tokenizer now correctly supports multi-line comments
- When only the case of var names differ for function comments, the error now indicates the case is different

### Fixed
- Fixed an issue with Generic UnnecessaryStringConcatSniff where it incorrectly suggested removing a concat
- Fixed bug [#16530][pear-16530] : ScopeIndentSniff reports false positive
- Fixed bug [#16533][pear-16533] : Duplicate errors and warnings
- Fixed bug [#16563][pear-16563] : Check file extensions problem in phpcs-svn-pre-commit
    - Thanks to [Kaijung Chen][pear-et3w503] for the patch
- Fixed bug [#16592][pear-16592] : Object operator indentation incorrect when first operator is on a new line
- Fixed bug [#16641][pear-16641] : Notice output
- Fixed bug [#16682][pear-16682] : Squiz_Sniffs_Strings_DoubleQuoteUsageSniff reports string "\0" as invalid
- Fixed bug [#16683][pear-16683] : Typing error in PHP_CodeSniffer_CommentParser_AbstractParser
- Fixed bug [#16684][pear-16684] : Bug in Squiz_Sniffs_PHP_NonExecutableCodeSniff
- Fixed bug [#16692][pear-16692] : Spaces in paths in Squiz_Sniffs_Debug_JavaScriptLintSniff
    - Thanks to [Jaroslav Hanslík][@kukulich] for the patch
- Fixed bug [#16696][pear-16696] : Spelling error in MultiLineConditionSniff
- Fixed bug [#16697][pear-16697] : MultiLineConditionSniff incorrect result with inline IF
- Fixed bug [#16698][pear-16698] : Notice in JavaScript Tokenizer
- Fixed bug [#16736][pear-16736] : Multi-files sniffs aren't processed when FILE is a single directory
    - Thanks to [Alexey Shein][pear-conf] for the patch
- Fixed bug [#16792][pear-16792] : Bug in Generic_Sniffs_PHP_ForbiddenFunctionsSniff

[pear-16530]: https://pear.php.net/bugs/bug.php?id=16530
[pear-16533]: https://pear.php.net/bugs/bug.php?id=16533
[pear-16563]: https://pear.php.net/bugs/bug.php?id=16563
[pear-16592]: https://pear.php.net/bugs/bug.php?id=16592
[pear-16641]: https://pear.php.net/bugs/bug.php?id=16641
[pear-16682]: https://pear.php.net/bugs/bug.php?id=16682
[pear-16683]: https://pear.php.net/bugs/bug.php?id=16683
[pear-16684]: https://pear.php.net/bugs/bug.php?id=16684
[pear-16692]: https://pear.php.net/bugs/bug.php?id=16692
[pear-16696]: https://pear.php.net/bugs/bug.php?id=16696
[pear-16697]: https://pear.php.net/bugs/bug.php?id=16697
[pear-16698]: https://pear.php.net/bugs/bug.php?id=16698
[pear-16736]: https://pear.php.net/bugs/bug.php?id=16736
[pear-16792]: https://pear.php.net/bugs/bug.php?id=16792

## 1.2.0 - 2009-08-17

### Changed
- Installed standards are now favoured over custom standards when using the cmd line arg with relative paths
- Unit tests now use a lot less memory while running
- Squiz standard now uses Generic EmptyStatementSniff but throws errors instead of warnings
- Squiz standard now uses Generic UnusedFunctionParameterSniff
- Removed unused ValidArrayIndexNameSniff from the Squiz standard

### Fixed
- Fixed bug [#16424][pear-16424] : SubversionPropertiesSniff print PHP Warning
- Fixed bug [#16450][pear-16450] : Constant `PHP_CODESNIFFER_VERBOSITY` already defined (unit tests)
- Fixed bug [#16453][pear-16453] : function declaration long line splitted error
- Fixed bug [#16482][pear-16482] : phpcs-svn-pre-commit ignores extensions parameter

[pear-16424]: https://pear.php.net/bugs/bug.php?id=16424
[pear-16450]: https://pear.php.net/bugs/bug.php?id=16450
[pear-16453]: https://pear.php.net/bugs/bug.php?id=16453
[pear-16482]: https://pear.php.net/bugs/bug.php?id=16482

## 1.2.0RC3 - 2009-07-07

### Changed
- You can now use @codingStandardsIgnoreStart and @...End comments to suppress messages (feature request [#14002][pear-14002])
- A warning is now included for files without any code when short_open_tag is set to Off (feature request [#12952][pear-12952])
- You can now use relative paths to your custom standards with the --standard cmd line arg (feature request [#14967][pear-14967])
- You can now override magic methods and functions in PEAR ValidFunctionNameSniff (feature request [#15830][pear-15830])
- MySource IncludeSystemSniff now recognises widget action classes
- MySource IncludeSystemSniff now knows about unit test classes and changes rules accordingly

[pear-12952]: https://pear.php.net/bugs/bug.php?id=12952
[pear-14002]: https://pear.php.net/bugs/bug.php?id=14002
[pear-14967]: https://pear.php.net/bugs/bug.php?id=14967
[pear-15830]: https://pear.php.net/bugs/bug.php?id=15830

## 1.2.0RC2 - 2009-05-25

### Changed
- Test suite can now be run using the full path to `AllTests.php` (feature request [#16179][pear-16179])

### Fixed
- Fixed bug [#15980][pear-15980] : PHP_CodeSniffer change PHP current directory
    - Thanks to [Dolly Aswin Harahap][pear-dollyaswin] for the patch
- Fixed bug [#16001][pear-16001] : Notice triggered
- Fixed bug [#16054][pear-16054] : phpcs-svn-pre-commit not showing any errors
- Fixed bug [#16071][pear-16071] : Fatal error: Uncaught PHP_CodeSniffer_Exception
- Fixed bug [#16170][pear-16170] : Undefined Offset -1 in `MultiLineConditionSniff.php` on line 68
- Fixed bug [#16175][pear-16175] : Bug in Squiz-IncrementDecrementUsageSniff

[pear-15980]: https://pear.php.net/bugs/bug.php?id=15980
[pear-16001]: https://pear.php.net/bugs/bug.php?id=16001
[pear-16054]: https://pear.php.net/bugs/bug.php?id=16054
[pear-16071]: https://pear.php.net/bugs/bug.php?id=16071
[pear-16170]: https://pear.php.net/bugs/bug.php?id=16170
[pear-16175]: https://pear.php.net/bugs/bug.php?id=16175
[pear-16179]: https://pear.php.net/bugs/bug.php?id=16179

## 1.2.0RC1 - 2009-03-09

### Changed
- Reports that are output to a file now include a trailing newline at the end of the file
- Fixed sniff names not shown in -vvv token processing output
- Added Generic SubversionPropertiesSniff to check that specific svn props are set for files
    - Thanks to Jack Bates for the contribution
- The PHP version check can now be overridden in classes that extend PEAR FileCommentSniff
    - Thanks to [Helgi Þormar Þorbjörnsson][@helgi] for the suggestion
- Added Generic ConstructorNameSniff to check for PHP4 constructor name usage
    - Thanks to Leif Wickland for the contribution
- Squiz standard now supports multi-line function and condition sniffs from PEAR standard
- Squiz standard now uses Generic ConstructorNameSniff
- Added MySource GetRequestDataSniff to ensure REQUEST, GET and POST are not accessed directly
- Squiz OperatorBracketSniff now allows square brackets in simple unbracketed operations

### Fixed
- Fixed the incorrect tokenizing of multi-line block comments in CSS files
- Fixed bug [#15383][pear-15383] : Uncaught PHP_CodeSniffer_Exception
- Fixed bug [#15408][pear-15408] : An unexpected exception has been caught: Undefined offset: 2
- Fixed bug [#15519][pear-15519] : Uncaught PHP_CodeSniffer_Exception
- Fixed bug [#15624][pear-15624] : Pre-commit hook fails with PHP errors
- Fixed bug [#15661][pear-15661] : Uncaught PHP_CodeSniffer_Exception
- Fixed bug [#15722][pear-15722] : "declare(encoding = 'utf-8');" leads to "Missing file doc comment"
- Fixed bug [#15910][pear-15910] : Object operator indention not calculated correctly

[pear-15383]: https://pear.php.net/bugs/bug.php?id=15383
[pear-15408]: https://pear.php.net/bugs/bug.php?id=15408
[pear-15519]: https://pear.php.net/bugs/bug.php?id=15519
[pear-15624]: https://pear.php.net/bugs/bug.php?id=15624
[pear-15661]: https://pear.php.net/bugs/bug.php?id=15661
[pear-15722]: https://pear.php.net/bugs/bug.php?id=15722
[pear-15910]: https://pear.php.net/bugs/bug.php?id=15910

## 1.2.0a1 - 2008-12-18

### Changed
- PHP_CodeSniffer now has a CSS tokenizer for checking CSS files
- Added support for a new multi-file sniff that sniffs all processed files at once
- Added new output format --report=emacs to output errors using the emacs standard compile output format
    - Thanks to Len Trigg for the contribution
- Reports can now be written to a file using the --report-file command line argument (feature request [#14953][pear-14953])
    - The report is also written to screen when using this argument
- The CheckStyle, CSV and XML reports now include a source for each error and warning (feature request [#13242][pear-13242])
    - A new report type --report=source can be used to show you the most common errors in your files
- Added new command line argument -s to show error sources in all reports
- Added new command line argument --sniffs to specify a list of sniffs to restrict checking to
    - Uses the sniff source codes that are optionally displayed in reports
- Changed the max width of error lines from 80 to 79 chars to stop blank lines in the default windows cmd window
- PHP_CodeSniffer now has a token for an asperand (@ symbol) so sniffs can listen for them
    - Thanks to Andy Brockhurst for the patch
- Added Generic DuplicateClassNameSniff that will warn if the same class name is used in multiple files
    - Not currently used by any standard; more of a multi-file sniff sample than anything useful
- Added Generic NoSilencedErrorsSniff that warns if PHP errors are being silenced using the @ symbol
    - Thanks to Andy Brockhurst for the contribution
- Added Generic UnnecessaryStringConcatSniff that checks for two strings being concatenated
- Added PEAR FunctionDeclarationSniff to enforce the new multi-line function declaration PEAR standard
- Added PEAR MultiLineAssignmentSniff to enforce the correct indentation of multi-line assignments
- Added PEAR MultiLineConditionSniff to enforce the new multi-line condition PEAR standard
- Added PEAR ObjectOperatorIndentSniff to enforce the new chained function call PEAR standard
- Added MySource DisallowSelfActionSniff to ban the use of self::method() calls in Action classes
- Added MySource DebugCodeSniff to ban the use of Debug::method() calls
- Added MySource CreateWidgetTypeCallback sniff to check callback usage in widget type create methods
- Added Squiz DisallowObjectStringIndexSniff that forces object dot notation in JavaScript files
    - Thanks to [Sertan Danis][@sertand] for the contribution
- Added Squiz DiscouragedFunctionsSniff to warn when using debug functions
- Added Squiz PropertyLabelSniff to check whitespace around colons in JS property and label declarations
- Added Squiz DuplicatePropertySniff to check for duplicate property names in JS classes
- Added Squiz ColonSpacingSniff to check for spacing around colons in CSS style definitions
- Added Squiz SemicolonSpacingSniff to check for spacing around semicolons in CSS style definitions
- Added Squiz IndentationSniff to check for correct indentation of CSS files
- Added Squiz ColourDefinitionSniff to check that CSS colours are defined in uppercase and using shorthand
- Added Squiz EmptyStyleDefinitionSniff to check for CSS style definitions without content
- Added Squiz EmptyClassDefinitionSniff to check for CSS class definitions without content
- Added Squiz ClassDefinitionOpeningBraceSpaceSniff to check for spaces around opening brace of CSS class definitions
- Added Squiz ClassDefinitionClosingBraceSpaceSniff to check for a single blank line after CSS class definitions
- Added Squiz ClassDefinitionNameSpacingSniff to check for a blank lines inside CSS class definition names
- Added Squiz DisallowMultipleStyleDefinitionsSniff to check for multiple style definitions on a single line
- Added Squiz DuplicateClassDefinitionSniff to check for duplicate CSS class blocks that can be merged
- Added Squiz ForbiddenStylesSniff to check for usage of browser specific styles
- Added Squiz OpacitySniff to check for incorrect opacity values in CSS
- Added Squiz LowercaseStyleDefinitionSniff to check for styles that are not defined in lowercase
- Added Squiz MissingColonSniff to check for style definitions where the colon has been forgotten
- Added Squiz MultiLineFunctionDeclarationSniff to check that multi-line declarations contain one param per line
- Added Squiz JSLintSniff to check for JS errors using the jslint.js script through Rhino
    - Set jslint path using phpcs --config-set jslint_path /path/to/jslint.js
    - Set rhino path using phpcs --config-set rhino_path /path/to/rhino
- Added Generic TodoSniff that warns about comments that contain the word TODO
- Removed MultipleStatementAlignmentSniff from the PEAR standard as alignment is now optional
- Generic ForbiddenFunctionsSniff now has protected member var to specify if it should use errors or warnings
- Generic MultipleStatementAlignmentSniff now has correct error message if assignment is on a new line
- Generic MultipleStatementAlignmentSniff now has protected member var to allow it to ignore multi-line assignments
- Generic LineEndingsSniff now supports checking of JS files
- Generic LineEndingsSniff now supports checking of CSS files
- Generic DisallowTabIndentSniff now supports checking of CSS files
- Squiz DoubleQuoteUsageSniff now bans the use of variables in double quoted strings in favour of concatenation
- Squiz SuperfluousWhitespaceSniff now supports checking of JS files
- Squiz SuperfluousWhitespaceSniff now supports checking of CSS files
- Squiz DisallowInlineIfSniff now supports checking of JS files
- Squiz SemicolonSpacingSniff now supports checking of JS files
- Squiz PostStatementCommentSniff now supports checking of JS files
- Squiz FunctionOpeningBraceSpacingSniff now supports checking of JS files
- Squiz FunctionClosingBraceSpacingSniff now supports checking of JS files
    - Empty JS functions must have their opening and closing braces next to each other
- Squiz ControlStructureSpacingSniff now supports checking of JS files
- Squiz LongConditionClosingCommentSniff now supports checking of JS files
- Squiz OperatorSpacingSniff now supports checking of JS files
- Squiz SwitchDeclarationSniff now supports checking of JS files
- Squiz CommentedOutCodeSniff now supports checking of CSS files
- Squiz DisallowSizeFunctionsInLoopsSniff now supports checking of JS files for the use of object.length
- Squiz DisallowSizeFunctionsInLoopsSniff no longer complains about size functions outside of the FOR condition
- Squiz ControlStructureSpacingSniff now bans blank lines at the end of a control structure
- Squiz ForLoopDeclarationSniff no longer throws errors for JS FOR loops without semicolons
- Squiz MultipleStatementAlignmentSniff no longer throws errors if a statement would take more than 8 spaces to align
- Squiz standard now uses Generic TodoSniff
- Squiz standard now uses Generic UnnecessaryStringConcatSniff
- Squiz standard now uses PEAR MultiLineAssignmentSniff
- Squiz standard now uses PEAR MultiLineConditionSniff
- Zend standard now uses OpeningFunctionBraceBsdAllmanSniff (feature request [#14647][pear-14647])
- MySource JoinStringsSniff now bans the use of inline array joins and suggests the + operator
- Fixed incorrect errors that can be generated from abstract scope sniffs when moving to a new file
- Core tokenizer now matches orphaned curly braces in the same way as square brackets
- Whitespace tokens at the end of JS files are now added to the token stack
- JavaScript tokenizer now identifies properties and labels as new token types
- JavaScript tokenizer now identifies object definitions as a new token type and matches curly braces for them
- JavaScript tokenizer now identifies DIV_EQUAL and MUL_EQUAL tokens
- Improved regular expression detection in the JavaScript tokenizer
- Improve AbstractPatternSniff support so it can listen for any token type, not just weighted tokens

### Fixed
- Fixed Squiz DoubleQuoteUsageSniff so it works correctly with short_open_tag=Off
- Fixed bug [#14409][pear-14409] : Output of warnings to log file
- Fixed bug [#14520][pear-14520] : Notice: Undefined offset: 1 in `CodeSniffer/File.php` on line
- Fixed bug [#14637][pear-14637] : Call to processUnknownArguments() misses second parameter $pos
    - Thanks to [Peter Buri][pear-burci] for the patch
- Fixed bug [#14889][pear-14889] : Lack of clarity: licence or license
- Fixed bug [#15008][pear-15008] : Nested Parentheses in Control Structure Sniffs
- Fixed bug [#15091][pear-15091] : pre-commit hook attempts to sniff folders
    - Thanks to [Bruce Weirdan][pear-weirdan] for the patch
- Fixed bug [#15124][pear-15124] : `AbstractParser.php` uses deprecated `split()` function
    - Thanks to [Sebastian Bergmann][@sebastianbergmann] for the patch
- Fixed bug [#15188][pear-15188] : PHPCS vs HEREDOC strings
- Fixed bug [#15231][pear-15231] : Notice: Uninitialized string offset: 0 in `FileCommentSniff.php` on line 555
- Fixed bug [#15336][pear-15336] : Notice: Undefined offset: 2 in `CodeSniffer/File.php` on line

[pear-13242]: https://pear.php.net/bugs/bug.php?id=13242
[pear-14409]: https://pear.php.net/bugs/bug.php?id=14409
[pear-14520]: https://pear.php.net/bugs/bug.php?id=14520
[pear-14637]: https://pear.php.net/bugs/bug.php?id=14637
[pear-14647]: https://pear.php.net/bugs/bug.php?id=14647
[pear-14889]: https://pear.php.net/bugs/bug.php?id=14889
[pear-14953]: https://pear.php.net/bugs/bug.php?id=14953
[pear-15008]: https://pear.php.net/bugs/bug.php?id=15008
[pear-15091]: https://pear.php.net/bugs/bug.php?id=15091
[pear-15124]: https://pear.php.net/bugs/bug.php?id=15124
[pear-15188]: https://pear.php.net/bugs/bug.php?id=15188
[pear-15231]: https://pear.php.net/bugs/bug.php?id=15231
[pear-15336]: https://pear.php.net/bugs/bug.php?id=15336

## 1.1.0 - 2008-07-14

### Changed
- PEAR FileCommentSniff now allows tag orders to be overridden in child classes
    - Thanks to Jeff Hodsdon for the patch
- Added Generic DisallowMultipleStatementsSniff to ensure there is only one statement per line
- Squiz standard now uses DisallowMultipleStatementsSniff

### Fixed
- Fixed error in Zend ValidVariableNameSniff when checking vars in form: $class->{$var}
- Fixed bug [#14077][pear-14077] : Fatal error: Uncaught PHP_CodeSniffer_Exception: $stackPtr is not a class member
- Fixed bug [#14168][pear-14168] : Global Function -> Static Method and __autoload()
- Fixed bug [#14238][pear-14238] : Line length not checked at last line of a file
- Fixed bug [#14249][pear-14249] : wrong detection of scope_opener
- Fixed bug [#14250][pear-14250] : ArrayDeclarationSniff emit warnings at malformed array
- Fixed bug [#14251][pear-14251] : --extensions option doesn't work

## 1.1.0RC3 - 2008-07-03

### Changed
- PEAR FileCommentSniff now allows tag orders to be overridden in child classes
    - Thanks to Jeff Hodsdon for the patch
- Added Generic DisallowMultipleStatementsSniff to ensure there is only one statement per line
- Squiz standard now uses DisallowMultipleStatementsSniff

### Fixed
- Fixed error in Zend ValidVariableNameSniff when checking vars in form: $class->{$var}
- Fixed bug [#14077][pear-14077] : Fatal error: Uncaught PHP_CodeSniffer_Exception: $stackPtr is not a class member
- Fixed bug [#14168][pear-14168] : Global Function -> Static Method and __autoload()
- Fixed bug [#14238][pear-14238] : Line length not checked at last line of a file
- Fixed bug [#14249][pear-14249] : wrong detection of scope_opener
- Fixed bug [#14250][pear-14250] : ArrayDeclarationSniff emit warnings at malformed array
- Fixed bug [#14251][pear-14251] : --extensions option doesn't work

[pear-14077]: https://pear.php.net/bugs/bug.php?id=14077
[pear-14168]: https://pear.php.net/bugs/bug.php?id=14168
[pear-14238]: https://pear.php.net/bugs/bug.php?id=14238
[pear-14249]: https://pear.php.net/bugs/bug.php?id=14249
[pear-14250]: https://pear.php.net/bugs/bug.php?id=14250
[pear-14251]: https://pear.php.net/bugs/bug.php?id=14251

## 1.1.0RC2 - 2008-06-13

### Changed
- Permission denied errors now stop script execution but still display current errors (feature request [#14076][pear-14076])
- Added Squiz ValidArrayIndexNameSniff to ensure array indexes do not use camel case
- Squiz ArrayDeclarationSniff now ensures arrays are not declared with camel case index values
- PEAR ValidVariableNameSniff now alerts about a possible parse error for member vars inside an interface

### Fixed
- Fixed bug [#13921][pear-13921] : js parsing fails for comments on last line of file
- Fixed bug [#13922][pear-13922] : crash in case of malformed (but tokenized) PHP file
    - PEAR and Squiz ClassDeclarationSniff now throw warnings for possible parse errors
    - Squiz ValidClassNameSniff now throws warning for possible parse errors
    - Squiz ClosingDeclarationCommentSniff now throws additional warnings for parse errors

[pear-13921]: https://pear.php.net/bugs/bug.php?id=13921
[pear-13922]: https://pear.php.net/bugs/bug.php?id=13922
[pear-14076]: https://pear.php.net/bugs/bug.php?id=14076

## 1.1.0RC1 - 2008-05-13

### Changed
- Added support for multiple tokenizers so PHP_CodeSniffer can check more than just PHP files
    - PHP_CodeSniffer now has a JS tokenizer for checking JavaScript files
    - Sniffs need to be updated to work with additional tokenizers, or new sniffs written for them
- phpcs now exits with status 2 if the tokenizer extension has been disabled (feature request [#13269][pear-13269])
- Added scripts/phpcs-svn-pre-commit that can be used as an SVN pre-commit hook
    - Also reworked the way the phpcs script works to make it easier to wrap it with other functionality
    - Thanks to Jack Bates for the contribution
- Fixed error in phpcs error message when a supplied file does not exist
- Fixed a cosmetic error in AbstractPatternSniff where the "found" string was missing some content
- Added sniffs that implement part of the PMD rule catalog to the Generic standard
    - Thanks to [Manuel Pichler][@manuelpichler] for the contribution of all these sniffs.
- Squiz FunctionCommentThrowTagSniff no longer throws errors for function that only throw variables
- Generic ScopeIndentSniff now has private member to enforce exact indent matching
- Replaced Squiz DisallowCountInLoopsSniff with Squiz DisallowSizeFunctionsInLoopsSniff
    - Thanks to Jan Miczaika for the sniff
- Squiz BlockCommentSniff now checks inline doc block comments
- Squiz InlineCommentSniff now checks inline doc block comments
- Squiz BlockCommentSniff now checks for no blank line before first comment in a function
- Squiz DocCommentAlignmentSniff now ignores inline doc block comments
- Squiz ControlStructureSpacingSniff now ensures no blank lines at the start of control structures
- Squiz ControlStructureSpacingSniff now ensures no blank lines between control structure closing braces
- Squiz IncrementDecrementUsageSniff now ensures inc/dec ops are bracketed in string concats
- Squiz IncrementDecrementUsageSniff now ensures inc/dec ops are not used in arithmetic operations
- Squiz FunctionCommentSniff no longer throws errors if return value is mixed but function returns void somewhere
- Squiz OperatorBracketSniff no allows function call brackets to count as operator brackets
- Squiz DoubleQuoteUsageSniff now supports \x \f and \v (feature request [#13365][pear-13365])
- Squiz ComparisonOperatorUsageSniff now supports JS files
- Squiz ControlSignatureSniff now supports JS files
- Squiz ForLoopDeclarationSniff now supports JS files
- Squiz OperatorBracketSniff now supports JS files
- Squiz InlineControlStructureSniff now supports JS files
- Generic LowerCaseConstantSniff now supports JS files
- Generic DisallowTabIndentSniff now supports JS files
- Generic MultipleStatementAlignmentSniff now supports JS files
- Added Squiz ObjectMemberCommaSniff to ensure the last member of a JS object is not followed by a comma
- Added Squiz ConstantCaseSniff to ensure the PHP constants are uppercase and JS lowercase
- Added Squiz JavaScriptLintSniff to check JS files with JSL
    - Set path using phpcs --config-set jsl_path /path/to/jsl
- Added MySource FirebugConsoleSniff to ban the use of "console" for JS variable and function names
- Added MySource JoinStringsSniff to enforce the use of join() to concatenate JS strings
- Added MySource AssignThisSniff to ensure this is only assigned to a var called self
- Added MySource DisallowNewWidgetSniff to ban manual creation of widget objects
- Removed warning shown in Zend CodeAnalyzerSniff when the ZCA path is not set

### Fixed
- Fixed error in Squiz ValidVariableNameSniff when checking vars in the form $obj->$var
- Fixed error in Squiz DisallowMultipleAssignmentsSniff when checking vars in the form $obj->$var
- Fixed error in Squiz InlineCommentSniff where comments for class constants were seen as inline
- Fixed error in Squiz BlockCommentSniff where comments for class constants were not ignored
- Fixed error in Squiz OperatorBracketSniff where negative numbers were ignored during comparisons
- Fixed error in Squiz FunctionSpacingSniff where functions after member vars reported incorrect spacing
- Fixed bug [#13062][pear-13062] : Interface comments aren't handled in PEAR standard
    - Thanks to [Manuel Pichler][@manuelpichler] for the path
- Fixed bug [#13119][pear-13119] : PHP minimum requirement need to be fix
- Fixed bug [#13156][pear-13156] : Bug in Squiz_Sniffs_PHP_NonExecutableCodeSniff
- Fixed bug [#13158][pear-13158] : Strange behaviour in AbstractPatternSniff
- Fixed bug [#13169][pear-13169] : Undefined variables
- Fixed bug [#13178][pear-13178] : Catch exception in `File.php`
- Fixed bug [#13254][pear-13254] : Notices output in checkstyle report causes XML issues
- Fixed bug [#13446][pear-13446] : crash with src of phpMyAdmin
    - Thanks to [Manuel Pichler][@manuelpichler] for the path

[pear-13062]: https://pear.php.net/bugs/bug.php?id=13062
[pear-13119]: https://pear.php.net/bugs/bug.php?id=13119
[pear-13156]: https://pear.php.net/bugs/bug.php?id=13156
[pear-13158]: https://pear.php.net/bugs/bug.php?id=13158
[pear-13169]: https://pear.php.net/bugs/bug.php?id=13169
[pear-13178]: https://pear.php.net/bugs/bug.php?id=13178
[pear-13254]: https://pear.php.net/bugs/bug.php?id=13254
[pear-13269]: https://pear.php.net/bugs/bug.php?id=13269
[pear-13365]: https://pear.php.net/bugs/bug.php?id=13365
[pear-13446]: https://pear.php.net/bugs/bug.php?id=13446

## 1.0.1a1 - 2008-04-21

### Changed
- Fixed error in PEAR ValidClassNameSniff when checking class names with double underscores
- Moved Squiz InlineControlStructureSniff into Generic standard
- PEAR standard now throws warnings for inline control structures
- Squiz OutputBufferingIndentSniff now ignores the indentation of inline HTML
- MySource IncludeSystemSniff now ignores usage of ZipArchive
- Removed "function" from error messages for Generic function brace sniffs (feature request [#13820][pear-13820])
- Generic UpperCaseConstantSniff no longer throws errors for declare(ticks = ...)
    - Thanks to Josh Snyder for the patch
- Squiz ClosingDeclarationCommentSniff and AbstractVariableSniff now throw warnings for possible parse errors

### Fixed
- Fixed bug [#13827][pear-13827] : AbstractVariableSniff throws "undefined index"
- Fixed bug [#13846][pear-13846] : Bug in Squiz.NonExecutableCodeSniff
- Fixed bug [#13849][pear-13849] : infinite loop in PHP_CodeSniffer_File::findNext()

[pear-13820]: https://pear.php.net/bugs/bug.php?id=13820
[pear-13827]: https://pear.php.net/bugs/bug.php?id=13827
[pear-13846]: https://pear.php.net/bugs/bug.php?id=13846
[pear-13849]: https://pear.php.net/bugs/bug.php?id=13849

## 1.0.1 - 2008-02-04

### Changed
- Squiz ArrayDeclarationSniff now throws error if the array keyword is followed by a space
- Squiz ArrayDeclarationSniff now throws error for empty multi-line arrays
- Squiz ArrayDeclarationSniff now throws error for multi-line arrays with a single value
- Squiz DocCommentAlignmentSniff now checks for a single space before tags inside docblocks
- Squiz ForbiddenFunctionsSniff now disallows is_null() to force use of (=== NULL) instead
- Squiz VariableCommentSniff now continues throwing errors after the first one is found
- Squiz SuperfluousWhitespaceSniff now throws errors for multiple blank lines inside functions
- MySource IncludedSystemSniff now checks extended class names
- MySource UnusedSystemSniff now checks extended and implemented class names
- MySource IncludedSystemSniff now supports includeWidget()
- MySource UnusedSystemSniff now supports includeWidget()
- Added PEAR ValidVariableNameSniff to check that only private member vars are prefixed with an underscore
- Added Squiz DisallowCountInLoopsSniff to check for the use of count() in FOR and WHILE loop conditions
- Added MySource UnusedSystemSniff to check for included classes that are never used

### Fixed
- Fixed a problem that caused the parentheses map to sometimes contain incorrect values
- Fixed bug [#12767][pear-12767] : Cant run phpcs from dir with PEAR subdir
- Fixed bug [#12773][pear-12773] : Reserved variables are not detected in strings
    - Thanks to [Wilfried Loche][pear-wloche] for the patch
- Fixed bug [#12832][pear-12832] : Tab to space conversion does not work
- Fixed bug [#12888][pear-12888] : extra space indentation = Notice: Uninitialized string offset...
- Fixed bug [#12909][pear-12909] : Default generateDocs function does not work under linux
    - Thanks to [Paul Smith][pear-thing2b] for the patch
- Fixed bug [#12957][pear-12957] : PHP 5.3 magic method __callStatic
    - Thanks to [Manuel Pichler][@manuelpichler] for the patch

[pear-12767]: https://pear.php.net/bugs/bug.php?id=12767
[pear-12773]: https://pear.php.net/bugs/bug.php?id=12773
[pear-12832]: https://pear.php.net/bugs/bug.php?id=12832
[pear-12888]: https://pear.php.net/bugs/bug.php?id=12888
[pear-12909]: https://pear.php.net/bugs/bug.php?id=12909
[pear-12957]: https://pear.php.net/bugs/bug.php?id=12957

## 1.0.0 - 2007-12-21

### Changed
- You can now specify the full path to a coding standard on the command line (feature request [#11886][pear-11886])
    - This allows you to use standards that are stored outside of PHP_CodeSniffer's own Standard dir
    - You can also specify full paths in the `CodingStandard.php` include and exclude methods
    - Classes, dirs and files need to be names as if the standard was part of PHP_CodeSniffer
    - Thanks to Dirk Thomas for the doc generator patch and testing
- Modified the scope map to keep checking after 3 lines for some tokens (feature request [#12561][pear-12561])
    - Those tokens that must have an opener (like T_CLASS) now keep looking until EOF
    - Other tokens (like T_FUNCTION) still stop after 3 lines for performance
- You can now escape commas in ignore patterns so they can be matched in file names
    - Thanks to [Carsten Wiedmann][pear-cwiedmann] for the patch
- Config data is now cached in a global var so the file system is not hit so often
    - You can also set config data temporarily for the script if you are using your own external script
    - Pass TRUE as the third argument to PHP_CodeSniffer::setConfigData()
- PEAR ClassDeclarationSniff no longer throws errors for multi-line class declarations
- Squiz ClassDeclarationSniff now ensures there is one blank line after a class closing brace
- Squiz ClassDeclarationSniff now throws errors for a missing end PHP tag after the end class tag
- Squiz IncrementDecrementUsageSniff no longer throws errors when -= and += are being used with vars
- Squiz SwitchDeclarationSniff now throws errors for switch statements that do not contain a case statement
    - Thanks to [Sertan Danis][@sertand] for the patch
- MySource IncludeSystemSniff no longer throws errors for the Util package

### Fixed
- Fixed bug [#12621][pear-12621] : "space after AS" check is wrong
    - Thanks to [Satoshi Oikawa][pear-renoiv] for the patch
- Fixed bug [#12645][pear-12645] : error message is wrong
    - Thanks to [Satoshi Oikawa][pear-renoiv] for the patch
- Fixed bug [#12651][pear-12651] : Increment/Decrement Operators Usage at -1

[pear-11886]: https://pear.php.net/bugs/bug.php?id=11886
[pear-12561]: https://pear.php.net/bugs/bug.php?id=12561
[pear-12621]: https://pear.php.net/bugs/bug.php?id=12621
[pear-12645]: https://pear.php.net/bugs/bug.php?id=12645
[pear-12651]: https://pear.php.net/bugs/bug.php?id=12651

## 1.0.0RC3 - 2007-11-30

### Changed
- Added new command line argument --tab-width that will convert tabs to spaces before testing
    - This allows you to use the existing sniffs that check for spaces even when you use tabs
    - Can also be set via a config var: phpcs --config-set tab_width 4
    - A value of zero (the default) tells PHP_CodeSniffer not to replace tabs with spaces
- You can now change the default report format from "full" to something else
    - Run: phpcs `--config-set report_format [format]`
- Improved performance by optimising the way the scope map is created during tokenizing
- Added new Squiz DisallowInlineIfSniff to disallow the usage of inline IF statements
- Fixed incorrect errors being thrown for nested switches in Squiz SwitchDeclarationSniff
- PEAR FunctionCommentSniff no longer complains about missing comments for @throws tags
- PEAR FunctionCommentSniff now throws error for missing exception class name for @throws tags
- PHP_CodeSniffer_File::isReference() now correctly returns for functions that return references
- Generic LineLengthSniff no longer warns about @version lines with CVS or SVN id tags
- Generic LineLengthSniff no longer warns about @license lines with long URLs
- Squiz FunctionCommentThrowTagSniff no longer complains about throwing variables
- Squiz ComparisonOperatorUsageSniff no longer throws incorrect errors for inline IF statements
- Squiz DisallowMultipleAssignmentsSniff no longer throws errors for assignments in inline IF statements

### Fixed
- Fixed bug [#12455][pear-12455] : CodeSniffer treats content inside heredoc as PHP code
- Fixed bug [#12471][pear-12471] : Checkstyle report is broken
- Fixed bug [#12476][pear-12476] : PHP4 destructors are reported as error
- Fixed bug [#12513][pear-12513] : Checkstyle XML messages need to be utf8_encode()d
    - Thanks to [Sebastian Bergmann][@sebastianbergmann] for the patch.
- Fixed bug [#12517][pear-12517] : getNewlineAfter() and dos files

[pear-12455]: https://pear.php.net/bugs/bug.php?id=12455
[pear-12471]: https://pear.php.net/bugs/bug.php?id=12471
[pear-12476]: https://pear.php.net/bugs/bug.php?id=12476
[pear-12513]: https://pear.php.net/bugs/bug.php?id=12513
[pear-12517]: https://pear.php.net/bugs/bug.php?id=12517

## 1.0.0RC2 - 2007-11-14

### Changed
- Added a new Checkstyle report format
    - Like the current XML format but modified to look like Checkstyle output
    - Thanks to [Manuel Pichler][@manuelpichler] for helping get the format correct
- You can now hide warnings by default
    - Run: phpcs --config-set show_warnings 0
    - If warnings are hidden by default, use the new -w command line argument to override
- Added new command line argument --config-delete to delete a config value and revert to the default
- Improved overall performance by optimising tokenizing and next/prev methods (feature request [#12421][pear-12421])
    - Thanks to [Christian Weiske][@cweiske] for the patch
- Added FunctionCallSignatureSniff to Squiz standard
- Added @subpackage support to file and class comment sniffs in PEAR standard (feature request [#12382][pear-12382])
    - Thanks to [Carsten Wiedmann][pear-cwiedmann] for the patch
- An error is now displayed if you use a PHP version less than 5.1.0 (feature request [#12380][pear-12380])
    - Thanks to [Carsten Wiedmann][pear-cwiedmann] for the patch
- phpcs now exits with status 2 if it receives invalid input (feature request [#12380][pear-12380])
    - This is distinct from status 1, which indicates errors or warnings were found
- Added new Squiz LanguageConstructSpacingSniff to throw errors for additional whitespace after echo etc.
- Removed Squiz ValidInterfaceNameSniff
- PEAR FunctionCommentSniff no longer complains about unknown tags

### Fixed
- Fixed incorrect errors about missing function comments in PEAR FunctionCommentSniff
- Fixed incorrect function docblock detection in Squiz FunctionCommentSniff
- Fixed incorrect errors for list() in Squiz DisallowMultipleAssignmentsSniff
- Errors no longer thrown if control structure is followed by a CASE's BREAK in Squiz ControlStructureSpacingSniff
- Fixed bug [#12368][pear-12368] : Autoloader cannot be found due to include_path override
    - Thanks to [Richard Quadling][pear-rquadling] for the patch
- Fixed bug [#12378][pear-12378] : equal sign alignments problem with while()

[pear-12368]: https://pear.php.net/bugs/bug.php?id=12368
[pear-12378]: https://pear.php.net/bugs/bug.php?id=12378
[pear-12380]: https://pear.php.net/bugs/bug.php?id=12380
[pear-12382]: https://pear.php.net/bugs/bug.php?id=12382
[pear-12421]: https://pear.php.net/bugs/bug.php?id=12421

## 1.0.0RC1 - 2007-11-01

### Changed
- Main phpcs script can now be run from a CVS checkout without installing the package
- Added a new CSV report format
    - Header row indicates what position each element is in
    - Always use the header row to determine positions rather than assuming the format, as it may change
- XML and CSV report formats now contain information about which column the error occurred at
    - Useful if you want to highlight the token that caused the error in a custom application
- Square bracket tokens now have bracket_opener and bracket_closer set
- Added new Squiz SemicolonSpacingSniff to throw errors if whitespace is found before a semicolon
- Added new Squiz ArrayBracketSpacingSniff to throw errors if whitespace is found around square brackets
- Added new Squiz ObjectOperatorSpacingSniff to throw errors if whitespace is found around object operators
- Added new Squiz DisallowMultipleAssignmentsSniff to throw errors if multiple assignments are on the same line
- Added new Squiz ScopeKeywordSpacingSniff to throw errors if there is not a single space after a scope modifier
- Added new Squiz ObjectInstantiationSniff to throw errors if new objects are not assigned to a variable
- Added new Squiz FunctionDuplicateArgumentSniff to throw errors if argument is declared multiple times in a function
- Added new Squiz FunctionOpeningBraceSpaceSniff to ensure there are no blank lines after a function open brace
- Added new Squiz CommentedOutCodeSniff to warn about comments that looks like they are commented out code blocks
- Added CyclomaticComplexitySniff to Squiz standard
- Added NestingLevelSniff to Squiz standard
- Squiz ForbiddenFunctionsSniff now recommends echo() instead of print()
- Squiz ValidLogicalOperatorsSniff now recommends ^ instead of xor
- Squiz SwitchDeclarationSniff now contains more checks
    - A single space is required after the case keyword
    - No space is allowed before the colon in a case or default statement
    - All switch statements now require a default case
    - Default case must contain a break statement
    - Empty default case must contain a comment describing why the default is ignored
    - Empty case statements are not allowed
    - Case and default statements must not be followed by a blank line
    - Break statements must be followed by a blank line or the closing brace
    - There must be no blank line before a break statement
- Squiz standard is now using the PEAR IncludingFileSniff
- PEAR ClassCommentSniff no longer complains about unknown tags
- PEAR FileCommentSniff no longer complains about unknown tags
- PEAR FileCommentSniff now accepts multiple @copyright tags
- Squiz BlockCommentSniff now checks that comment starts with a capital letter
- Squiz InlineCommentSniff now has better checking to ensure comment starts with a capital letter
- Squiz ClassCommentSniff now checks that short and long comments start with a capital letter
- Squiz FunctionCommentSniff now checks that short, long and param comments start with a capital letter
- Squiz VariableCommentSniff now checks that short and long comments start with a capital letter

### Fixed
- Fixed error with multi-token array indexes in Squiz ArrayDeclarationSniff
- Fixed error with checking shorthand IF statements without a semicolon in Squiz InlineIfDeclarationSniff
- Fixed error where constants used as default values in function declarations were seen as type hints
- Fixed bug [#12316][pear-12316] : PEAR is no longer the default standard
- Fixed bug [#12321][pear-12321] : wrong detection of missing function docblock

[pear-12316]: https://pear.php.net/bugs/bug.php?id=12316
[pear-12321]: https://pear.php.net/bugs/bug.php?id=12321

## 0.9.0 - 2007-09-24

### Changed
- Added a config system for setting config data across phpcs runs
- You can now change the default coding standard from PEAR to something else
    - Run: phpcs `--config-set default_standard [standard]`
- Added new Zend coding standard to check code against the Zend Framework standards
    - The complete standard is not yet implemented
    - Specify --standard=Zend to use
    - Thanks to Johann-Peter Hartmann for the contribution of some sniffs
    - Thanks to Holger Kral for the Code Analyzer sniff

## 0.8.0 - 2007-08-08

### Changed
- Added new XML report format; --report=xml (feature request [#11535][pear-11535])
    - Thanks to [Brett Bieber][@saltybeagle] for the patch
- Added new command line argument --ignore to specify a list of files to skip (feature request [#11556][pear-11556])
- Added PHPCS and MySource coding standards into the core install
- Scope map no longer gets confused by curly braces that act as string offsets
- Removed `CodeSniffer/SniffException.php` as it is no longer used
- Unit tests can now be run directly from a CVS checkout
- Made private vars and functions protected in PHP_CodeSniffer class so this package can be overridden
- Added new Metrics category to Generic coding standard
    - Contains Cyclomatic Complexity and Nesting Level sniffs
    - Thanks to Johann-Peter Hartmann for the contribution
- Added new Generic DisallowTabIndentSniff to throw errors if tabs are used for indentation (feature request [#11738][pear-11738])
    - PEAR and Squiz standards use this new sniff to throw more specific indentation errors
- Generic MultipleStatementAlignmentSniff has new private var to set a padding size limit (feature request [#11555][pear-11555])
- Generic MultipleStatementAlignmentSniff can now handle assignments that span multiple lines (feature request [#11561][pear-11561])
- Generic LineLengthSniff now has a max line length after which errors are thrown instead of warnings
    - BC BREAK: Override the protected member var absoluteLineLimit and set it to zero in custom LineLength sniffs
    - Thanks to Johann-Peter Hartmann for the contribution
- Comment sniff errors about incorrect tag orders are now more descriptive (feature request [#11693][pear-11693])

### Fixed
- Fixed bug [#11473][pear-11473] : Invalid CamelCaps name when numbers used in names

[pear-11473]: https://pear.php.net/bugs/bug.php?id=11473
[pear-11535]: https://pear.php.net/bugs/bug.php?id=11535
[pear-11555]: https://pear.php.net/bugs/bug.php?id=11555
[pear-11556]: https://pear.php.net/bugs/bug.php?id=11556
[pear-11561]: https://pear.php.net/bugs/bug.php?id=11561
[pear-11693]: https://pear.php.net/bugs/bug.php?id=11693
[pear-11738]: https://pear.php.net/bugs/bug.php?id=11738

## 0.7.0 - 2007-07-02

### Changed
- BC BREAK: EOL character is now auto-detected and used instead of hard-coded \n
    - Pattern sniffs must now specify "EOL" instead of "\n" or "\r\n" to use auto-detection
    - Please use $phpcsFile->eolChar to check for newlines instead of hard-coding "\n" or "\r\n"
    - Comment parser classes now require you to pass $phpcsFile as an additional argument
- BC BREAK: Included and excluded sniffs now require `.php` extension
    - Please update your coding standard classes and add `.php` to all sniff entries
    - See `CodeSniffer/Standards/PEAR/PEARCodingStandard.php` for an example
- Fixed error where including a directory of sniffs in a coding standard class did not work
- Coding standard classes can now specify a list of sniffs to exclude as well as include (feature request [#11056][pear-11056])
- Two uppercase characters can now be placed side-by-side in class names in Squiz ValidClassNameSniff
- SVN tags now allowed in PEAR file doc blocks (feature request [#11038][pear-11038])
    - Thanks to [Torsten Roehr][pear-troehr] for the patch
- Private methods in commenting sniffs and comment parser are now protected (feature request [#11087][pear-11087])
- Added Generic LineEndingsSniff to check the EOL character of a file
- PEAR standard now only throws one error per file for incorrect line endings (eg. /r/n)
- Command line arg -v now shows number of registered sniffs
- Command line arg -vvv now shows list of registered sniffs
- Squiz ControlStructureSpacingSniff no longer throws errors if the control structure is at the end of the script
- Squiz FunctionCommentSniff now throws error for "return void" if function has return statement
- Squiz FunctionCommentSniff now throws error for functions that return void but specify something else
- Squiz ValidVariableNameSniff now allows multiple uppercase letters in a row
- Squiz ForEachLoopDeclarationSniff now throws error for AS keyword not being lowercase
- Squiz SwitchDeclarationSniff now throws errors for CASE/DEFAULT/BREAK keywords not being lowercase
- Squiz ArrayDeclarationSniff now handles multi-token array values when checking alignment
- Squiz standard now enforces a space after cast tokens
- Generic MultipleStatementAlignmentSniff no longer gets confused by assignments inside FOR conditions
- Generic MultipleStatementAlignmentSniff no longer gets confused by the use of list()
- Added Generic SpaceAfterCastSniff to ensure there is a single space after a cast token
- Added Generic NoSpaceAfterCastSniff to ensure there is no whitespace after a cast token
- Added PEAR ClassDeclarationSniff to ensure the opening brace of a class is on the line after the keyword
- Added Squiz ScopeClosingBraceSniff to ensure closing braces are aligned correctly
- Added Squiz EvalSniff to discourage the use of eval()
- Added Squiz LowercaseDeclarationSniff to ensure all declaration keywords are lowercase
- Added Squiz LowercaseClassKeywordsSniff to ensure all class declaration keywords are lowercase
- Added Squiz LowercaseFunctionKeywordsSniff to ensure all function declaration keywords are lowercase
- Added Squiz LowercasePHPFunctionsSniff to ensure all calls to inbuilt PHP functions are lowercase
- Added Squiz CastSpacingSniff to ensure cast statements don't contain whitespace
- Errors no longer thrown when checking 0 length files with verbosity on

### Fixed
- Fixed bug [#11105][pear-11105] : getIncludedSniffs() not working anymore
    - Thanks to [Blair Robertson][pear-adviva] for the patch
- Fixed bug [#11120][pear-11120] : Uninitialized string offset in `AbstractParser.php` on line 200

[pear-11038]: https://pear.php.net/bugs/bug.php?id=11038
[pear-11056]: https://pear.php.net/bugs/bug.php?id=11056
[pear-11087]: https://pear.php.net/bugs/bug.php?id=11087
[pear-11105]: https://pear.php.net/bugs/bug.php?id=11105
[pear-11120]: https://pear.php.net/bugs/bug.php?id=11120

## 0.6.0 - 2007-05-15

### Changed
- The number of errors and warnings found is now shown for each file while checking the file if verbosity is enabled
- Now using PHP_EOL instead of hard-coded \n so output looks good on Windows (feature request [#10761][pear-10761])
    - Thanks to [Carsten Wiedmann][pear-cwiedmann] for the patch.
- phpcs now exits with status 0 (no errors) or 1 (errors found) (feature request [#10348][pear-10348])
- Added new -l command line argument to stop recursion into directories (feature request [#10979][pear-10979])

### Fixed
- Fixed variable name error causing incorrect error message in Squiz ValidVariableNameSniff
- Fixed bug [#10757][pear-10757] : Error in ControlSignatureSniff
- Fixed bugs [#10751][pear-10751], [#10777][pear-10777] : Sniffer class paths handled incorrectly in Windows
    - Thanks to [Carsten Wiedmann][pear-cwiedmann] for the patch.
- Fixed bug [#10961][pear-10961] : Error "Last parameter comment requires a blank newline after it" thrown
- Fixed bug [#10983][pear-10983] : phpcs outputs notices when checking invalid PHP
- Fixed bug [#10980][pear-10980] : Incorrect warnings for equals sign

[pear-10348]: https://pear.php.net/bugs/bug.php?id=10348
[pear-10751]: https://pear.php.net/bugs/bug.php?id=10751
[pear-10757]: https://pear.php.net/bugs/bug.php?id=10757
[pear-10761]: https://pear.php.net/bugs/bug.php?id=10761
[pear-10777]: https://pear.php.net/bugs/bug.php?id=10777
[pear-10961]: https://pear.php.net/bugs/bug.php?id=10961
[pear-10979]: https://pear.php.net/bugs/bug.php?id=10979
[pear-10980]: https://pear.php.net/bugs/bug.php?id=10980
[pear-10983]: https://pear.php.net/bugs/bug.php?id=10983

## 0.5.0 - 2007-04-17

### Changed
- BC BREAK: Coding standards now require a class to be added so PHP_CodeSniffer can get information from them
    - Please read the end user docs for info about the new class required for all coding standards
- Coding standards can now include sniffs from other standards, or whole standards, without writing new sniff files
- PHP_CodeSniffer_File::isReference() now correctly returns for references in function declarations
- PHP_CodeSniffer_File::isReference() now returns false if you don't pass it a T_BITWISE_AND token
- PHP_CodeSniffer_File now stores the absolute path to the file so sniffs can check file locations correctly
- Fixed undefined index error in AbstractVariableSniff for variables inside an interface function definition
- Added MemberVarSpacingSniff to Squiz standard to enforce one-line spacing between member vars
- Add FunctionCommentThrowTagSniff to Squiz standard to check that @throws tags are correct

### Fixed
- Fixed problems caused by references and type hints in Squiz FunctionDeclarationArgumentSpacingSniff
- Fixed problems with errors not being thrown for some misaligned @param comments in Squiz FunctionCommentSniff
- Fixed badly spaced comma error being thrown for "extends" class in Squiz ClassDeclarationSniff
- Errors no longer thrown for class method names in Generic ForbiddenFunctionsSniff
- Errors no longer thrown for type hints in front of references in Generic UpperCaseConstantNameSniff
- Errors no longer thrown for correctly indented buffered lines in Squiz ScopeIndexSniff
- Errors no longer thrown for user-defined functions named as forbidden functions in Generic ForbiddenFunctionsSniff
- Errors no longer thrown on __autoload functions in PEAR ValidFunctionNameSniff
- Errors now thrown for __autoload methods in PEAR ValidFunctionNameSniff
- Errors now thrown if constructors or destructors have @return tags in Squiz FunctionCommentSniff
- Errors now thrown if @throws tags don't start with a capital and end with a full stop in Squiz FunctionCommentSniff
- Errors now thrown for invalid @var tag values in Squiz VariableCommentSniff
- Errors now thrown for missing doc comment in Squiz VariableCommentSniff
- Errors now thrown for unspaced operators in FOR loop declarations in Squiz OperatorSpacingSniff
- Errors now thrown for using ob_get_clean/flush functions to end buffers in Squiz OutputBufferingIndentSniff
- Errors now thrown for all missing member variable comments in Squiz VariableCommentSniff

## 0.4.0 - 2007-02-19

### Changed
- Standard name specified with --standard command line argument is no longer case sensitive
- Long error and warning messages are now wrapped to 80 characters in the full error report (thanks Endre Czirbesz)
- Shortened a lot of error and warning messages so they don't take up so much room
- Squiz FunctionCommentSniff now checks that param comments start with a capital letter and end with a full stop
- Squiz FunctionSpacingSniff now reports incorrect lines below function on closing brace, not function keyword
- Squiz FileCommentSniff now checks that there are no blank lines between the open PHP tag and the comment
- PHP_CodeSniffer_File::isReference() now returns correctly when checking refs on right side of =>

### Fixed
- Fixed incorrect error with switch closing brace in Squiz SwitchDeclarationSniff
- Fixed missing error when multiple statements are not aligned correctly with object operators
- Fixed incorrect errors for some PHP special variables in Squiz ValidVariableNameSniff
- Fixed incorrect errors for arrays that only contain other arrays in Squiz ArrayDeclarationSniff
- Fixed bug [#9844][pear-9844] : throw new Exception(\n accidentally reported as error but it ain't

[pear-9844]: https://pear.php.net/bugs/bug.php?id=9844

## 0.3.0 - 2007-01-11

### Changed
- Updated package.xml to version 2
- Specifying coding standard on command line is now optional, even if you have multiple standards installed
    - PHP_CodeSniffer uses the PEAR coding standard by default if no standard is specified
- New command line option, --extensions, to specify a comma separated list of file extensions to check
- Converted all unit tests to PHPUnit 3 format
- Added new coding standard, Squiz, that can be used as an alternative to PEAR
    - also contains more examples of sniffs
    - some may be moved into the Generic coding standard if required
- Added MultipleStatementAlignmentSniff to Generic standard
- Added ScopeIndentSniff to Generic standard
- Added ForbiddenFunctionsSniff to Generic standard
- Added FileCommentSniff to PEAR standard
- Added ClassCommentSniff to PEAR standard
- Added FunctionCommentSniff to PEAR standard
- Change MultipleStatementSniff to MultipleStatementAlignmentSniff in PEAR standard
- Replaced Methods directory with Functions directory in Generic and PEAR standards
    - also renamed some of the sniffs in those directories
- Updated file, class and method comments for all files

### Fixed
- Fixed bug [#9274][pear-9274] : nested_parenthesis element not set for open and close parenthesis tokens
- Fixed bug [#9411][pear-9411] : too few pattern characters cause incorrect error report

[pear-9411]: https://pear.php.net/bugs/bug.php?id=9411

## 0.2.1 - 2006-11-09

### Fixed
- Fixed bug [#9274][pear-9274] : nested_parenthesis element not set for open and close parenthesis tokens

[pear-9274]: https://pear.php.net/bugs/bug.php?id=9274

## 0.2.0 - 2006-10-13

### Changed
- Added a generic standards package that will contain generic sniffs to be used in specific coding standards
    - thanks to Frederic Poeydomenge for the idea
- Changed PEAR standard to use generic sniffs where available
- Added LowerCaseConstantSniff to Generic standard
- Added UpperCaseConstantSniff to Generic standard
- Added DisallowShortOpenTagSniff to Generic standard
- Added LineLengthSniff to Generic standard
- Added UpperCaseConstantNameSniff to Generic standard
- Added OpeningMethodBraceBsdAllmanSniff to Generic standard (contrib by Frederic Poeydomenge)
- Added OpeningMethodBraceKernighanRitchieSniff to Generic standard (contrib by Frederic Poeydomenge)
- Added framework for core PHP_CodeSniffer unit tests
- Added unit test for PHP_CodeSniffer:isCamelCaps method
- ScopeClosingBraceSniff now checks indentation of BREAK statements
- Added new command line arg (-vv) to show developer debug output

### Fixed
- Fixed some coding standard errors
- Fixed bug [#8834][pear-8834] : Massive memory consumption
- Fixed bug [#8836][pear-8836] : path case issues in package.xml
- Fixed bug [#8843][pear-8843] : confusion on nested switch()
- Fixed bug [#8841][pear-8841] : comments taken as whitespace
- Fixed bug [#8884][pear-8884] : another problem with nested switch() statements

[pear-8834]: https://pear.php.net/bugs/bug.php?id=8834
[pear-8836]: https://pear.php.net/bugs/bug.php?id=8836
[pear-8841]: https://pear.php.net/bugs/bug.php?id=8841
[pear-8843]: https://pear.php.net/bugs/bug.php?id=8843
[pear-8884]: https://pear.php.net/bugs/bug.php?id=8884

## 0.1.1 - 2006-09-25

### Changed
- Added unit tests for all PEAR sniffs
- Exception class now extends from PEAR_Exception

### Fixed
- Fixed summary report so files without errors but with warnings are not shown when warnings are hidden

## 0.1.0 - 2006-09-19

### Changed
- Reorganised package contents to conform to PEAR standards
- Changed version numbering to conform to PEAR standards
- Removed duplicate `require_once()` of `Exception.php` from `CodeSniffer.php`

## 0.0.5 - 2006-09-18

### Fixed
- Fixed `.bat` file for situation where `php.ini` cannot be found so `include_path` is not set

## 0.0.4 - 2006-08-28

### Changed
- Added .bat file for easier running of PHP_CodeSniffer on Windows
- Sniff that checks method names now works for PHP4 style code where there is no scope keyword
- Sniff that checks method names now works for PHP4 style constructors
- Sniff that checks method names no longer incorrectly reports error with magic methods
- Sniff that checks method names now reports errors with non-magic methods prefixed with __
- Sniff that checks for constant names no longer incorrectly reports errors with heredoc strings
- Sniff that checks for constant names no longer incorrectly reports errors with created objects
- Sniff that checks indentation no longer incorrectly reports errors with heredoc strings
- Sniff that checks indentation now correctly reports errors with improperly indented multi-line strings
- Sniff that checks function declarations now checks for spaces before and after an equals sign for default values
- Sniff that checks function declarations no longer incorrectly reports errors with multi-line declarations
- Sniff that checks included code no longer incorrectly reports errors when return value is used conditionally
- Sniff that checks opening brace of function no longer incorrectly reports errors with multi-line declarations
- Sniff that checks spacing after commas in function calls no longer reports too many errors for some code
- Sniff that checks control structure declarations now gives more descriptive error message

## 0.0.3 - 2006-08-22

### Changed
- Added sniff to check for invalid class and interface names
- Added sniff to check for invalid function and method names
- Added sniff to warn if line is greater than 85 characters
- Added sniff to check that function calls are in the correct format
- Added command line arg to print current version (--version)

### Fixed
- Fixed error where comments were not allowed on the same line as a control structure declaration

## 0.0.2 - 2006-07-25

### Changed
- Removed the including of checked files to stop errors caused by parsing them
- Removed the use of reflection so checked files do not have to be included
- Memory usage has been greatly reduced
- Much faster tokenizing and checking times
- Reworked the PEAR coding standard sniffs (much faster now)
- Fix some bugs with the PEAR scope indentation standard
- Better checking for installed coding standards
- Can now accept multiple files and dirs on the command line
- Added an option to list installed coding standards
- Added an option to print a summary report (number of errors and warnings shown for each file)
- Added an option to hide warnings from reports
- Added an option to print verbose output (so you know what is going on)
- Reordered command line args to put switches first (although order is not enforced)
- Switches can now be specified together (e.g. `phpcs -nv`) as well as separately (`phpcs -n -v`)

## 0.0.1 - 2006-07-19

### Added
- Initial preview release

<!--
=== Link list for release links ====
-->

[Unreleased]: https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/master...HEAD
[3.13.2]:     https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.13.1...3.13.2
[3.13.1]:     https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.13.0...3.13.1
[3.13.0]:     https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.12.2...3.13.0
[3.12.2]:     https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.12.1...3.12.2
[3.12.1]:     https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.12.0...3.12.1
[3.12.0]:     https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.11.3...3.12.0
[3.11.3]:     https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.11.2...3.11.3
[3.11.2]:     https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.11.1...3.11.2
[3.11.1]:     https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.11.0...3.11.1
[3.11.0]:     https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.10.3...3.11.0
[3.10.3]:     https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.10.2...3.10.3
[3.10.2]:     https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.10.1...3.10.2
[3.10.1]:     https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.10.0...3.10.1
[3.10.0]:     https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.9.2...3.10.0
[3.9.2]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.9.1...3.9.2
[3.9.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.9.0...3.9.1
[3.9.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.8.1...3.9.0
[3.8.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.8.0...3.8.1
[3.8.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.7.2...3.8.0
[3.7.2]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.7.1...3.7.2
[3.7.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.7.0...3.7.1
[3.7.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.6.2...3.7.0
[3.6.2]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.6.1...3.6.2
[3.6.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.6.0...3.6.1
[3.6.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.5.8...3.6.0
[3.5.8]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.5.7...3.5.8
[3.5.7]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.5.6...3.5.7
[3.5.6]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.5.5...3.5.6
[3.5.5]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.5.4...3.5.5
[3.5.4]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.5.3...3.5.4
[3.5.3]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.5.2...3.5.3
[3.5.2]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.5.1...3.5.2
[3.5.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.5.0...3.5.1
[3.5.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.4.2...3.5.0
[3.4.2]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.4.1...3.4.2
[3.4.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.4.0...3.4.1
[3.4.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.3.2...3.4.0
[3.3.2]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.3.1...3.3.2
[3.3.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.3.0...3.3.1
[3.3.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.2.3...3.3.0
[3.2.3]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.2.2...3.2.3
[3.2.2]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.2.1...3.2.2
[3.2.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.2.0...3.2.1
[3.2.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.1.1...3.2.0
[3.1.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.1.0...3.1.1
[3.1.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.0.2...3.1.0
[3.0.2]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.0.1...3.0.2
[3.0.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.0.0...3.0.1
[3.0.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.0.0RC4...3.0.0
[3.0.0RC4]:   https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.0.0RC3...3.0.0RC4
[3.0.0RC3]:   https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.0.0RC2...3.0.0RC3
[3.0.0RC2]:   https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.0.0RC1...3.0.0RC2
[3.0.0RC1]:   https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/3.0.0a1...3.0.0RC1
[3.0.0a1]:    https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.9.2...3.0.0a1
[2.9.2]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.9.1...2.9.2
[2.9.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.9.0...2.9.1
[2.9.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.8.1...2.9.0
[2.8.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.8.0...2.8.1
[2.8.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.7.1...2.8.0
[2.7.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.7.0...2.7.1
[2.7.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.6.2...2.7.0
[2.6.2]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.6.1...2.6.2
[2.6.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.6.0...2.6.1
[2.6.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.5.1...2.6.0
[2.5.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.5.0...2.5.1
[2.5.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.4.0...2.5.0
[2.4.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.3.4...2.4.0
[2.3.4]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.3.3...2.3.4
[2.3.3]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.3.2...2.3.3
[2.3.2]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.3.1...2.3.2
[2.3.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.3.0...2.3.1
[2.3.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.2.0...2.3.0
[2.2.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.1.0...2.2.0
[2.1.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.0.0...2.1.0
[2.0.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.0.0RC4...2.0.0
[2.0.0RC4]:   https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.0.0RC3...2.0.0RC4
[2.0.0RC3]:   https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.0.0RC2...2.0.0RC3
[2.0.0RC2]:   https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.0.0RC1...2.0.0RC2
[2.0.0RC1]:   https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.0.0a2...2.0.0RC1
[2.0.0a2]:    https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/2.0.0a1...2.0.0a2
[2.0.0a1]:    https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.5.6...2.0.0a1
[1.5.6]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.5.5...1.5.6
[1.5.5]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.5.4...1.5.5
[1.5.4]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.5.3...1.5.4
[1.5.3]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.5.2...1.5.3
[1.5.2]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.5.1...1.5.2
[1.5.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.5.0...1.5.1
[1.5.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.5.0RC4...1.5.0
[1.5.0RC4]:   https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.5.0RC3...1.5.0RC4
[1.5.0RC3]:   https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.5.0RC2...1.5.0RC3
[1.5.0RC2]:   https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.5.0RC1...1.5.0RC2
[1.5.0RC1]:   https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.4.8...1.5.0RC1
[1.4.8]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.4.7...1.4.8
[1.4.7]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.4.6...1.4.7
[1.4.6]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.4.5...1.4.6
[1.4.5]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.4.4...1.4.5
[1.4.4]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.4.3...1.4.4
[1.4.3]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.4.2...1.4.3
[1.4.2]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.4.1...1.4.2
[1.4.1]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.4.0...1.4.1
[1.4.0]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.3.6...1.4.0
[1.3.6]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.3.5...1.3.6
[1.3.5]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.3.4...1.3.5
[1.3.4]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.3.3...1.3.4
[1.3.3]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.3.2...1.3.3
[1.3.2]:      https://github.com/PHPCSStandards/PHP_CodeSniffer/compare/1.3.1...1.3.2

<!--
=== Link list for contributor profiles ====
-->

[@2shediac]:            https://github.com/2shediac
[@ablyler]:             https://github.com/ablyler
[@aboks]:               https://github.com/aboks
[@abulford]:            https://github.com/abulford
[@afilina]:             https://github.com/afilina
[@aik099]:              https://github.com/aik099
[@akarmazyn]:           https://github.com/akarmazyn
[@akkie]:               https://github.com/akkie
[@alcohol]:             https://github.com/alcohol
[@alekitto]:            https://github.com/alekitto
[@AlexHowansky]:        https://github.com/AlexHowansky
[@anbuc]:               https://github.com/anbuc
[@andrei-propertyguru]: https://github.com/andrei-propertyguru
[@AndrewDawes]:         https://github.com/AndrewDawes
[@andygrunwald]:        https://github.com/andygrunwald
[@andypost]:            https://github.com/andypost
[@annechko]:            https://github.com/annechko
[@anomiex]:             https://github.com/anomiex
[@arnested]:            https://github.com/arnested
[@asispts]:             https://github.com/asispts
[@asnyder]:             https://github.com/asnyder
[@Astinus-Eberhard]:    https://github.com/Astinus-Eberhard
[@axlon]:               https://github.com/axlon
[@bayleedev]:           https://github.com/bayleedev
[@becoded]:             https://github.com/becoded
[@Benjamin-Loison]:     https://github.com/Benjamin-Loison
[@benmatselby]:         https://github.com/benmatselby
[@biinari]:             https://github.com/biinari
[@Billz95]:             https://github.com/Billz95
[@biozshock]:           https://github.com/biozshock
[@bkdotcom]:            https://github.com/bkdotcom
[@bladeofsteel]:        https://github.com/bladeofsteel
[@blerou]:              https://github.com/blerou
[@blue32a]:             https://github.com/blue32a
[@bondas83]:            https://github.com/bondas83
[@boonkerz]:            https://github.com/boonkerz
[@braindawg]:           https://github.com/braindawg
[@BRMatt]:              https://github.com/BRMatt
[@CandySunPlus]:        https://github.com/CandySunPlus
[@ceeram]:              https://github.com/ceeram
[@cixtor]:              https://github.com/cixtor
[@claylo]:              https://github.com/claylo
[@codebymikey]:         https://github.com/codebymikey
[@costdev]:             https://github.com/costdev
[@covex-nn]:            https://github.com/covex-nn
[@cweiske]:             https://github.com/cweiske
[@Daimona]:             https://github.com/Daimona
[@danez]:               https://github.com/danez
[@DanielEScherzer]:     https://github.com/DanielEScherzer
[@DannyvdSluijs]:       https://github.com/DannyvdSluijs
[@das-peter]:           https://github.com/das-peter
[@datengraben]:         https://github.com/datengraben
[@david-binda]:         https://github.com/david-binda
[@Decave]:              https://github.com/Decave
[@dereuromark]:         https://github.com/dereuromark
[@derrabus]:            https://github.com/derrabus
[@devfrey]:             https://github.com/devfrey
[@deviantintegral]:     https://github.com/deviantintegral
[@dhensby]:             https://github.com/dhensby
[@dingo-d]:             https://github.com/dingo-d
[@dominics]:            https://github.com/dominics
[@donatj]:              https://github.com/donatj
[@dryabkov]:            https://github.com/dryabkov
[@dschniepp]:           https://github.com/dschniepp
[@duncan3dc]:           https://github.com/duncan3dc
[@edorian]:             https://github.com/edorian
[@elazar]:              https://github.com/elazar
[@ElvenSpellmaker]:     https://github.com/ElvenSpellmaker
[@emil-nasso]:          https://github.com/emil-nasso
[@enl]:                 https://github.com/enl
[@erikwiffin]:          https://github.com/erikwiffin
[@eser]:                https://github.com/eser
[@exussum12]:           https://github.com/exussum12
[@fabacino]:            https://github.com/fabacino
[@fabre-thibaud]:       https://github.com/fabre-thibaud
[@fcool]:               https://github.com/fcool
[@filips123]:           https://github.com/filips123
[@Fischer-Bjoern]:      https://github.com/Fischer-Bjoern
[@fonsecas72]:          https://github.com/fonsecas72
[@fredden]:             https://github.com/fredden
[@GaryJones]:           https://github.com/GaryJones
[@ghostal]:             https://github.com/ghostal
[@ghunti]:              https://github.com/ghunti
[@gmponos]:             https://github.com/gmponos
[@gnutix]:              https://github.com/gnutix
[@goatherd]:            https://github.com/goatherd
[@grongor]:             https://github.com/grongor
[@grzr]:                https://github.com/grzr
[@gwharton]:            https://github.com/gwharton
[@hashar]:              https://github.com/hashar
[@helgi]:               https://github.com/helgi
[@hernst42]:            https://github.com/hernst42
[@iammattcoleman]:      https://github.com/iammattcoleman
[@ihabunek]:            https://github.com/ihabunek
[@illusori]:            https://github.com/illusori
[@index0h]:             https://github.com/index0h
[@ivuorinen]:           https://github.com/ivuorinen
[@jasonmccreary]:       https://github.com/jasonmccreary
[@javer]:               https://github.com/javer
[@jaymcp]:              https://github.com/jaymcp
[@JDGrimes]:            https://github.com/JDGrimes
[@jedgell]:             https://github.com/jedgell
[@jeffslofish]:         https://github.com/jeffslofish
[@jmarcil]:             https://github.com/jmarcil
[@jnrbsn]:              https://github.com/jnrbsn
[@joachim-n]:           https://github.com/joachim-n
[@joelposti]:           https://github.com/joelposti
[@johanderuijter]:      https://github.com/johanderuijter
[@johnmaguire]:         https://github.com/johnmaguire
[@johnpbloch]:          https://github.com/johnpbloch
[@JorisDebonnet]:       https://github.com/JorisDebonnet
[@josephzidell]:        https://github.com/josephzidell
[@joshdavis11]:         https://github.com/joshdavis11
[@jpoliveira08]:        https://github.com/jpoliveira08
[@jpuck]:               https://github.com/jpuck
[@jrfnl]:               https://github.com/jrfnl
[@kdebisschop]:         https://github.com/kdebisschop
[@kenguest]:            https://github.com/kenguest
[@klausi]:              https://github.com/klausi
[@Konafets]:            https://github.com/Konafets
[@kristofser]:          https://github.com/kristofser
[@ksimka]:              https://github.com/ksimka
[@ktomk]:               https://github.com/ktomk
[@kukulich]:            https://github.com/kukulich
[@legoktm]:             https://github.com/legoktm
[@lmanzke]:             https://github.com/lmanzke
[@localheinz]:          https://github.com/localheinz
[@lucc]:                https://github.com/lucc
[@MacDada]:             https://github.com/MacDada
[@Majkl578]:            https://github.com/Majkl578
[@manuelpichler]:       https://github.com/manuelpichler
[@marcospassos]:        https://github.com/marcospassos
[@MarkBaker]:           https://github.com/MarkBaker
[@MarkMaldaba]:         https://github.com/MarkMaldaba
[@martinssipenko]:      https://github.com/martinssipenko
[@marvasDE]:            https://github.com/marvasDE
[@maryo]:               https://github.com/maryo
[@MasterOdin]:          https://github.com/MasterOdin
[@mathroc]:             https://github.com/mathroc
[@MatmaRex]:            https://github.com/MatmaRex
[@maxgalbu]:            https://github.com/maxgalbu
[@mcuelenaere]:         https://github.com/mcuelenaere
[@mhujer]:              https://github.com/mhujer
[@michaelbutler]:       https://github.com/michaelbutler
[@michalbundyra]:       https://github.com/michalbundyra
[@Morerice]:            https://github.com/Morerice
[@mbomb007]:            https://github.com/mbomb007
[@morozov]:             https://github.com/morozov
[@mrkrstphr]:           https://github.com/mrkrstphr
[@mythril]:             https://github.com/mythril
[@Naelyth]:             https://github.com/Naelyth
[@ndm2]:                https://github.com/ndm2
[@nicholascus]:         https://github.com/nicholascus
[@NickDickinsonWilde]:  https://github.com/NickDickinsonWilde
[@nkovacs]:             https://github.com/nkovacs
[@nubs]:                https://github.com/nubs
[@o5]:                  https://github.com/o5
[@ofbeaton]:            https://github.com/ofbeaton
[@olemartinorg]:        https://github.com/olemartinorg
[@ondrejmirtes]:        https://github.com/ondrejmirtes
[@orx0r]:               https://github.com/orx0r
[@ostrolucky]:          https://github.com/ostrolucky
[@peterwilsoncc]:       https://github.com/peterwilsoncc
[@pfrenssen]:           https://github.com/pfrenssen
[@phil-davis]:          https://github.com/phil-davis
[@photodude]:           https://github.com/photodude
[@przemekhernik]:       https://github.com/przemekhernik
[@r3nat]:               https://github.com/r3nat
[@raul338]:             https://github.com/raul338
[@realmfoo]:            https://github.com/realmfoo
[@remicollet]:          https://github.com/remicollet
[@renaatdemuynck]:      https://github.com/renaatdemuynck
[@renan]:               https://github.com/renan
[@rhorber]:             https://github.com/rhorber
[@rmccue]:              https://github.com/rmccue
[@robocoder]:           https://github.com/robocoder
[@rodrigoprimo]:        https://github.com/rodrigoprimo
[@rogeriopradoj]:       https://github.com/rogeriopradoj
[@rovangju]:            https://github.com/rovangju
[@rvanvelzen]:          https://github.com/rvanvelzen
[@saltybeagle]:         https://github.com/saltybeagle
[@samlev]:              https://github.com/samlev
[@scato]:               https://github.com/scato
[@schlessera]:          https://github.com/schlessera
[@schnittstabil]:       https://github.com/schnittstabil
[@sebastianbergmann]:   https://github.com/sebastianbergmann
[@sertand]:             https://github.com/sertand
[@shanethehat]:         https://github.com/shanethehat
[@shivammathur]:        https://github.com/shivammathur
[@simonsan]:            https://github.com/simonsan
[@sjlangley]:           https://github.com/sjlangley
[@sserbin]:             https://github.com/sserbin
[@stefanlenselink]:     https://github.com/stefanlenselink
[@SteveTalbot]:         https://github.com/SteveTalbot
[@storeman]:            https://github.com/storeman
[@stronk7]:             https://github.com/stronk7
[@svycka]:              https://github.com/svycka
[@syranez]:             https://github.com/syranez
[@tasuki]:              https://github.com/tasuki
[@tim-bezhashvyly]:     https://github.com/tim-bezhashvyly
[@TomHAnderson]:        https://github.com/TomHAnderson
[@thewilkybarkid]:      https://github.com/thewilkybarkid
[@thiemowmde]:          https://github.com/thiemowmde
[@thomasjfox]:          https://github.com/thomasjfox
[@till]:                https://github.com/till
[@timoschinkel]:        https://github.com/timoschinkel
[@TimWolla]:            https://github.com/TimWolla
[@uniquexor]:           https://github.com/uniquexor
[@valorin]:             https://github.com/valorin
[@VasekPurchart]:       https://github.com/VasekPurchart
[@VincentLanglet]:      https://github.com/VincentLanglet
[@waltertamboer]:       https://github.com/waltertamboer
[@westonruter]:         https://github.com/westonruter
[@willemstuursma]:      https://github.com/willemstuursma
[@wimg]:                https://github.com/wimg
[@wvega]:               https://github.com/wvega
[@xalopp]:              https://github.com/xalopp
[@xjm]:                 https://github.com/xjm
[@xt99]:                https://github.com/xt99
[@yesmeck]:             https://github.com/yesmeck
[@zBart]:               https://github.com/zBart
[pear-adviva]:          https://pear.php.net/user/adviva
[pear-bakert]:          https://pear.php.net/user/bakert
[pear-bjorn]:           https://pear.php.net/user/bjorn
[pear-boxgav]:          https://pear.php.net/user/boxgav
[pear-burci]:           https://pear.php.net/user/burci
[pear-conf]:            https://pear.php.net/user/conf
[pear-cwiedmann]:       https://pear.php.net/user/cwiedmann
[pear-dollyaswin]:      https://pear.php.net/user/dollyaswin
[pear-dvino]:           https://pear.php.net/user/dvino
[pear-et3w503]:         https://pear.php.net/user/et3w503
[pear-gemineye]:        https://pear.php.net/user/gemineye
[pear-kwinahradsky]:    https://pear.php.net/user/kwinahradsky
[pear-ljmaskey]:        https://pear.php.net/user/ljmaskey
[pear-mccammos]:        https://pear.php.net/user/mccammos
[pear-pete]:            https://pear.php.net/user/pete
[pear-recurser]:        https://pear.php.net/user/recurser
[pear-renoiv]:          https://pear.php.net/user/renoiv
[pear-rquadling]:       https://pear.php.net/user/rquadling
[pear-ryba]:            https://pear.php.net/user/ryba
[pear-thezero]:         https://pear.php.net/user/thezero
[pear-thing2b]:         https://pear.php.net/user/thing2b
[pear-tomdesp]:         https://pear.php.net/user/tomdesp
[pear-troehr]:          https://pear.php.net/user/troehr
[pear-weirdan]:         https://pear.php.net/user/weirdan
[pear-wloche]:          https://pear.php.net/user/wloche
[pear-woellchen]:       https://pear.php.net/user/woellchen
[pear-youngian]:        https://pear.php.net/user/youngian
