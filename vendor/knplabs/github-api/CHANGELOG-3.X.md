# Changelog

## 3.13.0

### Added
- Test against php 8.3 ([sergiy-petrov](https://github.com/sergiy-petrov)) [#1124](https://github.com/KnpLabs/php-github-api/issues/1124)
- feat: Secret Scanning Alerts ([haridarshan](https://github.com/haridarshan)) [#1114](https://github.com/KnpLabs/php-github-api/issues/1114)
- feat: User Migration ([haridarshan](https://github.com/haridarshan)) [#1115](https://github.com/KnpLabs/php-github-api/issues/1115)

### Changed
- General chores ([acrobat](https://github.com/acrobat)) [#1125](https://github.com/KnpLabs/php-github-api/issues/1125)

### Fixed
- Fix detection of secondary rate limit ([mathieudz](https://github.com/mathieudz)) [#1126](https://github.com/KnpLabs/php-github-api/issues/1126)

## 3.12.0

### Added
- feat: Support for Organization Runners ([haridarshan](https://github.com/haridarshan), [renovate](https://github.com/renovate)[[bot](https://github.com/bot)]) [#1101](https://github.com/KnpLabs/php-github-api/issues/1101)
- allow psr/http-message v2 ([LordSimal](https://github.com/LordSimal)) [#1122](https://github.com/KnpLabs/php-github-api/issues/1122)

### Changed
- Fixed branch alias ([GrahamCampbell](https://github.com/GrahamCampbell)) [#1109](https://github.com/KnpLabs/php-github-api/issues/1109)

## 3.11.0

### Added
- Added environment variables & secrets ([Froxz](https://github.com/Froxz)) [#1104](https://github.com/KnpLabs/php-github-api/issues/1104)
- Added Org & Repository variables ([Froxz](https://github.com/Froxz)) [#1106](https://github.com/KnpLabs/php-github-api/issues/1106)
- Deployment branch policies ([Froxz](https://github.com/Froxz)) [#1108](https://github.com/KnpLabs/php-github-api/issues/1108)

### Changed
- Test on PHP 8.2 ([GrahamCampbell](https://github.com/GrahamCampbell)) [#1105](https://github.com/KnpLabs/php-github-api/issues/1105)

### Fixed
- Bugfix creating env ([Froxz](https://github.com/Froxz)) [#1107](https://github.com/KnpLabs/php-github-api/issues/1107)

## 3.10.0

### Added
- Add vulnerability alerts endpoints ([andreia](https://github.com/andreia)) [#1096](https://github.com/KnpLabs/php-github-api/issues/1096)
- Added environments ([Froxz](https://github.com/Froxz)) [#1103](https://github.com/KnpLabs/php-github-api/issues/1103)

### Changed
- Create authorization removed from docs ([rafasashi](https://github.com/rafasashi)) [#1090](https://github.com/KnpLabs/php-github-api/issues/1090)
- Setup dependabot for github action workflows ([acrobat](https://github.com/acrobat)) [#1098](https://github.com/KnpLabs/php-github-api/issues/1098)
- Bump actions/checkout from 2 to 3 ([dependabot](https://github.com/dependabot)[[bot](https://github.com/bot)]) [#1099](https://github.com/KnpLabs/php-github-api/issues/1099)
- Bump ramsey/composer-install from 1 to 2 ([dependabot](https://github.com/dependabot)[[bot](https://github.com/bot)]) [#1100](https://github.com/KnpLabs/php-github-api/issues/1100)

## 3.9.0

### Added
- Add the ability to download raw file, needed when size > 1MB ([genintho](https://github.com/genintho)) [#1075](https://github.com/KnpLabs/php-github-api/issues/1075)
- Feat: Support new Team Repositories Endpoint ([iBotPeaches](https://github.com/iBotPeaches)) [#1082](https://github.com/KnpLabs/php-github-api/issues/1082)
- App: add hook endpoints ([glaubinix](https://github.com/glaubinix)) [#1086](https://github.com/KnpLabs/php-github-api/issues/1086)
- Add sync a fork branch with the upstream repository  ([DAGpro](https://github.com/DAGpro)) [#1084](https://github.com/KnpLabs/php-github-api/issues/1084)

### Changed
- Fix return types in phpdoc for `Assignees` and `ReviewRequest` ([rob006](https://github.com/rob006)) [#1078](https://github.com/KnpLabs/php-github-api/issues/1078)
- Fixed several typos and grammar errors ([AndrewDawes](https://github.com/AndrewDawes)) [#1088](https://github.com/KnpLabs/php-github-api/issues/1088)

## 3.8.0

### Added
- API rate limit error status can be 403 ([matthewnessworthy](https://github.com/matthewnessworthy)) [#1072](https://github.com/KnpLabs/php-github-api/issues/1072)
- Add method to use generate release notes endpoint ([GuySartorelli](https://github.com/GuySartorelli)) [#1074](https://github.com/KnpLabs/php-github-api/issues/1074)
- Fix typehint for repository dispatch method ([cweagans](https://github.com/cweagans)) [#1066](https://github.com/KnpLabs/php-github-api/issues/1066)

### Changed
- Update security.md ([secalith-code](https://github.com/secalith-code)) [#1076](https://github.com/KnpLabs/php-github-api/issues/1076)

### Fixed
- dont require encoding ([gemal](https://github.com/gemal)) [#1071](https://github.com/KnpLabs/php-github-api/issues/1071)

## 3.7.0

### Added
- added phpdocs ([staabm](https://github.com/staabm)) [#1068](https://github.com/KnpLabs/php-github-api/issues/1068)
- added missing magic method phpdocs for deployments ([staabm](https://github.com/staabm)) [#1069](https://github.com/KnpLabs/php-github-api/issues/1069)
- Fix issue https://github.com/KnpLabs/php-github-api/issues/1061 ([mruell](https://github.com/mruell)) [#1062](https://github.com/KnpLabs/php-github-api/issues/1062)

### Changed
- Updates ReviewRequest Create method to return type Array ([ejntaylor](https://github.com/ejntaylor)) [#1060](https://github.com/KnpLabs/php-github-api/issues/1060)

## 3.6.0

### Added
- Include optional params parameter for Commits compare method ([mountiny](https://github.com/mountiny)) [#1053](https://github.com/KnpLabs/php-github-api/issues/1053)

### Changed
- Update incorrect documentation ([pheeque1](https://github.com/pheeque1)) [#1058](https://github.com/KnpLabs/php-github-api/issues/1058)

### Fixed
- fix(Apps): use /orgs/ORG/installation ([ellisio](https://github.com/ellisio)) [#1056](https://github.com/KnpLabs/php-github-api/issues/1056)

## 3.5.1

### Fixed
- Boolean private needed to create private repo! ([mruell](https://github.com/mruell)) [#1051](https://github.com/KnpLabs/php-github-api/issues/1051)

## 3.5.0

### Added
- added support for psr\cache 3.0  ([rconfig](https://github.com/rconfig)) [#1046](https://github.com/KnpLabs/php-github-api/issues/1046)
- Symfony: allow deprecation-contracts version 3 ([glaubinix](https://github.com/glaubinix)) [#1049](https://github.com/KnpLabs/php-github-api/issues/1049)

### Changed
- Fix internal doc link ([staudenmeir](https://github.com/staudenmeir)) [#1044](https://github.com/KnpLabs/php-github-api/issues/1044)

### Fixed
- Fix Client URL Prepending For GraphQL Endpoint on Enterprise ([asher-goldberg](https://github.com/asher-goldberg), [acrobat](https://github.com/acrobat)) [#1048](https://github.com/KnpLabs/php-github-api/issues/1048)

## 3.4.0

### Added
- Add create a repository using a template endpoint ([martinbean](https://github.com/martinbean)) [#994](https://github.com/KnpLabs/php-github-api/issues/994)
- Allow fetching repo readme for a specific ref ([bery](https://github.com/bery)) [#1019](https://github.com/KnpLabs/php-github-api/issues/1019)
- allow assigning role to organisation members ([luceos](https://github.com/luceos)) [#1018](https://github.com/KnpLabs/php-github-api/issues/1018)
- Branch lists . ( ? query  per_page) ([pitonic](https://github.com/pitonic)) [#1020](https://github.com/KnpLabs/php-github-api/issues/1020)
- Php8.1 support ([acrobat](https://github.com/acrobat)) [#1025](https://github.com/KnpLabs/php-github-api/issues/1025)
- Allow psr/cache 2.0 as well as 1.0 ([johnnoel](https://github.com/johnnoel)) [#1029](https://github.com/KnpLabs/php-github-api/issues/1029)
- adding code_with_match (#1024) ([QuentinRa](https://github.com/QuentinRa)) [#1031](https://github.com/KnpLabs/php-github-api/issues/1031)
- Added dir parameter for Repo readme ([AlexandrePavy](https://github.com/AlexandrePavy)) [#1032](https://github.com/KnpLabs/php-github-api/issues/1032)
- refs #955: deprecate Client::AUTH_* constants and replace them with AuthMethod::AUTH_* const ([ipalo](https://github.com/ipalo)) [#1036](https://github.com/KnpLabs/php-github-api/issues/1036)
- feat: Add `visibility` option to repo create ([gerdemann](https://github.com/gerdemann)) [#1038](https://github.com/KnpLabs/php-github-api/issues/1038)
- Feature get authenticated app ([kdaniel95](https://github.com/kdaniel95)) [#1041](https://github.com/KnpLabs/php-github-api/issues/1041)

### Changed
- Fix up typos ([dereuromark](https://github.com/dereuromark)) [#1011](https://github.com/KnpLabs/php-github-api/issues/1011)
- Update integration authentication documentation for usage with lcobucci/jwt ^4 ([glaubinix](https://github.com/glaubinix)) [#1017](https://github.com/KnpLabs/php-github-api/issues/1017)
- Update result_pager.md ([tomsowerby](https://github.com/tomsowerby)) [#1023](https://github.com/KnpLabs/php-github-api/issues/1023)
- fix(doc): links to doc in CurrentUser class ([Nek-](https://github.com/Nek-)) [#1026](https://github.com/KnpLabs/php-github-api/issues/1026)
- Fix incorrect phpdoc ([gemal](https://github.com/gemal)) [#1034](https://github.com/KnpLabs/php-github-api/issues/1034)

### Fixed
- Add accept header for creating repo from template ([davidpeach](https://github.com/davidpeach)) [#1030](https://github.com/KnpLabs/php-github-api/issues/1030)

## 3.3.0

### Added
- Allow costume accept headers for GraphQL Endpoint. ([Necmttn](https://github.com/Necmttn)) [#1001](https://github.com/KnpLabs/php-github-api/issues/1001)
- Add endpoint for approve workflow run ([Nyholm](https://github.com/Nyholm)) [#1006](https://github.com/KnpLabs/php-github-api/issues/1006)

### Changed
- Update readme and add example for different http client usage ([acrobat](https://github.com/acrobat)) [#1002](https://github.com/KnpLabs/php-github-api/issues/1002)
- Bumped branch alias after new feature merged ([GrahamCampbell](https://github.com/GrahamCampbell)) [#1004](https://github.com/KnpLabs/php-github-api/issues/1004)
- Add comment on AbstractApi::$perPage() ([Nyholm](https://github.com/Nyholm)) [#1007](https://github.com/KnpLabs/php-github-api/issues/1007)

### Fixed
- Fix publicKey ([Yurunsoft](https://github.com/Yurunsoft)) [#1005](https://github.com/KnpLabs/php-github-api/issues/1005)

## 3.2.0

### Added
- Deprecate ResultPager::postFetch method ([acrobat](https://github.com/acrobat)) [#986](https://github.com/KnpLabs/php-github-api/issues/986)
- Add deprecations to the PR review methods to allow cleanup ([acrobat](https://github.com/acrobat)) [#984](https://github.com/KnpLabs/php-github-api/issues/984)
- Allow binary content downloads of assets ([acrobat](https://github.com/acrobat)) [#990](https://github.com/KnpLabs/php-github-api/issues/990)
- Deployments: added missing 'delete deployment' endpoint ([clxmstaab](https://github.com/clxmstaab)) [#991](https://github.com/KnpLabs/php-github-api/issues/991)
- Events list per authenticated user for all repos ([richard015ar](https://github.com/richard015ar)) [#1000](https://github.com/KnpLabs/php-github-api/issues/1000)

### Changed
- Fixed branch alias ([GrahamCampbell](https://github.com/GrahamCampbell)) [#975](https://github.com/KnpLabs/php-github-api/issues/975)
- fix typo ([staabm](https://github.com/staabm)) [#977](https://github.com/KnpLabs/php-github-api/issues/977)
- Improved bc check ([acrobat](https://github.com/acrobat)) [#982](https://github.com/KnpLabs/php-github-api/issues/982)
- Correctly link to github actions docs and fix backlinks ([acrobat](https://github.com/acrobat)) [#983](https://github.com/KnpLabs/php-github-api/issues/983)
- Add missing repo hooks documentation ([acrobat](https://github.com/acrobat)) [#987](https://github.com/KnpLabs/php-github-api/issues/987)
- Fix incorrect public key documentation ([acrobat](https://github.com/acrobat)) [#988](https://github.com/KnpLabs/php-github-api/issues/988)
- Fixed incorrect parameters in apps docs ([acrobat](https://github.com/acrobat)) [#989](https://github.com/KnpLabs/php-github-api/issues/989)
- phpdoc: fix typo ([clxmstaab](https://github.com/clxmstaab)) [#993](https://github.com/KnpLabs/php-github-api/issues/993)
- Fix upmerged usage of deprecated phpunit assert ([acrobat](https://github.com/acrobat)) [#995](https://github.com/KnpLabs/php-github-api/issues/995)
- Fix typo ([romainneutron](https://github.com/romainneutron)) [#997](https://github.com/KnpLabs/php-github-api/issues/997)

### Fixed
- Deployments: use proper media-type for in_progress/queued, inactive state ([staabm](https://github.com/staabm)) [#979](https://github.com/KnpLabs/php-github-api/issues/979)
- [952] doc - Specify lcobucci/jwt version, fix deprecation ([amacrobert-meq](https://github.com/amacrobert-meq), [acrobat](https://github.com/acrobat)) [#953](https://github.com/KnpLabs/php-github-api/issues/953)
- Replace deprecated organization team repository add/remove urls ([acrobat](https://github.com/acrobat)) [#985](https://github.com/KnpLabs/php-github-api/issues/985)
- fixed php warning in GithubExceptionThrower ([clxmstaab](https://github.com/clxmstaab), [acrobat](https://github.com/acrobat)) [#992](https://github.com/KnpLabs/php-github-api/issues/992)

## 3.1.0

### Added
- Add workflow dispatch and allow workflow names. ([fodinabor](https://github.com/fodinabor)) [#969](https://github.com/KnpLabs/php-github-api/issues/969)

### Changed
- Re-enable roave bc check for 3.x ([acrobat](https://github.com/acrobat)) [#958](https://github.com/KnpLabs/php-github-api/issues/958)
- Cleanup 3.0.0 changelog ([acrobat](https://github.com/acrobat)) [#957](https://github.com/KnpLabs/php-github-api/issues/957)
- Update new GitHub doc links in repo. ([fodinabor](https://github.com/fodinabor)) [#974](https://github.com/KnpLabs/php-github-api/issues/974)

### Fixed
- Add accept header for the checks API ([Agares](https://github.com/Agares)) [#968](https://github.com/KnpLabs/php-github-api/issues/968)
- ExceptionThrower: adjust rate limit detection ([glaubinix](https://github.com/glaubinix)) [#959](https://github.com/KnpLabs/php-github-api/issues/959)

## 3.0.0

### Added
- Switch to PSR18 client implementation and bump httplug minimum version to ^2.0 ([GrahamCampbell](https://github.com/GrahamCampbell)) [#885](https://github.com/KnpLabs/php-github-api/issues/885)
- Switch to PSR-17 and remove deprecated code ([GrahamCampbell](https://github.com/GrahamCampbell)) [#888](https://github.com/KnpLabs/php-github-api/issues/888)
- Allow PHP8 ([acrobat](https://github.com/acrobat)) [#934](https://github.com/KnpLabs/php-github-api/issues/934)
- [3.x] Make PHP 7.2.5 the minimum version ([GrahamCampbell](https://github.com/GrahamCampbell)) [#942](https://github.com/KnpLabs/php-github-api/issues/942)
- [3.x] Re-worked pagination to not mutate the api classes ([GrahamCampbell](https://github.com/GrahamCampbell)) [#907](https://github.com/KnpLabs/php-github-api/issues/907) & ([acrobat](https://github.com/acrobat)) [#956](https://github.com/KnpLabs/php-github-api/issues/956)
- Prepare 3.0 release and remove remaining deprecated code ([acrobat](https://github.com/acrobat)) [#948](https://github.com/KnpLabs/php-github-api/issues/948)

### Changed
- Remove BC check on 3.x ([GrahamCampbell](https://github.com/GrahamCampbell)) [#900](https://github.com/KnpLabs/php-github-api/issues/900)
- [3.x] Fix the HTTP methods client ([GrahamCampbell](https://github.com/GrahamCampbell)) [#910](https://github.com/KnpLabs/php-github-api/issues/910)
- fix typo ([michielkempen](https://github.com/michielkempen)) [#920](https://github.com/KnpLabs/php-github-api/issues/920)
- [3.x] Added some additional scalar types and return types ([GrahamCampbell](https://github.com/GrahamCampbell)) [#949](https://github.com/KnpLabs/php-github-api/issues/949)
