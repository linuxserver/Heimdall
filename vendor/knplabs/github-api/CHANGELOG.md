# Changelog

## 2.20.0

### Added
- Deployments: added missing 'delete deployment' endpoint ([clxmstaab](https://github.com/clxmstaab)) [#991](https://github.com/KnpLabs/php-github-api/issues/991)

### Changed
- phpdoc: fix typo ([clxmstaab](https://github.com/clxmstaab)) [#993](https://github.com/KnpLabs/php-github-api/issues/993)

### Fixed
- fixed php warning in GithubExceptionThrower ([clxmstaab](https://github.com/clxmstaab), [acrobat](https://github.com/acrobat)) [#992](https://github.com/KnpLabs/php-github-api/issues/992)

## 2.19.2

### Changed
- Improved bc check ([acrobat](https://github.com/acrobat)) [#982](https://github.com/KnpLabs/php-github-api/issues/982)
- Correctly link to github actions docs and fix backlinks ([acrobat](https://github.com/acrobat)) [#983](https://github.com/KnpLabs/php-github-api/issues/983)
- Add missing repo hooks documentation ([acrobat](https://github.com/acrobat)) [#987](https://github.com/KnpLabs/php-github-api/issues/987)
- Fix incorrect public key documentation ([acrobat](https://github.com/acrobat)) [#988](https://github.com/KnpLabs/php-github-api/issues/988)
- Fixed incorrect parameters in apps docs ([acrobat](https://github.com/acrobat)) [#989](https://github.com/KnpLabs/php-github-api/issues/989)

### Fixed
- Deployments: use proper media-type for in_progress/queued, inactive state ([staabm](https://github.com/staabm)) [#979](https://github.com/KnpLabs/php-github-api/issues/979)
- backported #979 into 2.x ([staabm](https://github.com/staabm)) [#981](https://github.com/KnpLabs/php-github-api/issues/981)
- [952] doc - Specify lcobucci/jwt version, fix deprecation ([amacrobert-meq](https://github.com/amacrobert-meq), [acrobat](https://github.com/acrobat)) [#953](https://github.com/KnpLabs/php-github-api/issues/953)
- Replace deprecated organization team repository add/remove urls ([acrobat](https://github.com/acrobat)) [#985](https://github.com/KnpLabs/php-github-api/issues/985)

## 2.19.1

### Fixed
- ExceptionThrower: adjust rate limit detection ([glaubinix](https://github.com/glaubinix)) [#959](https://github.com/KnpLabs/php-github-api/issues/959)

## 2.19.0

### Added
- Mark some classes as final ([acrobat](https://github.com/acrobat)) [#954](https://github.com/KnpLabs/php-github-api/issues/954)

## 2.18.0

### Added
- Add parameters to PullRequest commits method ([seanmtaylor](https://github.com/seanmtaylor)) [#938](https://github.com/KnpLabs/php-github-api/issues/938)
- Actions (#872) ([lexor](https://github.com/lexor)) [#939](https://github.com/KnpLabs/php-github-api/issues/939)
- automated security endpoints (#868) ([lexor](https://github.com/lexor)) [#944](https://github.com/KnpLabs/php-github-api/issues/944)

### Changed
- Update apps.md ([clarkeash](https://github.com/clarkeash)) [#936](https://github.com/KnpLabs/php-github-api/issues/936)

### Fixed
- Throw exception for graphql errors ([acrobat](https://github.com/acrobat)) [#941](https://github.com/KnpLabs/php-github-api/issues/941)

## 2.17.0

### Added

- Improve checks api implementation ([acrobat](https://github.com/acrobat)) [#932](https://github.com/KnpLabs/php-github-api/issues/932)

### Changed
- Missing auth method in list of omitted passwords. ([tobyS](https://github.com/tobyS)) [#933](https://github.com/KnpLabs/php-github-api/issues/933)
- Improve github actions setup ([acrobat](https://github.com/acrobat)) [#935](https://github.com/KnpLabs/php-github-api/issues/935)

## 2.16.0

### Added
- Add support for SSO errors coming from the API ([eiriksm](https://github.com/eiriksm)) [#913](https://github.com/KnpLabs/php-github-api/issues/913)
- Add OutsideCollaborators api ([Matth--](https://github.com/Matth--)) [#925](https://github.com/KnpLabs/php-github-api/issues/925)
- Add support for creating a repo dispatch event ([Nyholm](https://github.com/Nyholm)) [#931](https://github.com/KnpLabs/php-github-api/issues/931)

### Changed
- Fix: Wrong PHPDoc description ([OskarStark](https://github.com/OskarStark)) [#922](https://github.com/KnpLabs/php-github-api/issues/922)
- Adding GitHub authentication to GraphQL documentation ([legionth](https://github.com/legionth)) [#927](https://github.com/KnpLabs/php-github-api/issues/927)

### Fixed
- Use RFC3986 for building URI query strings ([GrahamCampbell](https://github.com/GrahamCampbell)) [#908](https://github.com/KnpLabs/php-github-api/issues/908)
- Fix call to test a webhook method ([morrislaptop](https://github.com/morrislaptop)) [#915](https://github.com/KnpLabs/php-github-api/issues/915)

## 2.15.0

### Added
- Prepare deprecation of authentication methods ([acrobat](https://github.com/acrobat)) [#870](https://github.com/KnpLabs/php-github-api/issues/870)
- Add Support For GitData Reference Matching Methods ([nickpoulos](https://github.com/nickpoulos)) [#875](https://github.com/KnpLabs/php-github-api/issues/875)
- Make invalid request error more clear ([acrobat](https://github.com/acrobat)) [#880](https://github.com/KnpLabs/php-github-api/issues/880)
- Show user by ID ([genintho](https://github.com/genintho)) [#894](https://github.com/KnpLabs/php-github-api/issues/894)
- add additional check run methods ([bobeagan](https://github.com/bobeagan), [acrobat](https://github.com/acrobat)) [#865](https://github.com/KnpLabs/php-github-api/issues/865)

### Changed
- Added phpstan ([clxkoders](https://github.com/clxkoders)) [#871](https://github.com/KnpLabs/php-github-api/issues/871)
- Increase phpstan to level 4 ([acrobat](https://github.com/acrobat)) [#874](https://github.com/KnpLabs/php-github-api/issues/874)
- [Documentation] Add Missing Children to Pull Request Index ([jimlind](https://github.com/jimlind)) [#877](https://github.com/KnpLabs/php-github-api/issues/877)
- [Documentation] Include links to Pull Request Children on Pull Request Doc. ([jimlind](https://github.com/jimlind)) [#878](https://github.com/KnpLabs/php-github-api/issues/878)
- Fix typo in /lib/Github/Api/RateLimit.php ([yoonper](https://github.com/yoonper)) [#886](https://github.com/KnpLabs/php-github-api/issues/886)
- Don't use deprecated auth in examples ([GrahamCampbell](https://github.com/GrahamCampbell)) [#892](https://github.com/KnpLabs/php-github-api/issues/892)
- Removed shadow-cat ([GrahamCampbell](https://github.com/GrahamCampbell)) [#893](https://github.com/KnpLabs/php-github-api/issues/893)
- Don't urlencode integer values ([acrobat](https://github.com/acrobat)) [#895](https://github.com/KnpLabs/php-github-api/issues/895)
- phpstan level 6 fixes ([acrobat](https://github.com/acrobat), [GrahamCampbell](https://github.com/GrahamCampbell)) [#897](https://github.com/KnpLabs/php-github-api/issues/897)

### Fixed
- fix: use new media type for branch protections ([iBotPeaches](https://github.com/iBotPeaches)) [#881](https://github.com/KnpLabs/php-github-api/issues/881)
- Added missing 'machine-man-preview' accept headers ([vovayatsyuk](https://github.com/vovayatsyuk)) [#883](https://github.com/KnpLabs/php-github-api/issues/883)
- Fix broken roave/bc-check test ([acrobat](https://github.com/acrobat)) [#890](https://github.com/KnpLabs/php-github-api/issues/890)
- Fixed incorrect MissingArgumentException parent constructor values ([acrobat](https://github.com/acrobat)) [#896](https://github.com/KnpLabs/php-github-api/issues/896)
- Added AUTH_ACCESS_TOKEN to allowed methods ([GrahamCampbell](https://github.com/GrahamCampbell)) [#899](https://github.com/KnpLabs/php-github-api/issues/899)

## 2.14.0

### Added
- Replace deprecated Organization\Teams api calls ([lolos](https://github.com/lolos)) [#860](https://github.com/KnpLabs/php-github-api/issues/860)
- Add sort and direction for fetching organizations repos ([pgrimaud](https://github.com/pgrimaud)) [#863](https://github.com/KnpLabs/php-github-api/issues/863)
- Added parameters to Repo/milestones() method ([dereuromark](https://github.com/dereuromark)) [#856](https://github.com/KnpLabs/php-github-api/issues/856)

### Fixed 

- Remove incorrect MissingArgumentException in Labels api ([bobeagan](https://github.com/bobeagan)) [#861](https://github.com/KnpLabs/php-github-api/issues/861)

### Changed
- Fix typos in test/Github/Tests/Api/RepoTest.php ([pgrimaud](https://github.com/pgrimaud)) [#862](https://github.com/KnpLabs/php-github-api/issues/862)
- further detail on ResultPager parameters ([sepiariver](https://github.com/sepiariver)) [#843](https://github.com/KnpLabs/php-github-api/issues/843)
- fix phpdoc in labels api ([staabm](https://github.com/staabm)) [#854](https://github.com/KnpLabs/php-github-api/issues/854)
- fix api link in Issue\Labels::add() phpdoc ([staabm](https://github.com/staabm)) [#853](https://github.com/KnpLabs/php-github-api/issues/853)

## 2.13.0

### Added

- Support the new authorizations API
- Repo community profile API endpoint
- Support for draft PRs
- Missing Apps endpoints
- Test against php 7.4

### Changed

- Allow create & remove to set and remove requests for teams

## 2.12.1

### Fixed

- Fixed bug in handling of validation errors
- Updated docs to not use deprected string parameter in issue assignee call

## 2.12.0

### Added

- Support for HTTPlug 2.0 and PSR-18
- Add support for GitHub Checks
- Add support for GitHub Pages
- Add support to update a Pull Request Review
- Add support to get specific revision of a gist
- Added a 4th argument `$parameters` to `PullRequest::files()`
- Added `Accept` headers to Github Apps 

### Removed

- Active support for HHVM
- Support for PHP <7.1

### Changed

- Allow tags to be created without the `Tagger` object
- When updating DeployKeys we will first remove existing then add a new DeployKey

### Fixed

- In `Trees` we should check if `array_key_exists('sha', $tree)` instead of `isset` to avoid issues with `null`.  (#822)

### Deprecated

- Passing an integer (`$page`) as 4th arugment in `Comments::all()` is deprecated. It should be an array.

## 2.11.0

### Added

- Support for Miscellaneous Licenses (#744)
- Structured Limit objects for rate limiting API (#733)
- Support for getting star creation timestamps in activity/starring endpoint (#729)

### Fixed

- Added missing has_projects parameter to repo create (#752)
- Proper type hinting for magic methods (#758)
- Allow symlink to files to be downloaded (#751)

### Changed

- Fix of PHP version in readme (#747)
- Fix documentation to get release for a tag (#755)
- Declare all used properties in RateLimitResource class (#762)
- Set correct property and return types (#764)
- Fixed install docs broken after 2.0 release of httplug lib (#767)

## 2.10.1

### Fixed

- Convert the assignee parameter to array to avoid getting a 422 error on github (#738)
- Fix GraphQL test warnings when they do not assert anything (#735)

### Changed

- Check for BC breaks during the travis build (#734)

## 2.10.0

### Added

- Support for "before" parameter on Notification API (#724)

### Changed

- Allow unspecified `event` when creating review (#723)

### Fixed

- Adjust: installationn access token endpoint (#731)
- Fixed "get single label" example and add correct example for getting issue's labels (#732)
- Add comment about `Key` constructor argument (#722)

## 2.9.0

### Added

- API endpoint `Github\Api\Repo::transfer()`
- API endpoint `Github\Api\Notification::markThreadRead()`
- API endpoint `Github\Api\Search::topics()`

### Fixed

- Make sure to always reset the "per page" in `Github\ResultPager::fetchAll()`.

## 2.8.0

### Added

- Allow our HTTP plugins to show up in the Symfony web profiler page. (#687)
- Repository documentation to current user (#671)
- Add collaborator permission call (#678)
- Add missing parameters for User/CurrentUser Repositories (#684)
- Pimp the readme with badge poser (#686)

### Fixed

- Typo in assignee documentation
- Missing use statement in security example
- Fixed phpdoc typo (#695)
- Replace use of deprecated api to the correct one in the security docs (#697)

### Changed

- Updated requirements in readme (#689)

## 2.7.0

### Added

- Phpunit 6 compatibility
- `Github\Api\AbstractApi::setPage()` to allow you to set the page on all endpoints. 
- Support for query parameters and request headers on `Github\Api\User::following` and `Github\Api\User::followers` 
- API endpoint `Github\Api\CurrentUser\Emails::allPublic()`
- API endpoint `Github\Api\Search::commits()`
- API endpoint `Github\Api\Miscellaneous\CodeOfConduct`
- API endpoint `Github\Api\Repo::topics()`
- API endpoint `Github\Api\Repo::replaceTopics()`

### Fixed

- Fixed bug in `PathPrepend` plugin where "api/vX" could be duplicated.

### Changed

- Improved documentation and doc blocks

### Removed

- Dropped support for php 5.5

### Deprecated

The following endpoints were deprecated by Github and are also deprecated in the client: 

- `Github\Api\Repo::find()`
- `Github\Api\User::find()`
- `Github\Api\Issue::find()`

## 2.6.0

### Added

- Support for graphql api [variables](https://developer.github.com/v4/guides/forming-calls/#working-with-variables) (#612)
- Added missing branch protection methods (#616)
- Helper function `fromFile ` to get GraphQL queries from a file (#628)
- Extra parameter `params` to collaborators api calls (#623)
- Documentation for GitData API (#613)

### Fixed
- Remove `body` as a required parameter when creating an issue (#624)
- Minor fixes in example code (#617)

## 2.5.0

### Added

- Stable support for graphql api (V4) (#593)
- Stable support for apps (previously integrations) (#592)
- `Repo::events()`

### Fixed

- Incorrect link in repository search docs (#594)
- Added the required parameter `$message` on `Review::dismiss`.

## 2.4.0

### Added

- `Integrations::configure` to allow accessing early access program endpoints.
- Add support for pagination and parameters in the pull request comments
- Add the ability to fetch user installations (`CurrentUser::installations`)
- Allow getting repo info by id (`Repo::showById`)
- Allow fetching repositories for a specific installation and user (`CurrentUser::repositoriesByInstallation`)

### Changed

- `PullRequest\Review` and `PullRequest\ReviewRequest` is now part of the official API. No need to call `configure`.

## 2.3.0

### Fixed

- Issue where we serve the wrong cached response. We vary on authorization header now.

### Added

- `PullRequest::status`
- Throw InvalidArgumentException on `PullRequest::merge` when wrong merge method is used.
- Added `Protection::configure`

### Changed

- First argument to `Integrations::listRepositories()` is now optional.
- Moved tests from "functional" to "integration"

## 2.2.0

### Added

- API support for Pull Request Review Requests.
- API support for Traffic.
- API support for issue Assignees.
- API support for Miscellaneous Gitignore and Emojis.
- Added endpoints for issue lock, unlock and issue label show.
- Added more parameters to `User::starred`.
- Fluid interface by allowing `configure()` to return `$this`.
- `configure()` support for issues API.

### Fixed

- Cache issue where some requests are not cached
- Issue with `User::all()` creates a query with double question marks.

## 2.1.0

### Added

- Add support for retrieving a single notification info using his ID
- Add a function to get user organizations
- Added GraphQL support
- Add page variable to organization repo list (Organization::repositories())
- Add support for pull request review.
- Add support for adding branch protection.

### Fixed

- Bug with double slashes when using enterprise URL.
- Bug when headers not being passed to request (#529)

## 2.0.0

### Added

- Support for JWT authentication
- API for Organization\Members
- API for Integrations
- API for Repo\Cards
- API for Repo\Columns
- API for Repo\Projects
- API for User\MyRepositories
- Methods in Repo API for frequency and participation

### Changed

- `ApiLimitExceedException::__construct` has a new second parameter for the remaining API calls.
- First parameter of `Github\Client` has changed type from `\Http\Client\HttpClient` to
`Github\HttpClient\Builder`. A factory class was also added. To upgrade you need to change:

```php
// Old way does not work:
$github = new Github\Client($httpClient);

// New way will work:
$github = new Github\Client(new Github\HttpClient\Builder($httpClient));
$github = Github\Client::createWithHttpClient($httpClient);
```
- Renamed the currentuser `DeployKeys` api class to `PublicKeys` to reflect to github api name.

## 2.0.0-rc4

### Added

- HTTPlug to decouple from Guzzle
- `Github\Client::getLastResponse` was added
- Support for PSR-6 cache
- `Github\Client::addPlugin` and `Github\Client::removePlugin`
- `Github\Client::getApiVersion`
- `Github\Client::removeCache`

### Changed

- Uses of `Github\HttpClient\HttpClientInterface` is replaced by `Http\Client\HttpClient` ie the constructor of `Github\Client`.
- We use PSR-7's representation of HTTP message instead of `Guzzle\Http\Message\Response` and `Guzzle\Http\Message\Request`.
- `Github\Client::addHeaders` was added instead of `Github\Client::setHeaders`
- Signature of `Github\Client::useCache` has changed. First argument must be a `CacheItemPoolInterface`
- We use PSR-4 instead of PSR-0

### Removed

- Support for PHP 5.3 and 5.4
- `Github/HttpClient/HttpClientInterface` was removed
- `Github/HttpClient/HttpClient` was removed
-  All classes in `Github/HttpClient/HttpClient/Listener/*` were removed
- `Github/HttpClient/CachedHttpClient` was removed
-  All classes in `Github/HttpClient/Cache/*` were removed

## 1.7.1

No change log before this version
