# Changelog

All notable changes to `flare-client-php` will be documented in this file

## 1.9.1 - 2021-09-13

- let `report` return the created report

## 1.9.0 - 2021-09-13

- add report tracking uuid

## 1.8.1 - 2021-05-31

- improve compatibility with Symfony 5.3

## 1.8.0 - 2021-04-30

- add ability to ignore errors and exceptions (#23)
- fix curl parameters

## 1.7.0 - 2021-04-12

- use new Flare endpoint and allow 1 redirect to it
 
## 1.6.1 - 2021-04-08

- make `censorRequestBodyFields` chainable

## 1.6.0 - 2021-04-08

- add ability to censor request body fields (#18)

## 1.5.0 - 2021-03-31

- add `determineVersionUsing`

## 1.4.0 - 2021-02-16

- remove custom grouping

## 1.3.7 - 2020-10-21

- allow PHP 8

## 1.3.6 - 2020-09-18

- remove `larapack/dd` (#15)

## 1.3.5 - 2020-08-26

- allow Laravel 8 (#13)

## 1.3.4 - 2020-07-14

- use directory separator constant

## 1.3.3 - 2020-07-14

- fix tests by requiring symfony/mime
- display real exception class for view errors (see https://github.com/facade/ignition/discussions/237)

## 1.3.2 - 2020-03-02

- allow L7

## 1.3.1 - 2019-12-15

- allow var-dumper v5.0

## 1.3.0 - 2019-11-27

- Allow custom grouping types

## 1.2.1 - 2019-11-19

- Let `registerFlareHandlers` return $this

## 1.2.0 - 2019-11-19

- Add `registerFlareHandlers` method to register error and exception handlers in non-Laravel applications
- Fix get requests with query parameters (#4)

## 1.1.2 - 2019-11-08

- Ignore invalid mime type detection issues

## 1.1.1 - 2019-10-07

- Wrap filesize detection in try-catch block

## 1.1.0 - 2019-09-27

- Add ability to log messages

## 1.0.4 - 2019-09-11

- Fixes an issue when sending exceptions inside a queue worker

## 1.0.3 - 2019-09-05

- Ensure valid session data

## 1.0.2 - 2019-09-05

- Fix error when uploading multiple files using an array name

## 1.0.1 - 2019-09-02

- Fix issue with uploaded files in request context

## 1.0.0 - 2019-08-30

- initial release
