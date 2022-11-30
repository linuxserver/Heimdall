# Changelog

All notable changes to this project will be documented in this file.

[Next release](https://github.com/barryvdh/laravel-ide-helper/compare/v2.12.3...master)
--------------

2022-03-06, 2.12.3
------------------

### Fixed
- Fix date and datetime handling for attributes that set a serialization format option for the Carbon instance [#1324 / FLeudts](https://github.com/barryvdh/laravel-ide-helper/pull/1324)
- Fix composer conflict with composer/pcre version 2/3. [#1327 / barryvdh](https://github.com/barryvdh/laravel-ide-helper/pull/1327)

2022-02-08, 2.12.2
------------------
### Fixed
- Remove composer dependecy, use copy of ClassMapGenerator [#1313 / barryvdh](https://github.com/barryvdh/laravel-ide-helper/pull/1313)

2022-01-24, 2.12.1
------------------
### Fixed
- Properly handle `Castable`s without return type. [#1306 / binotaliu](https://github.com/barryvdh/laravel-ide-helper/pull/1306)

2022-01-23, 2.12.0
------------------
### Added
- Add support for custom casts that using `Castable` [#1287 / binotaliu](https://github.com/barryvdh/laravel-ide-helper/pull/1287)
- Added Laravel 9 support [#1297 / rcerljenko](https://github.com/barryvdh/laravel-ide-helper/pull/1297)

2022-01-03, 2.11.0
------------------
### Added
- Add support for Laravel 8.77 Attributes [\#1289 / SimonJnsson](https://github.com/barryvdh/laravel-ide-helper/pull/1289)
- Add support for cast types `decimal:*`, `encrypted:*`, `immutable_date`, `immutable_datetime`, `custom_datetime`, and `immutable_custom_datetime` [#1262 / miken32](https://github.com/barryvdh/laravel-ide-helper/pull/1262)
- Add support of variadic parameters in `ide-helper:models` [\#1234 / shaffe-fr](https://github.com/barryvdh/laravel-ide-helper/pull/1234)
- Add support of custom casts without properties [\#1267 / sparclex](https://github.com/barryvdh/laravel-ide-helper/pull/1267)

### Fixed
- Fix recursively searching for `HasFactory` and `Macroable` traits [\#1216 / daniel-de-wit](https://github.com/barryvdh/laravel-ide-helper/pull/1216)
- Use platformName to determine db type when casting boolean types [\#1212 / stockalexander](https://github.com/barryvdh/laravel-ide-helper/pull/1212)

### Changed
- Move default models helper filename to config [\#1241 / wimski](https://github.com/barryvdh/laravel-ide-helper/pull/1241)

2021-06-18, 2.10.1
------------------
### Added
- Added Type registration according to [Custom Mapping Types documentation](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#custom-mapping-types) [\#1228 / wimski](https://github.com/barryvdh/laravel-ide-helper/pull/1241)

### Fixed
- Fixing issue where configured custom_db_types could cause a DBAL exception to be thrown while running `ide-helper:models` [\#1228 / wimski](https://github.com/barryvdh/laravel-ide-helper/pull/1241)

2021-04-09, 2.10.0
------------------
### Added
- Allowing Methods to be set or unset in ModelHooks [\#1198 / jenga201](https://github.com/barryvdh/laravel-ide-helper/pull/1198)\
  Note: the visibility of `\Barryvdh\LaravelIdeHelper\Console\ModelsCommand::setMethod` has been changed to **public**!

### Fixed
- Fixing issue where incorrect autoloader unregistered [\#1210 / tezhm](https://github.com/barryvdh/laravel-ide-helper/pull/1210)

2021-04-02, 2.9.3
-----------------
### Fixed
- Support both customized namespace factories as well as default resolvable ones [\#1201 / wimski](https://github.com/barryvdh/laravel-ide-helper/pull/1201)

2021-04-01, 2.9.2
-----------------
### Added
- Model hooks for adding custom information from external sources to model classes through the ModelsCommand [\#945 / wimski](https://github.com/barryvdh/laravel-ide-helper/pull/945)

### Fixed
- Fix ide-helper:models exception if model doesn't have factory [\#1196 / ahmed-aliraqi](https://github.com/barryvdh/laravel-ide-helper/pull/1196)
- Running tests triggering post_migrate hooks [\#1193 / netpok](https://github.com/barryvdh/laravel-ide-helper/pull/1193)
- Array_merge error when config is cached prior to package install [\#1184 / netpok](https://github.com/barryvdh/laravel-ide-helper/pull/1184)

2021-03-15, 2.9.1
-----------------
### Added
- Generate PHPDoc for Laravel 8.x factories [\#1074 / ahmed-aliraqi](https://github.com/barryvdh/laravel-ide-helper/pull/1074)
- Add a comment to a property like table columns [\#1168 / biiiiiigmonster](https://github.com/barryvdh/laravel-ide-helper/pull/1168)
- Added `post_migrate` hook to run commands after a migration [\#1163 / netpok](https://github.com/barryvdh/laravel-ide-helper/pull/1163)
- Allow for PhpDoc for macros with union types [\#1148 / riesjart](https://github.com/barryvdh/laravel-ide-helper/pull/1148)

### Fixed
- Error when generating helper for invokable classes [\#1124 / standaniels](https://github.com/barryvdh/laravel-ide-helper/pull/1124)
- Fix broken ReflectionUnionTypes [\#1132 / def-studio](https://github.com/barryvdh/laravel-ide-helper/pull/1132)
- Relative class names are not converted to fully-qualified class names [\#1005 / SavKS](https://github.com/barryvdh/laravel-ide-helper/pull/1005)

2020-12-30, 2.9.0
-----------------
### Changed
- Dropped support for Laravel 6 and Laravel 7, as well as support for PHP 7.2 and added support for doctrine/dbal:^3 [\#1114 / mfn](https://github.com/barryvdh/laravel-ide-helper/pull/1114)

### Fixed
- `Macro::initPhpDoc()` will save original docblock if present [\#1116 / LastDragon-ru](https://github.com/barryvdh/laravel-ide-helper/pull/1116)
- `Alias` will grab macros from `\Illuminate\Database\Eloquent\Builder` too [\#1118 / LastDragon-ru](https://github.com/barryvdh/laravel-ide-helper/pull/1118)

2020-12-08, 2.8.2
-----------------
### Added
- Fix phpdoc generate for custom cast with parameter [\#986 / artelkr](https://github.com/barryvdh/laravel-ide-helper/pull/986)
- Created a possibility to add custom relation type [\#987 / efinder2](https://github.com/barryvdh/laravel-ide-helper/pull/987)
- Added `@see` with macro/mixin definition location to PhpDoc [\#1054 / riesjart](https://github.com/barryvdh/laravel-ide-helper/pull/1054)
- Initial compatibility for PHP8 [\#1106 / mfn](https://github.com/barryvdh/laravel-ide-helper/pull/1106)

### Changed
- Implement DeferrableProvider [\#914 / kon-shou](https://github.com/barryvdh/laravel-ide-helper/pull/914)

### Fixed
- Compatibility with Lumen [\#1043 / mfn](https://github.com/barryvdh/laravel-ide-helper/pull/1043)
- Allow model_locations to have glob patterns [\#1059 / saackearl](https://github.com/barryvdh/laravel-ide-helper/pull/1059)
- Error when generating helper for macroable classes which are not facades and contain a "fake" method [\#1066 / domkrm] (https://github.com/barryvdh/laravel-ide-helper/pull/1066)
- Casts with a return type of `static` or `$this` now resolve to an instance of the cast [\#1103 / riesjart](https://github.com/barryvdh/laravel-ide-helper/pull/1103)

### Removed
- Removed format and broken generateJsonHelper [\#1053 / mfn](https://github.com/barryvdh/laravel-ide-helper/pull/1053)

2020-09-07, 2.8.1
-----------------
### Added
- Support Laravel 8 [\#1022 / barryvdh](https://github.com/barryvdh/laravel-ide-helper/pull/1022)
- Add option to force usage of FQN [\#1031 / edvordo](https://github.com/barryvdh/laravel-ide-helper/pull/1031)
- Add support for macros of all macroable classes [\#1006 / domkrm](https://github.com/barryvdh/laravel-ide-helper/pull/1006)

2020-08-11, 2.8.0
-----------------
### Added
- Add static return type to builder methods [\#924 / dmason30](https://github.com/barryvdh/laravel-ide-helper/pull/924)
- Add `optonal` to meta generator for PhpStorm [\#932 / halaei](https://github.com/barryvdh/laravel-ide-helper/pull/932)
- Decimal columns as string in Models [\#948 / fgibaux](https://github.com/barryvdh/laravel-ide-helper/pull/948)
- Simplify full namespaces for already included resources [\#954 / LANGERGabriel](https://github.com/barryvdh/laravel-ide-helper/pull/954)
- Make writing relation count properties optional [\#969 / AegirLeet](https://github.com/barryvdh/laravel-ide-helper/pull/969)
- Add more methods able to resolve container instances [\#996 / mfn](https://github.com/barryvdh/laravel-ide-helper/pull/996)

### Fixed
- Test `auth` is bound before detect Auth driver [\#946 / zhwei](https://github.com/barryvdh/laravel-ide-helper/pull/946)
- Fix inline doc-block for final models [\#944 / Gummibeer](https://github.com/barryvdh/laravel-ide-helper/pull/955)

2020-04-22, 2.7.0
-----------------
### Added
- Add `ignored_models` as config option [\#890 / pataar](https://github.com/barryvdh/laravel-ide-helper/pull/890)
- Infer return type from reflection if no phpdoc given [\#906 / mfn](https://github.com/barryvdh/laravel-ide-helper/pull/906)
- Add custom collection support for get and all methods [\#903 / dmason30](https://github.com/barryvdh/laravel-ide-helper/pull/903)
- if a model implements interfaces, include them in the stub [\#920 / mr-feek](https://github.com/barryvdh/laravel-ide-helper/pull/920)
- Generate noinspections PHPStorm tags [\#905 / mzglinski](https://github.com/barryvdh/laravel-ide-helper/pull/905)
- Added support for Laravel 7 custom casts [\#913 / belamov](https://github.com/barryvdh/laravel-ide-helper/pull/913)
- Ability to use patterns for model_locations [\#921 / 4n70w4](https://github.com/barryvdh/laravel-ide-helper/pull/921)

### Fixed
- MorphToMany relations with query not working [\#894 / UksusoFF](https://github.com/barryvdh/laravel-ide-helper/pull/894)
- Fix camelCase duplicated properties generator [\#881 / bop10](https://github.com/barryvdh/laravel-ide-helper/pull/881)
- Prevent generation of invalid code for certain parameter default values [\#901 / loilo](https://github.com/barryvdh/laravel-ide-helper/pull/901)
- Make hasOne and morphOne nullable [\#864 / leo108](https://github.com/barryvdh/laravel-ide-helper/pull/864)
- Remove unnecessary and wrong definition of SoftDelete methods [\#918 / mfn](https://github.com/barryvdh/laravel-ide-helper/pull/918)
- Unregister meta command custom autoloader when it is no longer needed [\#919 / mr-feek](https://github.com/barryvdh/laravel-ide-helper/pull/919)

2020-02-25, 2.6.7
-----------------
### Added
- Support for Laravel 7 [commit by barryvdh](https://github.com/barryvdh/laravel-ide-helper/commit/edd69c5e0508972c81f1f7173236de2459c45814)

2019-12-02, 2.6.6
-----------------
### Added
- Add splat operator (...) support [\#860 / ngmy](https://github.com/barryvdh/laravel-ide-helper/pull/860)
- Add support for custom date class via Date::use() [\#859 / mfn](https://github.com/barryvdh/laravel-ide-helper/pull/859)

### Fixed
- Prevent undefined property errors [\#877 / matt-allan](https://github.com/barryvdh/laravel-ide-helper/pull/877)

----
Missing an older changelog? Feel free to submit a PR!
