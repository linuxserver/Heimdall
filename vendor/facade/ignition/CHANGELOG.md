# Changelog

All notable changes to `ignition` will be documented in this file

## 2.17.4 - 2021-12-27

- fix bug where uninitialized property within a job could break Ignition

## 2.17.3 - 2021-12-23

- allow filtering route parameters using a `toFlare` method

## 2.17.2 - 2021-11-29

## What's Changed

- Allow overflow-x on solutions with unbreakable words by @willemvb in https://github.com/facade/ignition/pull/431

**Full Changelog**: https://github.com/facade/ignition/compare/2.17.1...2.17.2

## 2.17.2 - 2021-11-29

- scroll overflow on solutions

## 2.17.1 - 2021-11-25

- streamline Livewire solutions

## 2.17.0 - 2021-11-24

- improve recording of Livewire data

## 2.16.1 - 2021-11-16

- allow sending of unbinded sql queries to Flare

## 2.16.0 - 2021-10-28

- improve recording data from jobs (#416)

## 2.15.0 - 2021-10-11

- improve output of flare:test

## 2.14.1 - 2021-10-08

- update base URL for Flare

## 2.14.0 - 2021-10-01

- add support for VScode WSL + SSH remote (#420)

## 2.13.1 - 2021-09-13

- fix namespace of `SentReports` in facade

## 2.13.0 - 2021-09-13

- add tracking uuid (#418)

## 2.12.1 - 2021-09-08

- add support for VS Codium editor (#417)

## 2.12.0 - 2021-08-24

- add support for collecting information about jobs (#412)

## 2.11.4 - 2021-08-16

- use npm ci instead of install (#411)

## 2.11.3 - 2021-08-16

- fix issues with circular dependencies in model route parameters (#408)
- remove notice about dirty git state in context
- wrap `AddGitInformation` middleware in try-catch

## 2.11.2 - 2021-07-20

- fix issues introduced in 2.11.1 (#403)

## 2.11.1 - 2021-07-20

- fix sending queued reports on Laravel Vapor queues (#398)

## 2.11.0 - 2021-07-12

- prepare Laravel 9 support
- remove filp/whoops dependency
- update front-end dependencies

## 2.10.2 - 2021-06-11

- fix typo in config/flare.php (#395)

## 2.10.1 - 2021-06-03

- fix memory leaks in Octane (#393)

## 2.10.0 - 2021-06-03

- add a solution for lazy loading violations (#392)

## 2.9.0 - 2021-05-05

- add Xdebug format links for editor (#383)

## 2.8.4 - 2021-04-29

- avoid making call to Flare when no API key is specified

## 2.8.3 - 2021-04-09

- support Octane (#379)

## 2.8.2 - 2021-04-08

- censor passwords by default (#377)

## 2.8.1 - 2021-04-08

- add `censor_request_body_fields` default config option

## 2.8.0 - 2021-04-08

- add `censor_request_body_fields` config option

## 2.7.0 - 2021-03-30

- adds a debug warning when having debug enabled on a non-local environment (#366)

## 2.6.1 - 2021-03-30

- Disable executing solutions on non-local environments or from non-local IP addresses (#364)

## 2.6.0 - 2021-03-24

- add extra output to test command when executing verbosely

## 2.5.14 - 2021-03-03

- fix ignition not working when there is no argv

## 2.5.13 - 2021-02-16

- remove custom grouping

## 2.5.12 - 2021-02-15

- fix wrong config usage (#354)

## 2.5.11 - 2021-02-05

- fix memory leaks caused by log and query recorder (#344)

## 2.5.10 - 2021-02-02

- fix tinker logs not being sent to Flare

## 2.5.9 - 2021-01-26

- fix logged context not being sent to Flare

## 2.5.8 - 2020-12-29

- fix double `$` on PHP 8 (#338)

## 2.5.7 - 2020-12-29

- fix for breaking change in highlight.js (fixes 2.5.5)

## 2.5.6 - 2020-12-29

- revert to compiled js of 2.5.3

## 2.5.5 - 2020-12-29

- added compiled js of previous release

## 2.5.4 - 2020-12-29

- added support for Nova text editor (#343)

## 2.5.3 - 2020-12-08

- Use Livewire compatible compiler engine when using Livewire (#340)

## 2.5.2 - 2020-11-14

- fix `MakeViewVariableOptionalSolution` to disallow stream wrappers and files that do not end in ".blade.php" (#334)

## 2.5.1 - 2020-11-13

- add support for LiveWire component urls

## 2.5.0 - 2020-10-27

- add PHP 8.0-dev support
- remove unnecessary `scrivo/highlight.php` dependency

## 2.4.2 - 2021-03-08

- fix `MakeViewVariableOptionalSolution` to disallow stream wrappers and files that do not end in .blade.php (#356)

## 2.4.1 - 2020-10-14

- fix copy casing

## 2.4.0 - 2020-10-14

- add livewire component discovery solution

## 2.3.8 - 2020-10-02

- Address Missing Mix Manifest Error (#317)

## 2.3.7 - 2020-09-06

- add loading state on share button (#309)
- compatibility fix for L8

## 2.3.6 - 2020-08-10

- possible security vulnerability: bump elliptic version (#300)
- possible XSS vulnerability: escape characters in stacktrace and exception title

## 2.3.5 - 2020-08-01

- catch exception in detectLineNumber for not existing blade files (#299)

## 2.3.4 - 2020-07-27

- fix an error that would throw a blank page when using third party extensions

## 2.3.3 -2020-07-14

- fix all psalm related issues

## 2.3.2 - 2020-07-14

- properly bind singleton (#291)

## 2.3.1 - 2020-07-13

- improve db name solution (#289)

## 2.3.0 - 2020-07-13

- allow override of Dumper via `$_SERVER variable` (#271)
- make DumpHandler instance manually in DumpRecorder (#286)
- only setup queues when queue is available (#287)

## 2.2.0 - 2020-07-13

- add `ignition:make:solution-provider` command

## 2.1.0 - 2020-07-13

- add "Undefined Property" solution (#264)

## 2.0.10 - 2020-07-13

- correctly detect dump location from ddd (#216)

## 2.0.9 - 2020-07-13

- use application contract instead of concrete class (#243)

## 2.0.8 - 2020-07-12

- do not render solution title tag for empty titles

## 2.0.7 - 2020-06-07

- Fix `DefaultDbNameSolutionProvider` (#277)

## 2.0.6 - 2020-06-01

- remove ability to fix variable names

## 2.0.5 - 2020-05-29

- blacklist certain variable names when fixing variable names

## 2.0.4 - 2020-05-18

- handle exceptions in case the request doesn't have a user (#274)

## 2.0.3 - 2020-04-07

- support Laravel 8

## 2.0.2 - 2020-03-18

- fix execute solution route not defined (#265)

## 2.0.0 - 2020-02-02

- adds support for Laravel 7
- drop support for Laravel 6 and below
- git information won't be collected by default anymore (if you need this set `collect_git_information` to `true` in the `flare` config file)
- `MissingPackageSolutionProvider` was added to the `ignored_solution_providers` because it potentially could be slow.

## 1.16.0 - 2020-01-21

- add named routes (#197)

## 1.15.0 - 2020-01-21

- add exception to the bottom of the html (#230)

## 1.14.0 - 2020-01-06

- add indicator that solution is running (#212)

## 1.13.1 - 2020-01-02

- Remove external reference for icons (#134)

## 1.13.0 - 2019-11-27

- Allow custom grouping types

## 1.12.1 - 2019-11-25

- Detect multibyte position offsets when adding linenumbers to the blade view - Fixes #193

## 1.12.0 - 2019-11-14

- Add exception to html (#206)
- Add a clear exception when passing no parameters to ddd (#205)
- Ignore JS tests (#215)
- Fix share report route bug

## 1.11.2 - 2019-10-13

- simplify default Laravel installation (#198)

## 1.11.1 - 2019-10-08

- add conditional line number (#182)

## 1.11.0 - 2019-10-08

- add better error messages for missing validation rules (#125)

## 1.10.0 - 2019-10-07

- Add `ignition:make-solution` command
- Add default for query binding option (Fixes #183)

## 1.9.2 - 2019-10-04

- Fix service provider registration (Fixes #177)

## 1.9.1 - 2019-10-01

- collapse vendor frames on windows fix (#176)

## 1.9.0 - 2019-09-27

- add ability to send logs to flare
- add `ddd` function

## 1.8.4 - 2019-09-27

- Resolve configuration from the injected app instead of the helper ([#168](https://github.com/facade/ignition/pull/168))

## 1.8.3 - 2019-09-25

- Remove `select-none` from error message
- Change line clamp behaviour for longer error messages

## 1.8.2 - 2019-09-20

- fix for `TypeError: Cannot set property 'highlightState' of undefined`

## 1.8.1 - 2019-09-20

- Revert javascript assets via URL - Fixes #161

## 1.8.0 - 2019-09-18

- added solution for running Laravel Dusk in production ([#121](https://github.com/facade/ignition/pull/121))
- Automatically fix blade variable typos and optional variables ([#38](https://github.com/facade/ignition/pull/38))

## 1.7.1 - 2019-09-18

- Use url helper to generate housekeeping endpoints

## 1.7.0 - 2019-09-18

- Add the ability to define a query collector max value ([#153](https://github.com/facade/ignition/pull/153))

## 1.6.10 - 2019-09-18

- fix `__invoke` method name in solution ([#151](https://github.com/facade/ignition/pull/151))

## 1.6.9 - 2019-09-18

- Add noscript trace information - fixes [#146](https://github.com/facade/ignition/issues/146)

## 1.6.8 - 2019-09-18

- Use javascript content type for asset response - fixes [#149](https://github.com/facade/ignition/issues/149)

## 1.6.7 - 2019-09-18

- Load javascript assets via URL. Fixes [#16](https://github.com/facade/ignition/issues/16)

## 1.6.6 - 2019-09-16

- Prevent undefined index exception in `TestCommand`

## 1.6.5 - 2019-09-13

- Ignore invalid characters in JSON encoding. Fixes [#138](https://github.com/facade/ignition/issues/138)

## 1.6.4 - 2019-09-13

- add no-index on error page

## 1.6.3 - 2019-09-12

- Fix `RouteNotDefinedSolutionProvider` in Laravel 5

## 1.6.2 - 2019-09-12

- updated publishing tag from default config

## 1.6.1 - 2019-09-12

- Resolve configuration from the injected application instead of the helper - Fixes [#131](https://github.com/facade/ignition/issues/131)

## 1.6.0 - 2019-09-09

- add `RouteNotDefined` solution provider ([#113](https://github.com/facade/ignition/pull/113))

## 1.5.0 - 2019-09-09

- suggest running migrations when a column is missing ([#83](https://github.com/facade/ignition/pull/83))

## 1.4.19 - 2019-09-09

- Remove quotation from git commit url ([#89](https://github.com/facade/ignition/pull/89))

## 1.4.18 - 2019-09-09

- Fix open_basedir restriction when looking up config file. Fixes ([#120](https://github.com/facade/ignition/pull/120))

## 1.4.17 - 2019-09-06

- Remove Inter, Operator from font stack. Fixes [#74](https://github.com/facade/ignition/issues/74)

## 1.4.15 - 2019-09-05

- Use previous exception trace for view exceptions. Fixes [#107](https://github.com/facade/ignition/issues/107)

## 1.4.14 - 2019-09-05

- Use DIRECTORY_SEPARATOR to fix an issue with blade view lookups in Windows

## 1.4.13 - 2019-09-05

- Use Laravel style comments

## 1.4.12 - 2019-09-04

- Use a middleware to protect ignition routes ([#93](https://github.com/facade/ignition/pull/93))

## 1.4.11 - 2019-09-04

- Use exception line number as fallbacks for view errors

## 1.4.10 - 2019-09-04

- Wrap solution provider lookup in a try-catch block

## 1.4.9 - 2019-09-04

- Lookup the first exception when linking to Telescope

## 1.4.8 - 2019-09-04

- pass an empty string to query if no connection name is available - fixes [#86](https://github.com/facade/ignition/issues/86)

## 1.4.7 - 2019-09-04

- Match whoops minimum version constraint with Laravel 6

## 1.4.6 - 2019-09-04

- Use empty array for default ignored solution providers

## 1.4.5 - 2019-09-03

- fix for new Laravel 6 installs

## 1.4.4 - 2019-09-03

- Suggest default database name in Laravel 6
- Add void return type to FlareHandler::write()

## 1.4.3 - 2019-09-03

- allow monolog v2

## 1.4.2 - 2019-09-03

- style fixes

## 1.4.1 - 2019-09-03

- Change `remote-sites-path` and `local-sites-path` config keys to us snake case

## 1.4.0 - 2019-09-03

- add `enable_runnable_solutions` key to config file

## 1.3.0 - 2019-09-02

- add `MergeConflictSolutionProvider`

## 1.2.0 - 2019-09-02

- add `ignored_solution_providers` key to config file

## 1.1.1 - 2019-09-02

- Fixed context tab crash when not using git ([#24](https://github.com/facade/ignition/issues/24))

## 1.1.0 - 2019-09-02

- Fixed an error that removed the ability to register custom blade directives.
- Fixed an error that prevented solution execution in Laravel 5.5 and 5.6
- The "Share" button can now be disabled in the configuration file
- Fixes an error when trying to log `null` values

## 1.0.4 - 2019-09-02

- Check if the authenticated user has a `toArray` method available, before collecting user data

## 1.0.3 - 2019-09-02

- Corrected invalid link in config file

## 1.0.2 - 2019-09-02

- Fixed an error in the `DefaultDbNameSolutionProvider` that could cause an infinite loop in Laravel < 5.6.28

## 1.0.1 - 2019-08-31

- add support for L5.5 & 5.6 ([#21](https://github.com/facade/ignition/pull/21))

## 1.0.0 - 2019-08-30

- initial release
